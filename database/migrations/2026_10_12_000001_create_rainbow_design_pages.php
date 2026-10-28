<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Make sure every Rainbow Automation design template has a page in Admin → Pages.
return new class extends Migration
{
    private $pages = [
        'Front Page' => ['Home', 'home'],
        'About Us' => ['About Us', 'about-us'],
        'Services' => ['Services', 'services'],
        'Solar Solution' => ['Solar Solution', 'solar-solution'],
        'Training Academy' => ['Training Academy', 'training-academy'],
        'Industrial Solution' => ['Industrial Solution', 'industrial-solution'],
        'Contact Us' => ['Contact Us', 'contact-us'],
    ];

    public function up(): void
    {
        foreach ($this->pages as $template => [$name, $slug]) {
            $pages = DB::table('posts')->where('type', 0);

            if ((clone $pages)->where('template', $template)->exists()) {
                continue;
            }

            // Reuse an existing page with the same slug (e.g. the old "Services" page), otherwise create it.
            $existing = (clone $pages)->where('slug', $slug)->first();
            if ($existing) {
                DB::table('posts')->where('id', $existing->id)->update([
                    'template' => $template,
                    'status' => 'active',
                    'updated_at' => now(),
                ]);
                continue;
            }

            DB::table('posts')->insert([
                'name' => $name,
                'slug' => $slug,
                'type' => 0,
                'template' => $template,
                'search_key' => $name,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('posts')->where('type', 0)
            ->whereIn('template', ['Services', 'Solar Solution', 'Training Academy', 'Industrial Solution'])
            ->update(['template' => null]);
    }
};
