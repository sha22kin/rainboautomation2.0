<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\Erp\Attendance;
use App\Models\Erp\Batch;
use App\Models\Erp\Certificate;
use App\Models\Erp\Expense;
use App\Models\Erp\Lab;
use App\Models\Erp\Payment;
use App\Models\Erp\Setting;
use App\Models\Erp\Student;
use App\Models\Erp\Trainer;
use App\Models\PostExtra;
use App\Support\CertificateTemplate;
use App\Support\Erp;
use App\Support\ErpReports;
use App\Support\WebsiteMenu;
use Database\Seeders\ErpSampleSeeder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Validation\Rule;

class ErpController extends Controller
{
    // Listing page => [title, description, record type]
    const LISTINGS = [
        'inquiries' => ['Website Inquiries', 'Receive and follow up on course inquiries', 'inquiry'],
        'courses' => ['Course Management', 'Based on your supplied training website', 'course'],
        'categories' => ['Course Categories', 'Groups and filter buttons of the website training courses', 'category'],
        'students' => ['Student Admissions', 'Student enrollment, selected course, fee due', 'student'],
        'batches' => ['Batch & Training Schedule', 'Manage class dates, trainers and rooms', 'batch'],
        'trainers' => ['Trainer Management', 'Trainer profiles and hourly rates', 'trainer'],
        'attendance' => ['Student Attendance', 'Class attendance records', 'attendance'],
        'fees' => ['Fees & Receipts', 'Payments, installments and due tracking', 'payment'],
        'expenses' => ['Expenses & Payroll', 'Record operating costs and trainer remuneration', 'expense'],
        'labs' => ['Lab & Equipment', 'Manage your existing teaching facilities', 'lab'],
        'certificates' => ['Certificates & Assessments', 'Issue completion records', 'certificate'],
    ];

    // Record type => [model, listing page]
    const TYPES = [
        'student' => [Student::class, 'students'],
        'batch' => [Batch::class, 'batches'],
        'trainer' => [Trainer::class, 'trainers'],
        'attendance' => [Attendance::class, 'attendance'],
        'payment' => [Payment::class, 'fees'],
        'expense' => [Expense::class, 'expenses'],
        'lab' => [Lab::class, 'labs'],
        'certificate' => [Certificate::class, 'certificates'],
        'inquiry' => [PostExtra::class, 'inquiries'],
        'course' => [PostExtra::class, 'courses'],
    ];

    const EXPENSE_CATEGORIES = ['Trainer remuneration', 'Rent & utilities', 'Lab consumables', 'Marketing', 'Equipment maintenance', 'Other'];
    const PAYMENT_METHODS = ['Cash', 'Bank', 'bKash', 'Nagad', 'Other'];

    public function dashboard(){
        $students = Student::with('payments')->get();
        $data = [
            'gross' => Erp::gross(),
            'costs' => Erp::costs(),
            'totalDue' => $students->sum(fn ($s) => $s->due()),
            'studentCount' => $students->count(),
            'batches' => Batch::orderBy('id')->get(),
            'inquiries' => Erp::inquiries(),
            'courseCount' => Erp::courses()->count(),
            'trainerCount' => Trainer::count(),
            'labCount' => Lab::count(),
        ];
        return view('cms.erp.dashboard', $data + $this->formData());
    }

