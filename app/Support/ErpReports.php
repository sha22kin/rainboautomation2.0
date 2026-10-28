<?php

namespace App\Support;

use App\Models\Erp\Attendance;
use App\Models\Erp\Batch;
use App\Models\Erp\Certificate;
use App\Models\Erp\Expense;
use App\Models\Erp\Payment;
use App\Models\Erp\Student;
use App\Models\Erp\Trainer;

// Report Center (Reports & Analytics): every report is a table of rows, the first row being the headers.
// The same rows feed Print, Excel and CSV. Reports marked "dated" follow the From / To period.
class ErpReports
{
    // key => [title, description, dated]
    const LIST = [
        'monthly' => ['Monthly Income & Expenses', 'Fee income, expenses and cash surplus per month', true],
        'collections' => ['Fee Collection', 'Every payment received with student, course and method', true],
        'dues' => ['Student Dues', 'Students with an unpaid fee balance', false],
        'expense-report' => ['Expense Report', 'Every expense voucher by category', true],
        'enrollments' => ['Course Enrollment & Revenue', 'Students, agreed fees, collected and due per course', false],
        'batch-report' => ['Batch Report', 'Seats filled, trainer, schedule and attendance rate per batch', true],
        'attendance-summary' => ['Attendance Summary', 'Classes, present, absent and attendance rate per student', true],
        'inquiry-report' => ['Inquiry Conversion', 'Website inquiries per course and how many converted', true],
        'trainer-report' => ['Trainer Workload', 'Batches and students handled by each trainer', false],
        'certificate-report' => ['Certificates Issued', 'Completion certificates with grade', true],
    ];

