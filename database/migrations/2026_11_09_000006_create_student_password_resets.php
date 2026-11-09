<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

// Password reset links of website student accounts (Student Login → Forgot password). Tokens are stored hashed.
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('student_password_resets', function (Blueprint $table) {
            $table->string('email', 150)->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('student_password_resets');
    }
};
