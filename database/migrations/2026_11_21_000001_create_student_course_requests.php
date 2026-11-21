<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// "Buy a Course" from the student profile: the request waits for the admin (ERP → Course Requests).
// Approving it enrolls the student in the course (ERP → Student Admissions). No payment here: fees go through the office.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_course_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('account_id')->index()->comment('student_accounts.id');
            $table->unsignedBigInteger('course_id')->comment('post_extras.id (type 4)');
            $table->string('note', 500)->nullable();
            $table->string('status', 20)->default('pending')->index()->comment('pending, approved, rejected');
            $table->unsignedBigInteger('student_id')->nullable()->comment('erp_students.id made on approval');
            $table->string('admin_note', 500)->nullable();
            $table->unsignedBigInteger('handled_by')->nullable();
            $table->timestamp('handled_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_course_requests');
    }
};