    public static function rows($key, $from = null, $to = null){
        // Inside the period (dates as Y-m-d text; an empty side is open)
        $in = fn ($date) => $date && (!$from || $date->format('Y-m-d') >= $from) && (!$to || $date->format('Y-m-d') <= $to);

        switch($key){
            case 'monthly':
                $months = [];
                foreach(Payment::all()->filter(fn ($p) => $in($p->date)) as $p){
                    $months[$p->date->format('Y-m')]['in'] = ($months[$p->date->format('Y-m')]['in'] ?? 0) + $p->amount;
                }
                foreach(Expense::all()->filter(fn ($e) => $in($e->date)) as $e){
                    $months[$e->date->format('Y-m')]['out'] = ($months[$e->date->format('Y-m')]['out'] ?? 0) + $e->amount;
                }
                ksort($months);
                $rows = [];
                foreach($months as $month => $m){
                    $rows[] = [date('F Y', strtotime($month.'-01')), $m['in'] ?? 0, $m['out'] ?? 0, ($m['in'] ?? 0) - ($m['out'] ?? 0)];
                }
                return [['Month', 'Fee Income', 'Expenses', 'Cash Surplus'], ...$rows,
                    ['Total', array_sum(array_column($rows, 1)), array_sum(array_column($rows, 2)), array_sum(array_column($rows, 3))]];

            case 'collections':
                $list = Payment::with('student')->orderBy('date')->get()->filter(fn ($p) => $in($p->date));
                return [['Receipt', 'Date', 'Student', 'Course', 'Method', 'Amount'],
                    ...$list->map(fn ($p) => [$p->code, $p->date->format('Y-m-d'), $p->student?->name ?? '—', Erp::courseName($p->student?->course_id), $p->method, $p->amount]),
                    ['Total', '', $list->count().' payment(s)', '', '', $list->sum('amount')]];

            case 'dues':
                $list = Student::with('payments')->orderBy('id')->get()->filter(fn ($s) => $s->due() > 0);
                return [['ID', 'Student', 'Phone', 'Course', 'Agreed Fee', 'Paid', 'Due'],
                    ...$list->map(fn ($s) => [$s->code, $s->name, $s->phone, Erp::courseName($s->course_id), $s->fee, $s->payments->sum('amount'), $s->due()]),
                    ['Total', $list->count().' student(s)', '', '', $list->sum('fee'), $list->sum(fn ($s) => $s->payments->sum('amount')), $list->sum(fn ($s) => $s->due())]];

            case 'expense-report':
                $list = Expense::orderBy('date')->get()->filter(fn ($e) => $in($e->date));
                return [['Voucher', 'Date', 'Category', 'Description', 'Amount'],
                    ...$list->map(fn ($e) => [$e->code, $e->date->format('Y-m-d'), $e->category, $e->description, $e->amount]),
                    ...$list->groupBy('category')->map(fn ($g, $cat) => ['', '', $cat, 'Category total', $g->sum('amount')])->values(),
                    ['Total', '', '', $list->count().' voucher(s)', $list->sum('amount')]];

            case 'enrollments':
                $students = Student::with('payments')->get();
                $rows = Erp::courses()->map(function ($c) use ($students){
                    $list = $students->where('course_id', $c->id);
                    $paid = $list->sum(fn ($s) => $s->payments->sum('amount'));
                    return [Erp::courseCode($c), $c->course('title'), Erp::courseCategory($c), $list->count(), $list->sum('fee'), $paid, $list->sum(fn ($s) => $s->due())];
                })->values()->all();
                return [['ID', 'Course', 'Category', 'Students', 'Agreed Fees', 'Collected', 'Due'], ...$rows,
                    ['Total', '', '', array_sum(array_column($rows, 3)), array_sum(array_column($rows, 4)), array_sum(array_column($rows, 5)), array_sum(array_column($rows, 6))]];

            case 'batch-report':
                $students = Student::whereNotNull('batch_id')->get()->countBy('batch_id');
                $attendance = Attendance::all()->filter(fn ($a) => $in($a->date))->groupBy('batch_id');
                return [['Batch', 'Course', 'Trainer', 'Start', 'Days & Time', 'Room', 'Students', 'Seats', 'Attendance Rate', 'Status'],
                    ...Batch::with('trainer')->orderBy('id')->get()->map(function ($b) use ($students, $attendance){
                        $marks = $attendance[$b->id] ?? collect();
                        return [$b->code, Erp::courseName($b->course_id), $b->trainer?->name ?? '—', $b->start?->format('Y-m-d'), $b->days.' • '.$b->time, $b->room,
                            $students[$b->id] ?? 0, $b->capacity, $marks->count() ? round(100 * $marks->where('status', 'Present')->count() / $marks->count()).'%' : '—', $b->status];
                    })];

            case 'attendance-summary':
                $list = Attendance::with('student', 'batch')->get()->filter(fn ($a) => $in($a->date));
                return [['Student', 'ID', 'Batch', 'Classes', 'Present', 'Absent', 'Attendance Rate'],
                    ...$list->groupBy(fn ($a) => $a->student_id.'|'.$a->batch_id)->map(function ($g){
                        $present = $g->where('status', 'Present')->count();
                        return [$g->first()->student?->name ?? '—', $g->first()->student?->code ?? '—', $g->first()->batch?->code ?? '—', $g->count(), $present, $g->count() - $present, round(100 * $present / $g->count()).'%'];
                    })->sortBy(0)->values()];

            case 'inquiry-report':
                $list = Erp::inquiries()->filter(fn ($x) => $in($x->created_at));
                $rows = $list->groupBy('src_id')->map(function ($g, $course){
                    $stage = fn ($s) => $g->filter(fn ($x) => $x->inquiry('stage') == $s)->count();
                    return [Erp::courseName($course), $g->count(), $stage('New'), $stage('Follow-up'), $stage('Converted'), round(100 * $stage('Converted') / $g->count()).'%'];
                })->sortByDesc(1)->values()->all();
                $converted = array_sum(array_column($rows, 4));
                return [['Course', 'Inquiries', 'New', 'Follow-up', 'Converted', 'Conversion'], ...$rows,
                    ['Total', $list->count(), array_sum(array_column($rows, 2)), array_sum(array_column($rows, 3)), $converted, $list->count() ? round(100 * $converted / $list->count()).'%' : '—']];

            case 'trainer-report':
                $batches = Batch::all()->groupBy('trainer_id');
                $students = Student::whereNotNull('batch_id')->get()->countBy('batch_id');
                return [['ID', 'Trainer', 'Specialty', 'Phone', 'Hourly Rate', 'Batches', 'Ongoing', 'Students'],
                    ...Trainer::orderBy('id')->get()->map(function ($t) use ($batches, $students){
                        $list = $batches[$t->id] ?? collect();
                        return [$t->code, $t->name, $t->specialty, $t->phone, $t->rate, $list->count(), $list->where('status', 'Ongoing')->count(), $list->sum(fn ($b) => $students[$b->id] ?? 0)];
                    })];

            case 'certificate-report':
                $list = Certificate::with('student')->orderBy('issued')->get()->filter(fn ($c) => $in($c->issued));
                return [['Certificate', 'Issued', 'Student', 'Course', 'Grade', 'GPA', 'CGPA'],
                    ...$list->map(fn ($c) => [$c->code, $c->issued->format('Y-m-d'), $c->student?->name ?? '—', Erp::courseName($c->course_id), $c->grade, $c->gpa ?? '', $c->cgpa ?? '']),
                    ['Total', '', $list->count().' certificate(s)', '', '', '', '']];
        }
        abort(404);
    }
}
