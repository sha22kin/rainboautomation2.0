<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attributes', function (Blueprint $table) {
            $table->id();
            $table->string('name', 191)->nullable();
            $table->string('slug')->nullable();
            $table->bigInteger('parent_id')->nullable();
            $table->bigInteger('category_id')->nullable();
            $table->bigInteger('src_id')->nullable();
            $table->text('short_description')->nullable();
            $table->text('description')->nullable();
            $table->bigInteger('view')->default(0);
            $table->tinyInteger('menu_type')->nullable()->comment('0=Custom Link, 1=Pages, 2=Post Categories, 3=Service Categories;');
            $table->string('location', 200)->nullable();
            $table->boolean('target')->default(0);
            $table->string('icon', 200)->nullable();
            $table->string('seo_title', 200)->nullable();
            $table->text('seo_description')->nullable();
            $table->text('seo_keyword')->nullable();
            $table->longText('data_counts')->nullable();
            $table->integer('type')->default(0)->comment('0=Category, 1=Slider, 2=Brand, 3=Client, 4=Galleries, 5=Portfolio, 6=Blog Category, 7=Blog Tags 8=Menus');
            $table->string('status', 10)->default('temp')->comment('temp, active, inactive');
            $table->boolean('fetured')->default(0);
            $table->bigInteger('addedby_id')->nullable();
            $table->bigInteger('editedby_id')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attributes');
    }
};
