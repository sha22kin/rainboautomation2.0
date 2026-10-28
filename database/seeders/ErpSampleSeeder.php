<?php

namespace Database\Seeders;

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
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

// Sample ERP records of the CMS design (php artisan db:seed --class=ErpSampleSeeder).
// Replaces every ERP record; website courses and inquiries are not touched.
class ErpSampleSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function () {
            foreach ([Attendance::class, Certificate::class, Payment::class, Student::class, Batch::class, Trainer::class, Lab::class, Expense::class] as $model) {
                $model::query()->delete();
            }

            // Course of the design sample by its title, falling back to the first website course
            $courses = PostExtra::where('type', 4)->where('status', '<>', 'temp')->orderBy('drag')->get();
            $course = fn ($title) => $courses->firstWhere('name', $title) ?: $courses->first();
            $fee = fn ($c) => $c && is_numeric($c->course('fee')) ? (float) $c->course('fee') : 0;

            $plc = $course('PLC Programming & Industrial Control System');
            $solid = $course('SOLIDWORKS 3D Mechanical Design & Assembly Modeling');
            $lathe = $course('CNC Lathe Programming, Operation & Machining Technology');

            $labs = collect([
                ['name' => 'Automation Lab', 'equipment' => 'PLC, HMI, VFD, Servo, Pneumatics'],
                ['name' => 'Computer Lab', 'equipment' => 'CAD/CAM Workstations'],
                ['name' => 'CNC Workshop', 'equipment' => 'CNC Lathe & CNC Milling'],
            ])->map(fn ($l) => Lab::create($l + ['status' => 'Available']));

            $trainers = collect([
                ['name' => 'Engr. Rahim', 'specialty' => 'PLC / SCADA', 'phone' => '01744444444', 'rate' => 1200],
                ['name' => 'Engr. Karim', 'specialty' => 'SolidWorks / CAD', 'phone' => '01855555555', 'rate' => 1400],
                ['name' => 'Engr. Tania', 'specialty' => 'CNC / CAM', 'phone' => '01966666666', 'rate' => 1600],
            ])->map(fn ($t) => Trainer::create($t));

            $batches = collect([
                [$plc, 0, '2026-10-15', '18:00', 'Sat, Mon, Wed', 0, 15],
                [$solid, 1, '2026-10-18', '10:00', 'Sun, Tue, Thu', 1, 15],
                [$lathe, 2, '2026-10-20', '15:00', 'Sat, Tue', 2, 10],
            ])->map(fn ($b) => Batch::create([
                'course_id' => $b[0]?->id, 'trainer_id' => $trainers[$b[1]]->id, 'start' => $b[2], 'time' => $b[3],
                'days' => $b[4], 'room' => $labs[$b[5]]->name, 'capacity' => $b[6], 'status' => 'Upcoming',
            ]));

            $students = collect([
                ['Arif Hasan', '01700000001', $plc, 0, '2026-10-01'],
                ['Nusrat Jahan', '01800000002', $solid, 1, '2026-10-05'],
                ['Fahim Ahmed', '01900000003', $plc, 0, '2026-10-08'],
                ['Mitu Akter', '01600000004', $lathe, 2, '2026-10-09'],
            ])->map(fn ($s) => Student::create([
                'name' => $s[0], 'phone' => $s[1], 'course_id' => $s[2]?->id, 'batch_id' => $batches[$s[3]]->id,
                'fee' => $fee($s[2]), 'admitted_on' => $s[4],
            ]));

            foreach ([[0, 5000, '2026-10-01', 'Cash'], [1, 10000, '2026-10-05', 'Bank'], [2, 12000, '2026-10-08', 'bKash'], [3, 15000, '2026-10-09', 'Cash']] as $p) {
                Payment::create(['student_id' => $students[$p[0]]->id, 'amount' => $p[1], 'date' => $p[2], 'method' => $p[3]]);
            }

            foreach ([['Lab consumables', 'PLC wiring accessories', 2300, '2026-10-04'], ['Rent & utilities', 'Training space utilities', 5000, '2026-10-07'], ['Marketing', 'Course advertisement', 3500, '2026-10-10']] as $e) {
                Expense::create(['category' => $e[0], 'description' => $e[1], 'amount' => $e[2], 'date' => $e[3]]);
            }

            foreach ([0, 2] as $s) {
                Attendance::create(['student_id' => $students[$s]->id, 'batch_id' => $batches[0]->id, 'date' => '2026-10-10', 'status' => 'Present']);
            }

            Certificate::create(['student_id' => $students[2]->id, 'course_id' => $plc?->id, 'issued' => '2026-10-11', 'grade' => 'A']);

            if (!Setting::where('key', 'branch')->exists()) {
                Setting::put('branch', 'Panthapath Training Center');
            }
        });
    }
}
