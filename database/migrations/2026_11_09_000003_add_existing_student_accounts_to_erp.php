<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Website student accounts registered before registration added students to the ERP:
// each account without an ERP student record gets one (no course yet), linked to the account.
return new class extends Migration
{
    public function up(): void
    {
        $linked = DB::table('erp_students')->whereNotNull('account_id')->pluck('account_id')->all();
        foreach (DB::table('student_accounts')->whereNotIn('id', $linked)->get() as $account) {
            DB::table('erp_students')->insert([
                'account_id' => $account->id,
                'name' => $account->name,
                'phone' => $account->phone,
                'course_id' => null,
                'batch_id' => null,
                'fee' => 0,
                'admitted_on' => null,
                'created_at' => $account->created_at ?? now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        // Records may have got courses, payments or certificates since; they are kept
    }
};
