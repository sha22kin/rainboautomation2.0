<?php

namespace App\Models;

use App\Models\Erp\Student;
use App\Support\Erp;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

// "Buy Course" request from the student profile, waiting for the admin (ERP → Course Requests)
class StudentCourseRequest extends Model
{
    protected $table = 'student_course_requests';
    protected $fillable = ['account_id', 'course_id', 'note'];
    protected $casts = ['handled_at' => 'datetime'];

    public function account(){
        return $this->belongsTo(StudentAccount::class, 'account_id');
    }

    public function course(){
        return $this->belongsTo(PostExtra::class, 'course_id')->where('type', 4);
    }

    public function getCodeAttribute(){
        return 'REQ-'.str_pad($this->id, 4, '0', STR_PAD_LEFT);
    }

    public function isPending(){
        return $this->status === 'pending';
    }

    // Approve: the student is enrolled in the course under the same Student ID (a course record without a
    // course yet is filled), with the course fee. Payments are recorded by the office in ERP → Fees.
    // Returns an error message, or null when done.
    public function approve($adminId){
        if(!$this->isPending()) return 'This request was already handled.';
        $account = $this->account;
        if(!$account || !$account->isApproved()) return 'The student account is not approved.';
        $course = Erp::course($this->course_id);
        if(!$course) return 'This course no longer exists.';

        return DB::transaction(function () use ($account, $course, $adminId) {
            $records = $account->enrollments()->orderBy('id')->get();
            if($records->firstWhere('course_id', $this->course_id)){
                return 'The student is already enrolled in this course.';
            }
            $record = $records->firstWhere('course_id', null) ?: new Student([
                'account_id' => $account->id,
                'student_no' => $records->first()?->student_no,
                'name' => $account->name,
                'phone' => $account->phone,
            ]);
            $record->fill(['course_id' => $this->course_id, 'fee' => Erp::courseFee($course)]);
            $record->admitted_on ??= now()->toDateString();
            $record->save();

            $this->forceFill(['status' => 'approved', 'student_id' => $record->id, 'handled_by' => $adminId, 'handled_at' => now()])->save();
            return null;
        });
    }

    public function reject($adminId, $note = null){
        if(!$this->isPending()) return 'This request was already handled.';
        $this->forceFill(['status' => 'rejected', 'admin_note' => $note, 'handled_by' => $adminId, 'handled_at' => now()])->save();
        return null;
    }
}
