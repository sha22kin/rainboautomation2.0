<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Website student accounts (register / login from the website footer), separate from the CMS users.
// An account sees its course and certificate records once an admin links it to ERP student records.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_accounts', function (Blueprint $table) {
            $table->id();
            $table->string('name', 150);
            $table->string('email', 150)->unique();
            $table->string('phone', 30);
            $table->string('phone_key', 20)->unique()->comment('Phone digits, last 11 (01XXXXXXXXX)');
            $table->string('password');
            $table->boolean('status')->default(1)->comment('1=Active, 0=Blocked');
            $table->rememberToken();
            $table->timestamp('last_login_at')->nullable();
            $table->string('last_login_ip', 45)->nullable();
            $table->timestamps();
        });

        Schema::table('erp_students', function (Blueprint $table) {
            $table->unsignedBigInteger('account_id')->nullable()->after('id')->index()->comment('student_accounts.id, linked by an admin');
        });
    }

    public function down(): void
    {
        Schema::table('erp_students', function (Blueprint $table) {
            $table->dropColumn('account_id');
        });
        Schema::dropIfExists('student_accounts');
    }
};
