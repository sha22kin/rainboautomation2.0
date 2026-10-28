<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Move the three addresses of the Contact Us design into Admin → Pages → Contact Us → Contact Locations.
return new class extends Migration
{
    private $locations = [
        [
            'label' => 'CORPORATE OFFICE',
            'title' => 'Rainbow Automation · Banglamotor',
            'address' => 'R.K Tower (10th Floor), 86/1, Bir Uttam C.R Datto Road, Banglamotor, Dhaka 1205, Bangladesh',
            'phones' => ['+880 1711-421693', '+880 1977-246269'],
            'email' => 'info@rainbow-automation.com',
            'email_note' => null,
        ],
        [
            'label' => 'TRAINING LOCATION – 1',
            'title' => 'Rainbow Automation – Panthapath',
            'address' => 'House # 56/6 (2nd Floor), Rasel Square, West Panthapath, Dhaka-1205, Bangladesh',
            'phones' => [],
            'email' => 'info@rainbow-automation.com',
            'email_note' => 'General enquiries:',
        ],
        [
            'label' => 'TRAINING LOCATION – 2',
            'title' => 'Rainbow Automation · Mirpur',
            'address' => 'Plot No. 17, Road No. 01, Block B, Mirpur 6, Dhaka 1216, Bangladesh',
            'phones' => ['+880 1711-421693', '+880 1711-421695'],
            'email' => 'info@rainbow-automation.com',
            'email_note' => null,
        ],
    ];

    public function up(): void
    {
        $page = DB::table('posts')->where('type', 0)->where('template', 'Contact Us')->orderBy('id')->first();
        if (!$page || DB::table('post_extras')->where('type', 2)->where('src_id', $page->id)->exists()) {
            return;
        }

        foreach ($this->locations as $i => $location) {
            DB::table('post_extras')->insert([
                'src_id' => $page->id,
                'type' => 2,
                'name' => $location['label'],
                'content' => json_encode(array_merge(array_diff_key($location, ['label' => 1]), ['map' => null]), JSON_UNESCAPED_UNICODE),
                'drag' => $i,
                'status' => 'active',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        //
    }
};