    public function listing($kind){
        $cfg = self::LISTINGS[$kind];
        $records = match ($kind) {
            'inquiries' => Erp::inquiries(),
            'courses' => Erp::courses(),
            'categories' => Erp::categories(),
            'students' => Student::with('payments', 'batch')->orderBy('id')->get(),
            'batches' => Batch::with('trainer')->orderBy('id')->get(),
            'trainers' => Trainer::orderBy('id')->get(),
            'attendance' => Attendance::with('student', 'batch')->orderBy('id')->get(),
            'fees' => Payment::with('student')->orderBy('id')->get(),
            'expenses' => Expense::orderBy('id')->get(),
            'labs' => Lab::orderBy('id')->get(),
            'certificates' => Certificate::with('student')->orderBy('id')->get(),
        };

        $data = compact('kind', 'cfg', 'records');
        // Course batches popup: the batches of every course
        // Batch-wise attendance: students of every batch, and the marks already saved per batch + date
        if($kind == 'attendance'){
            $data['batchStudents'] = Student::whereNotNull('batch_id')->orderBy('name')->get()
                ->groupBy('batch_id')->map(fn ($list) => $list->map(fn ($s) => [$s->id, $s->name, $s->code])->values());
            $data['attendanceMarks'] = $records->groupBy(fn ($a) => $a->batch_id.'|'.$a->date?->format('Y-m-d'))
                ->map(fn ($list) => $list->pluck('status', 'student_id'));
        }
        if($kind == 'courses'){
            $data['courseBatches'] = Batch::with('trainer')->orderBy('id')->get()->groupBy('course_id');
        }
        if($kind == 'fees' || $kind == 'expenses'){
            $data += [
                'gross' => Erp::gross(),
                'costs' => Erp::costs(),
                'totalDue' => Erp::totalDue(),
                'studentCount' => Student::count(),
                'paymentCount' => Payment::count(),
                'expenseCount' => Expense::count(),
            ];
        }
        return view('cms.erp.listing', $data + $this->formData());
    }

    public function store(Request $r, $type){
        if($type == 'attendancebatch'){
            return $this->saveBatchAttendance($r);
        }
        if($type == 'category'){
            return $this->saveCategory($r);
        }
        abort_unless(isset(self::TYPES[$type]), 404);
        $class = self::TYPES[$type][0];
        $record = new $class;
        if($type == 'inquiry'){
            $record->type = 5;
            $record->status = 'unread';
        }
        // New website course (Training Academy page), placed at the end of the website order
        if($type == 'course'){
            $page = templatePage('Training Academy');
            if(!$page){
                return back()->with('toast', 'No active page uses the Training Academy template');
            }
            $record->type = 4;
            $record->src_id = $page->id;
            $record->drag = PostExtra::where('type', 4)->where('src_id', $page->id)->max('drag') + 1;
            $record->addedby_id = Auth::id();
        }
        return $this->save($r, $type, $record);
    }

    public function update(Request $r, $type, $id){
        if($type == 'category'){
            return $this->saveCategory($r, $id);
        }
        abort_unless(isset(self::TYPES[$type]), 404);
        $record = $this->find($type, $id);
        return $this->save($r, $type, $record);
    }

    public function destroy($type, $id){
        if($type == 'category'){
            return $this->deleteCategory($id);
        }
        abort_unless(isset(self::TYPES[$type]), 404);
        $record = $this->find($type, $id);

        // Keep the books consistent: a student with receipts, attendance or certificates stays
        if($type == 'student' && ($record->payments()->exists() || Attendance::where('student_id', $id)->exists() || Certificate::where('student_id', $id)->exists())){
            return back()->with('toast', 'This student has payments, attendance or certificates and cannot be deleted');
        }
        // A course still used by students, batches or certificates stays
        if($type == 'course'){
            if(Student::where('course_id', $id)->exists() || Batch::where('course_id', $id)->exists() || Certificate::where('course_id', $id)->exists()){
                return back()->with('toast', 'This course has students, batches or certificates and cannot be deleted');
            }
            $this->deleteCourseImage($record->course('image'));
        }
        if($type == 'batch'){
            Student::where('batch_id', $id)->update(['batch_id' => null]);
        }
        if($type == 'trainer'){
            Batch::where('trainer_id', $id)->update(['trainer_id' => null]);
        }

        $record->delete();
        // Deleted from a course's batches popup: reopen that popup
        if($type == 'batch' && request('back')){
            return redirect()->route('erp.listing', ['courses', 'batches' => request('back')])->with('toast', 'Batch deleted');
        }
        return back()->with('toast', 'Record deleted');
    }

