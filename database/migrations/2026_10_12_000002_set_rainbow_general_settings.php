<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Replace the previous site's (NLFB) branding in Admin → Setting → General with Rainbow Automation.
// Settings already changed to something else in the admin panel are left alone.
return new class extends Migration
{
    public function up(): void
    {
        $general = DB::table('generals')->first();
        if (!$general || ($general->title && $general->title != 'National Liver Foundation of Bangladesh')) {
            return;
        }

        DB::table('generals')->where('id', $general->id)->update([
            'title' => 'Rainbow Automation',
            'subtitle' => 'Engineering • Training • Solar • Projects',
            'logo' => 'medies/rainbow-automation-logo.png',
            'favicon' => 'medies/rainbow-automation-logo.png',
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        //
    }
};
