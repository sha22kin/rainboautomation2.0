<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('post_content_elements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('src_id')->nullable();
            $table->integer('parent_id')->nullable();
            $table->string('name', 200)->nullable();
            $table->text('content')->nullable();
            $table->integer('drag')->default(0);
            $table->string('item_type', 50)->nullable()->comment('empty, paragraph, gridColumn');
            $table->integer('grid_column')->default(12);
            $table->string('class_name', 100)->nullable();
            $table->integer('type')->nullable()->comment('0=page, 1=post');
            $table->string('status', 20)->nullable()->comment('temp,active,inactive');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('post_content_elements');
    }
};
