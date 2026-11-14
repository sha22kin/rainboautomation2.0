<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Website registrations wait for the admin: approved_at stays empty until the admin approves
// (ERP → Registered Students). Only then can the student log in, and the ERP student record is made.
// Accounts that already exist stay approved.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('student_accounts', function (Blueprint $table) {
            $table->timestamp('approved_at')->nullable()->after('status')->comment('Empty = waiting for admin approval');
        });
        DB::table('student_accounts')->update(['approved_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        Schema::table('student_accounts', function (Blueprint $table) {
            $table->dropColumn('approved_at');
        });
    }
};
