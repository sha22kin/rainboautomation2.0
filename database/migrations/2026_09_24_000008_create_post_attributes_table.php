<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_attributes', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('src_id')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('reff_id')->nullable();
            $table->tinyInteger('type')->default(0)->comment('0=Category Post, 1=blog Category Post, 2=Blog Tags post');
            $table->string('status', 20)->nullable();
            $table->integer('duration')->nullable();
            $table->bigInteger('drag')->nullable();
            $table->bigInteger('addedby_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_attributes');
    }
};