    // Website inquiry follow-up: New → Follow-up → Converted → New
    // Batch-wise attendance: one Present / Absent mark per student of the batch for a class date.
    // A mark already saved for the same student, batch and date is updated, never duplicated.
    private function saveBatchAttendance(Request $r){
        $validator = validator($r->all(), [
            'batch' => 'required|exists:erp_batches,id',
            'date' => 'required|date',
            'status' => 'required|array',
            'status.*' => 'required|in:Present,Absent',
        ], ['status.required' => 'This batch has no students to mark']);
        if($validator->fails()){
            return back()->with('toast', $validator->errors()->first());
        }
        $v = $validator->validated();
        $students = Student::where('batch_id', $v['batch'])->pluck('id')->all();
        $saved = 0;
        foreach($v['status'] as $studentId => $status){
            if(!in_array((int) $studentId, $students)){
                continue;
            }
            Attendance::updateOrCreate(
                ['student_id' => $studentId, 'batch_id' => $v['batch'], 'date' => $v['date']],
                ['status' => $status]
            );
            $saved++;
        }
        return redirect()->route('erp.listing', 'attendance')->with('toast', $saved.' attendance mark(s) saved');
    }

    // One certificate filled from the certificate template, ready to print (A4 landscape)
    public function certificatePrint($id){
        $certificate = Certificate::with('student.batch')->findOrFail($id);
        return view('cms.erp.certificate-print', [
            'certificate' => $certificate,
            'template' => CertificateTemplate::get(),
            'values' => CertificateTemplate::values($certificate),
        ]);
    }

    public function certificateTemplate(){
        return view('cms.erp.certificate-template', ['template' => CertificateTemplate::get(), 'sample' => CertificateTemplate::sample()]);
    }

    public function saveCertificateTemplate(Request $r){
        $rules = collect(CertificateTemplate::FIELDS)->map(fn ($f) => $f[2] == 'color' ? ['required', 'regex:/^#[0-9a-fA-F]{6}$/'] : ['nullable', 'max:'.($f[2] == 'textarea' ? 600 : 200)])->all();
        $validator = validator($r->all(), $rules);
        if($validator->fails()){
            return back()->with('toast', $validator->errors()->first());
        }
        CertificateTemplate::save($validator->validated());
        return back()->with('toast', 'Certificate template saved');
    }

    public function resetCertificateTemplate(){
        CertificateTemplate::reset();
        return back()->with('toast', 'Certificate template reset to default');
    }

    // Course website toggle: show the course on the public website, or hide it again
    public function courseWebsite($id){
        $course = PostExtra::where('type', 4)->where('status', '<>', 'temp')->findOrFail($id);
        $course->status = $course->status == 'active' ? 'inactive' : 'active';
        $course->editedby_id = Auth::id();
        $course->save();
        return back()->with('toast', $course->status == 'active' ? 'Course is now shown on the website' : 'Course removed from the website');
    }

    // Weekly Training Plan: take one day out of a batch's training days ("Sat, Mon, Wed" → "Sat, Wed")
    public function batchRemoveDay(Request $r, $id){
        $batch = Batch::findOrFail($id);
        $day = strtolower(substr((string) $r->day, 0, 3));
        $days = collect(preg_split('/[\s,]+/', (string) $batch->days, -1, PREG_SPLIT_NO_EMPTY));
        $left = $days->reject(fn ($d) => strtolower(substr($d, 0, 3)) == $day);
        if($left->count() == $days->count()){
            return back()->with('toast', 'This batch has no class on that day');
        }
        if($left->isEmpty()){
            return back()->with('toast', 'This is the only training day of '.$batch->code.'; edit or delete the batch instead');
        }
        $batch->days = $left->implode(', ');
        $batch->save();
        return back()->with('toast', $batch->code.' removed from '.ucfirst($day));
    }

    public function inquiryStatus($id){
        $inquiry = PostExtra::where('type', 5)->findOrFail($id);
        $stages = Erp::INQUIRY_STAGES;
        $next = $stages[(array_search($inquiry->inquiry('stage'), $stages) + 1) % count($stages)];
        $inquiry->content = json_encode(array_merge($inquiry->inquiry(), ['stage' => $next]), JSON_UNESCAPED_UNICODE);
        $inquiry->save();
        return back()->with('toast', 'Inquiry status: '.$next);
    }

