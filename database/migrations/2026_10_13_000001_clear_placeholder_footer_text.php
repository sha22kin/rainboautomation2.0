<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Admin → Setting → General → Footer Text still holds the previous site's lorem ipsum.
// Clear it so the footer shows the default "© {year} Rainbow Automation. All rights reserved."
return new class extends Migration
{
    public function up(): void
    {
        DB::table('generals')->where('copyright_text', 'like', 'Lorem ipsum%')->update([
            'copyright_text' => null,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
