<?php

namespace App\Http\Controllers\Welcome;

use App\Http\Controllers\Controller;
use App\Models\Erp\Certificate;
use App\Models\Erp\Student;
use App\Support\CertificateTemplate;
use Illuminate\Http\Request;

// Website "Find Certificate" (footer): search by certificate number (RA-XXXX-XXXX) or Student ID (STU-1001),
// then download the certificate. Searches are rate-limited (route).
class CertificateFinderController extends Controller
{
    public function find(Request $r){
        $number = trim((string) $r->query('number', ''));
        $searched = $number !== '';
        $certificates = collect();
        $byStudent = false;

        if($searched){
            $input = mb_substr($number, 0, 40);
            if($serial = Certificate::normalizeSerial($input)){
                $certificates = Certificate::with(['student', 'course'])->where('serial', $serial)->get();
            }elseif($student = $this->studentFromId($input)){
                // All certificates of that student (every course record of the same person)
                $byStudent = true;
                $certificates = Certificate::with(['student', 'course'])->whereIn('student_id', Student::where('student_no', $student->student_no)->pluck('id'))->latest('issued')->get();
            }
        }

        return view('welcome.certificates.find', compact('number', 'certificates', 'searched', 'byStudent'));
    }

    public function show(Request $r, $serial){
        $certificate = Certificate::with('student.batch')->where('serial', Certificate::normalizeSerial($serial))->first();
        abort_unless($certificate, 404);

        return view('welcome.student.certificate', [
            'certificate' => $certificate,
            'template' => CertificateTemplate::get(),
            'values' => CertificateTemplate::values($certificate),
            'backUrl' => route('certificates.find', ['number' => $certificate->code]),
            'backLabel' => 'Find Certificate',
        ]);
    }

    // "STU-1003", "stu 1003" or "1003" => a course record of student STU-1003
    private function studentFromId($input){
        $no = Student::numberFromCode($input);
        return $no ? Student::where('student_no', $no)->orderBy('id')->first() : null;
    }
}
