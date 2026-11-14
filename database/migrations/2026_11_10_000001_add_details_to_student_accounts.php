<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Student registration details (design: desing/Student_Portal.html): birth date, profession, institute,
// education, preferred course, training preference / location and address
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_accounts', function (Blueprint $table) {
            $table->date('dob')->nullable()->after('phone_key');
            $table->string('profession', 60)->nullable()->after('dob');
            $table->string('organization', 150)->nullable()->after('profession');
            $table->string('education', 60)->nullable()->after('organization');
            $table->unsignedBigInteger('preferred_course_id')->nullable()->after('education')->comment('post_extras.id (type 4), the course the student wants; the office enrolls');
            $table->string('training_mode', 60)->nullable()->after('preferred_course_id');
            $table->string('training_location', 60)->nullable()->after('training_mode');
            $table->string('address', 255)->nullable()->after('training_location');
        });
    }

    public function down(): void
    {
        Schema::table('student_accounts', function (Blueprint $table) {
            $table->dropColumn(['dob', 'profession', 'organization', 'education', 'preferred_course_id', 'training_mode', 'training_location', 'address']);
        });
    }
};
