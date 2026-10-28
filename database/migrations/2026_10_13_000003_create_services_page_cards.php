<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Move the cards of the Services design into Admin → Services Page:
// "Our Core Engineering Services" (type 3) and "What We Can Support" (type 12).
return new class extends Migration
{
    private $cards = [
        3 => [
            ['✦', 'Laboratory Solutions', 'Engineering laboratory trainers, instrumentation, equipment supply, installation and commissioning.', '#2eb454'],
            ['✦', 'e-GP Tender Projects', 'Government and institutional tender participation, technical specifications, documentation and supply.', '#ffad26'],
            ['✦', 'Industrial Solution', 'Industrial PLC, VFD, HMI, SCADA, Servo and IIoT system design and integration.', '#8b28a4'],
            ['✦', 'Solar System Complete Solution', 'On-grid, off-grid and hybrid solar project design, supply, installation and commissioning.', '#287dcc'],
        ],
        12 => [
            ['✦', 'Technical Consultation', 'Project scoping, equipment selection, site review and technical proposals.', '#287dcc'],
            ['✦', 'System Integration', 'Control panels, industrial communication, automation and energy-system integration.', '#8b28a4'],
            ['✦', 'Installation & Training', 'Equipment installation, functional testing, commissioning and practical orientation.', '#2eb454'],
        ],
    ];

    public function up(): void
    {
        $page = DB::table('posts')->where('type', 0)->where('template', 'Services')->orderBy('id')->first();
        if (!$page) {
            return;
        }

        foreach ($this->cards as $type => $cards) {
            if (DB::table('post_extras')->where('type', $type)->where('src_id', $page->id)->exists()) {
                continue;
            }
            foreach ($cards as $i => [$icon, $title, $description, $color]) {
                DB::table('post_extras')->insert([
                    'src_id' => $page->id,
                    'type' => $type,
                    'name' => $title,
                    'content' => json_encode(compact('icon', 'title', 'description', 'color'), JSON_UNESCAPED_UNICODE),
                    'drag' => $i,
                    'status' => 'active',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        //
    }
};
