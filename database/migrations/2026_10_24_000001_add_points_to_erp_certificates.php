<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Optional GPA / CGPA points of a certificate (shown on the certificate only when filled)
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('erp_certificates', function (Blueprint $table) {
            $table->decimal('gpa', 4, 2)->nullable()->after('grade');
            $table->decimal('cgpa', 4, 2)->nullable()->after('gpa');
        });
    }

    public function down(): void
    {
        Schema::table('erp_certificates', function (Blueprint $table) {
            $table->dropColumn(['gpa', 'cgpa']);
        });
    }
};
