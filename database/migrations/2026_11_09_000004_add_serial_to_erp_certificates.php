<?php

use App\Models\Erp\Certificate;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

// Certificate number that cannot be guessed (e.g. RA-7K3P-9QX2), printed on the certificate and used by
// the website "Find Certificate" page. Existing certificates get one too.
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_certificates', function (Blueprint $table) {
            $table->string('serial', 20)->nullable()->unique()->after('id');
        });

        foreach (DB::table('erp_certificates')->whereNull('serial')->pluck('id') as $id) {
            DB::table('erp_certificates')->where('id', $id)->update(['serial' => Certificate::newSerial()]);
        }
    }

    public function down(): void
    {
        Schema::table('erp_certificates', function (Blueprint $table) {
            $table->dropColumn('serial');
        });
    }
};
