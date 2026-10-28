<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Move the "Solar Power Solutions" cards of the Solar Solution design into Admin → Solar Solution.
return new class extends Migration
{
    private $cards = [
        ['☀', 'On-Grid Solar System', 'Grid-interactive rooftop solar design, wiring, inverter integration and monitoring.', '#29b65d'],
        ['🔋', 'Hybrid Solar System', 'Solar-plus-battery systems with integrated power management and protection.', '#ffaf24'],
        ['🌙', 'Off-Grid Solar System', 'Stand-alone energy solutions for remote or backup supply applications.', '#8755be'],
        ['🏢', 'Commercial & Industrial Solar', 'Solar power solutions for factories, campuses, farms and office buildings.', '#2683d5'],
        ['📐', 'Solar System Design', 'Site survey, load assessment, module layout, shade study and electrical diagrams.', '#f04469'],
        ['🛠', 'Installation & Commissioning', 'Mounting, cabling, monitoring setup, testing and technical handover.', '#22a5b3'],
    ];

    public function up(): void
    {
        $page = DB::table('posts')->where('type', 0)->where('template', 'Solar Solution')->orderBy('id')->first();
        if (!$page || DB::table('post_extras')->where('type', 3)->where('src_id', $page->id)->exists()) {
            return;
        }

        foreach ($this->cards as $i => [$icon, $title, $description, $color]) {
            DB::table('post_extras')->insert([
                'src_id' => $page->id,
                'type' => 3,
                'name' => $title,
                'content' => json_encode(compact('icon', 'title', 'description', 'color'), JSON_UNESCAPED_UNICODE),
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
