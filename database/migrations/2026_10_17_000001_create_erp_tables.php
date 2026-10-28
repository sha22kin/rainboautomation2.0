<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Training ERP of the CMS. Courses and website inquiries are not here: they are the website's
// Training Academy courses (post_extras type 4) and course inquiries (post_extras type 5).
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('erp_trainers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('specialty', 150)->nullable();
            $table->string('phone', 30)->nullable();
            $table->decimal('rate', 12, 2)->default(0)->comment('Hourly rate (BDT)');
            $table->timestamps();
        });

        Schema::create('erp_labs', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->text('equipment')->nullable();
            $table->string('status', 30)->default('Available')->comment('Available, Maintenance, Unavailable');
            $table->timestamps();
        });

        Schema::create('erp_batches', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('course_id')->nullable()->comment('post_extras.id (type 4)');
            $table->unsignedBigInteger('trainer_id')->nullable();
            $table->date('start')->nullable();
            $table->string('time', 10)->nullable();
            $table->string('days', 100)->nullable();
            $table->string('room', 150)->nullable();
            $table->unsignedInteger('capacity')->default(0);
            $table->string('status', 30)->default('Upcoming')->comment('Upcoming, Ongoing, Completed');
            $table->timestamps();
        });

        Schema::create('erp_students', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('phone', 30)->nullable();
            $table->unsignedBigInteger('course_id')->nullable()->comment('post_extras.id (type 4)');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->decimal('fee', 12, 2)->default(0)->comment('Course fee agreed at admission');
            $table->date('admitted_on')->nullable();
            $table->timestamps();
        });

        Schema::create('erp_attendances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('batch_id')->nullable();
            $table->date('date');
            $table->string('status', 20)->default('Present')->comment('Present, Absent');
            $table->timestamps();
        });

        Schema::create('erp_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->string('method', 30)->default('Cash');
            $table->bigInteger('addedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('erp_expenses', function (Blueprint $table) {
            $table->id();
            $table->string('category', 100);
            $table->string('description', 255)->nullable();
            $table->decimal('amount', 12, 2);
            $table->date('date');
            $table->bigInteger('addedby_id')->nullable();
            $table->timestamps();
        });

        Schema::create('erp_certificates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('course_id')->nullable();
            $table->date('issued');
            $table->string('grade', 20)->nullable();
            $table->timestamps();
        });

        Schema::create('erp_settings', function (Blueprint $table) {
            $table->id();
            $table->string('key', 100)->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['erp_settings', 'erp_certificates', 'erp_expenses', 'erp_payments', 'erp_attendances', 'erp_students', 'erp_batches', 'erp_labs', 'erp_trainers'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