    public function reports(){
        $students = Student::with('payments')->get();
        $debts = $students->map(fn ($s) => (object) ['s' => $s, 'amount' => $s->due()])
            ->filter(fn ($x) => $x->amount > 0)->sortByDesc('amount')->values();
        $enrollments = Erp::courses()->map(fn ($c) => (object) ['course' => $c, 'count' => $students->where('course_id', $c->id)->count()])
            ->filter(fn ($x) => $x->count > 0)->values();

        return view('cms.erp.reports', [
            'gross' => Erp::gross(),
            'costs' => Erp::costs(),
            'debts' => $debts,
            'enrollments' => $enrollments,
            'studentCount' => $students->count(),
            'reports' => ErpReports::LIST,
            'from' => $this->reportDate('from'),
            'to' => $this->reportDate('to'),
        ]);
    }

    // Report period (From / To on the Reports page) as Y-m-d, or null
    private function reportDate($side){
        $date = request($side);
        return $date && preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) ? $date : null;
    }

    private function reportPeriod(){
        $from = $this->reportDate('from');
        $to = $this->reportDate('to');
        return $from || $to ? ($from ?: 'Start').' to '.($to ?: 'Today') : 'All dates';
    }

    public function settings(){
        return view('cms.erp.settings', ['branch' => Setting::get('branch', 'Training Center')]);
    }

    public function saveSettings(Request $r){
        $this->authorizeSettings();
        $r->validate(['branch' => 'nullable|max:150']);
        Setting::put('branch', $r->branch ?: 'Training Center');
        return back()->with('toast', 'Settings saved');
    }

    // Replace every ERP record with the sample data of the design
    public function resetData(){
        $this->authorizeSettings();
        (new ErpSampleSeeder)->run();
        return back()->with('toast', 'Sample data restored');
    }

    public function export($kind){
        $rows = $this->exportRows($kind);
        $csv = collect($rows)->map(fn ($row) => collect($row)->map(fn ($v) => '"'.str_replace('"', '""', (string) $v).'"')->implode(','))->implode("\r\n");
        return response("\u{FEFF}".$csv, 200, [
            'Content-Type' => 'text/csv; charset=utf-8',
            'Content-Disposition' => 'attachment; filename="rainbow-'.$kind.'.csv"',
        ]);
    }

    // Same rows as the CSV, as an Excel workbook (.xlsx)
    public function exportExcel($kind){
        $file = Erp::xlsx($this->exportRows($kind), ucfirst($kind));
        return response()->download($file, 'rainbow-'.$kind.'.xlsx', [
            'Content-Type' => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])->deleteFileAfterSend();
    }

    // Printable table of the same rows (opens the browser print dialog)
    public function print($kind){
        $rows = $this->exportRows($kind);
        return view('cms.erp.print', [
            'title' => self::LISTINGS[$kind][0] ?? ErpReports::LIST[$kind][0],
            'period' => (ErpReports::LIST[$kind][2] ?? false) ? $this->reportPeriod() : null,
            'headers' => array_shift($rows),
            'rows' => $rows,
            'branch' => Setting::get('branch', 'Training Center'),
        ]);
    }

    private function exportRows($kind){
        if(isset(ErpReports::LIST[$kind])){
            return ErpReports::rows($kind, $this->reportDate('from'), $this->reportDate('to'));
        }
        return match ($kind) {
            'inquiries' => [['id', 'name', 'phone', 'course', 'status', 'date'], ...Erp::inquiries()->map(fn ($x) => [Erp::inquiryCode($x), $x->name, $x->inquiry('phone'), Erp::courseName($x->src_id), $x->inquiry('stage'), $x->created_at?->format('Y-m-d')])],
            'categories' => [['key', 'name', 'website filter', 'colour', 'courses'], ...Erp::categories()->map(fn ($c) => [$c->key, $c->label, $c->filter, $c->color, $c->courses])],
            'courses' => [['id', 'name', 'category', 'hours', 'fee'], ...Erp::courses()->map(fn ($c) => [Erp::courseCode($c), $c->course('title'), Erp::courseCategory($c), Erp::courseHours($c), Erp::courseFee($c)])],
            'students' => [['id', 'name', 'phone', 'course', 'batch', 'date', 'fee', 'due'], ...Student::with('payments', 'batch')->get()->map(fn ($s) => [$s->code, $s->name, $s->phone, Erp::courseName($s->course_id), $s->batch?->code, $s->admitted_on?->format('Y-m-d'), $s->fee, $s->due()])],
            'batches' => [['id', 'course', 'trainer', 'start', 'time', 'days', 'room', 'capacity', 'status'], ...Batch::with('trainer')->get()->map(fn ($b) => [$b->code, Erp::courseName($b->course_id), $b->trainer?->name, $b->start?->format('Y-m-d'), $b->time, $b->days, $b->room, $b->capacity, $b->status])],
            'trainers' => [['id', 'name', 'specialty', 'phone', 'rate'], ...Trainer::all()->map(fn ($t) => [$t->code, $t->name, $t->specialty, $t->phone, $t->rate])],
            'attendance' => [['id', 'student', 'batch', 'date', 'status'], ...Attendance::with('student', 'batch')->get()->map(fn ($a) => [$a->code, $a->student?->name, $a->batch?->code, $a->date?->format('Y-m-d'), $a->status])],
            'fees' => [['id', 'student', 'amount', 'date', 'method'], ...Payment::with('student')->get()->map(fn ($p) => [$p->code, $p->student?->name, $p->amount, $p->date?->format('Y-m-d'), $p->method])],
            'expenses' => [['id', 'category', 'description', 'amount', 'date'], ...Expense::all()->map(fn ($e) => [$e->code, $e->category, $e->description, $e->amount, $e->date?->format('Y-m-d')])],
            'labs' => [['id', 'name', 'equipment', 'status'], ...Lab::all()->map(fn ($l) => [$l->code, $l->name, $l->equipment, $l->status])],
            'certificates' => [['id', 'student', 'course', 'issued', 'grade', 'gpa', 'cgpa'], ...Certificate::with('student')->get()->map(fn ($c) => [$c->code, $c->student?->name, Erp::courseName($c->course_id), $c->issued?->format('Y-m-d'), $c->grade, $c->gpa ?? '', $c->cgpa ?? ''])],
            default => abort(404),
        };
    }

    private function find($type, $id){
        return match ($type) {
            'inquiry' => PostExtra::where('type', 5)->findOrFail($id),
            'course' => PostExtra::where('type', 4)->where('status', '<>', 'temp')->findOrFail($id),
            default => self::TYPES[$type][0]::findOrFail($id),
        };
    }

    private function save(Request $r, $type, $record){
        $courseIds = Erp::courses()->pluck('id')->all();
        $rules = match ($type) {
            'inquiry' => ['name' => 'required|max:100', 'phone' => 'required|max:30', 'course' => ['required', Rule::in($courseIds)], 'status' => ['required', Rule::in(Erp::INQUIRY_STAGES)]],
            'student' => ['name' => 'required|max:150', 'phone' => 'required|max:30', 'course' => ['required', Rule::in($courseIds)], 'batch' => 'nullable|exists:erp_batches,id'],
            'batch' => ['course' => ['required', Rule::in($courseIds)], 'trainer' => 'required|exists:erp_trainers,id', 'start' => 'required|date', 'time' => 'required|max:10', 'days' => 'required|max:100', 'room' => 'required|max:150', 'capacity' => 'required|integer|min:0', 'status' => 'required|in:Upcoming,Ongoing,Completed'],
            'trainer' => ['name' => 'required|max:150', 'specialty' => 'required|max:150', 'phone' => 'required|max:30', 'rate' => 'required|numeric|min:0'],
            'attendance' => ['student' => 'required|exists:erp_students,id', 'batch' => 'required|exists:erp_batches,id', 'date' => 'required|date', 'status' => 'required|in:Present,Absent'],
            'payment' => ['student' => 'required|exists:erp_students,id', 'amount' => 'required|numeric|gt:0', 'date' => 'required|date', 'method' => ['required', Rule::in(self::PAYMENT_METHODS)]],
            'expense' => ['category' => ['required', Rule::in(self::EXPENSE_CATEGORIES)], 'description' => 'required|max:255', 'amount' => 'required|numeric|gt:0', 'date' => 'required|date'],
            'lab' => ['name' => 'required|max:150', 'equipment' => 'required', 'status' => 'required|in:Available,Maintenance,Unavailable'],
            'certificate' => ['student' => 'required|exists:erp_students,id', 'course' => ['required', Rule::in($courseIds)], 'issued' => 'required|date', 'grade' => ['required', Rule::in(Certificate::GRADES)], 'gpa' => 'nullable|numeric|min:0|max:5', 'cgpa' => 'nullable|numeric|min:0|max:5'],
            'course' => ['title' => 'required|max:191', 'category' => ['required', Rule::in(array_keys(trainingCategories()))], 'duration' => 'nullable|max:50', 'fee' => 'nullable|integer|min:0|max:100000000', 'description' => 'nullable|max:1000', 'modules' => 'nullable|max:3000', 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048', 'alt' => 'nullable|max:191'],
        };
        $validator = validator($r->all(), $rules, ['amount.gt' => 'Amount must be greater than zero']);
        if($validator->fails()){
            return back()->with('toast', $validator->errors()->first());
        }
        $v = $validator->validated();

        switch($type){
            case 'inquiry':
                $course = Erp::course($v['course']);
                $record->name = $v['name'];
                $record->src_id = $course->id;
                $record->parent_id = $course->src_id;
                $record->content = json_encode(array_merge($record->exists ? $record->inquiry() : ['message' => null, 'ip' => $r->ip()], [
                    'phone' => $v['phone'],
                    'course_title' => $course->course('title'),
                    'stage' => $v['status'],
                ]), JSON_UNESCAPED_UNICODE);
                break;
            case 'course':
                // A new image replaces the old upload; without one the current image is kept
                $image = $record->course('image');
                if($r->hasFile('image')){
                    $name = safeUploadName($r->file('image'));
                    $this->deleteCourseImage($image);
                    $r->file('image')->move(public_path('medies/courses'), $name);
                    $image = 'medies/courses/'.$name;
                }
                $record->name = $v['title'];
                $record->content = json_encode(array_merge($record->course(), [
                    'category' => $v['category'],
                    'title' => $v['title'],
                    'description' => $v['description'] ?? null,
                    'duration' => $v['duration'] ?? null,
                    'fee' => isset($v['fee']) && $v['fee'] !== '' ? (int) $v['fee'] : null,
                    'image' => $image,
                    'alt' => $v['alt'] ?? null,
                    'modules' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $v['modules'] ?? '')))),
                ]), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
                // A new course stays off the website until it is published with the website toggle
                $record->status ??= 'inactive';
                $record->editedby_id = Auth::id();
                break;
            case 'student':
                // The agreed fee is the course fee at admission; it follows a course change only
                if(!$record->exists || $record->course_id != $v['course']){
                    $record->fee = Erp::courseFee(Erp::course($v['course']));
                }
                $record->fill(['name' => $v['name'], 'phone' => $v['phone'], 'course_id' => $v['course'], 'batch_id' => $v['batch'] ?? null]);
                $record->admitted_on ??= now()->toDateString();
                break;
            case 'batch':
                $record->fill(['course_id' => $v['course'], 'trainer_id' => $v['trainer']] + collect($v)->except('course', 'trainer')->all());
                break;
            case 'attendance':
                $record->fill(['student_id' => $v['student'], 'batch_id' => $v['batch'], 'date' => $v['date'], 'status' => $v['status']]);
                break;
            case 'payment':
                $record->fill(['student_id' => $v['student']] + collect($v)->except('student')->all());
                $record->addedby_id ??= Auth::id();
                break;
            case 'expense':
                $record->fill($v);
                $record->addedby_id ??= Auth::id();
                break;
            case 'certificate':
                $record->fill(['student_id' => $v['student'], 'course_id' => $v['course'], 'issued' => $v['issued'], 'grade' => $v['grade'], 'gpa' => filled($v['gpa'] ?? null) ? $v['gpa'] : null, 'cgpa' => filled($v['cgpa'] ?? null) ? $v['cgpa'] : null]);
                break;
            default:
                $record->fill($v);
        }
        $record->save();

        // Saved from a course's batches popup: back to Course Management with that popup open
        if($type == 'batch' && $r->filled('back')){
            return redirect()->route('erp.listing', ['courses', 'batches' => $record->course_id])->with('toast', 'Batch saved successfully');
        }
        return redirect()->route('erp.listing', self::TYPES[$type][1])->with('toast', 'Record saved successfully');
    }

    // Course category (website filter + card label). The key is made from the name once and never changes,
    // so courses keep their category when it is renamed.
    private function saveCategory(Request $r, $key = null){
        $categories = trainingCategories();
        abort_if($key !== null && !isset($categories[$key]), 404);
        $validator = validator($r->all(), ['label' => 'required|max:60', 'filter' => 'nullable|max:40', 'color' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/']]);
        if($validator->fails()){
            return back()->with('toast', $validator->errors()->first());
        }
        $v = $validator->validated();
        $taken = collect($categories)->except($key)->contains(fn ($c) => mb_strtolower($c['label']) == mb_strtolower($v['label']));
        if($taken){
            return back()->with('toast', 'A category with this name already exists');
        }
        if($key === null){
            $base = \Illuminate\Support\Str::slug($v['label']) ?: 'category';
            $key = $base;
            for($i = 2; isset($categories[$key]); $i++){
                $key = $base.'-'.$i;
            }
        }
        $categories[$key] = ['label' => $v['label'], 'filter' => ($v['filter'] ?? null) ?: $v['label'], 'color' => strtolower($v['color'])];
        Setting::put('course_categories', json_encode($categories, JSON_UNESCAPED_UNICODE));
        return redirect()->route('erp.listing', 'categories')->with('toast', 'Category saved');
    }

    private function deleteCategory($key){
        $categories = trainingCategories();
        abort_unless(isset($categories[$key]), 404);
        if(PostExtra::where('type', 4)->get()->contains(fn ($c) => $c->course('category') == $key)){
            return back()->with('toast', 'This category has courses and cannot be deleted');
        }
        if(count($categories) == 1){
            return back()->with('toast', 'At least one category is needed');
        }
        unset($categories[$key]);
        Setting::put('course_categories', json_encode($categories, JSON_UNESCAPED_UNICODE));
        return back()->with('toast', 'Category deleted');
    }

    // Only uploaded course images are removed; external image links are left alone
    private function deleteCourseImage($image){
        if($image && str_starts_with($image, 'medies/courses/') && File::exists(public_path($image))){
            File::delete(public_path($image));
        }
    }

    private function authorizeSettings(){
        abort_unless(WebsiteMenu::can('appsSetting', 'general'), 403);
    }

    // Select options of the add / edit forms (design: courseOpts, studentOpts, batchOpts, trainerOpts)
    private function formData(){
        return [
            'courseOpts' => Erp::courses()->map(fn ($c) => [$c->id, $c->course('title')])->all(),
            'studentOpts' => Student::orderBy('id')->get()->map(fn ($s) => [$s->id, $s->name.' — '.$s->code])->all(),
            'batchOpts' => Batch::orderBy('id')->get()->map(fn ($b) => [$b->id, $b->code.' — '.Erp::courseName($b->course_id)])->all(),
            'trainerOpts' => Trainer::orderBy('id')->get()->map(fn ($t) => [$t->id, $t->name])->all(),
            'labOpts' => Lab::orderBy('id')->pluck('name')->map(fn ($n) => [$n, $n])->all(),
            'categoryOpts' => collect(trainingCategories())->map(fn ($c, $key) => [$key, $c['label']])->values()->all(),
        ];
    }
}
