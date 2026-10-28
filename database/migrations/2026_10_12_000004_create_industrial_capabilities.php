<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Move the "Industrial Automation Capabilities" cards of the Industrial Solution design into Admin → Industrial Capabilities.
return new class extends Migration
{
    private $cards = [
        ['⚙', 'PLC Programming & Control', 'Machine sequencing, interlocks, sensors, actuators and automation control.', '#29b65d'],
        ['🖥', 'HMI Interface Development', 'Operator screens, process visualization and alarms.', '#2683d5'],
        ['📊', 'SCADA Integration', 'Supervisory monitoring, trends, reports and system communication.', '#8847bd'],
        ['🔄', 'VFD Motor Control', 'Speed control, motor drives, protection and industrial motor integration.', '#ffae22'],
        ['🎯', 'Servo & Motion Control', 'Servo commissioning, precise motion and multi-axis control concepts.', '#f04469'],
        ['📡', 'Industrial IIoT Systems', 'Gateways, field data capture and remote monitoring dashboards.', '#18a4a7'],
        ['🦾', 'Robotics & Mechatronics', 'Robotic systems, motion integration and smart factory applications.', '#25ae7b'],
        ['🔧', 'Pneumatic & Hydraulic', 'Electro-pneumatic and electro-hydraulic sequencing, control and troubleshooting.', '#3286d8'],
        ['🛡', 'Commissioning & Support', 'Panel testing, fault diagnostics, technical documentation and operator training.', '#d28d19'],
    ];

    public function up(): void
    {
        $page = DB::table('posts')->where('type', 0)->where('template', 'Industrial Solution')->orderBy('id')->first();
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
