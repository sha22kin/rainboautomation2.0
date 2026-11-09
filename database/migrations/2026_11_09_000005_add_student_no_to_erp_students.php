<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// One Student ID per person: every course record of a student carries the same student_no
// (Student ID = STU-(student_no + 1000)). Existing records of the same person (same website account,
// or else the same mobile number) are grouped under the earliest record's number.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_students', function (Blueprint $table) {
            $table->unsignedBigInteger('student_no')->nullable()->after('account_id')->index()->comment('Shared by all course records of one student; Student ID = STU-(student_no + 1000)');
        });

        $groups = [];
        foreach (DB::table('erp_students')->orderBy('id')->get(['id', 'account_id', 'phone']) as $s) {
            $digits = substr(preg_replace('/\D/', '', (string) $s->phone), -11);
            $key = $s->account_id ? 'account:'.$s->account_id : (strlen($digits) >= 10 ? 'phone:'.$digits : 'record:'.$s->id);
            $groups[$key] ??= $s->id;
            DB::table('erp_students')->where('id', $s->id)->update(['student_no' => $groups[$key]]);
        }
    }

    public function down(): void
    {
        Schema::table('erp_students', function (Blueprint $table) {
            $table->dropColumn('student_no');
        });
    }
};
