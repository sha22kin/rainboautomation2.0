<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// The Home page "Our Core Services" cards now come from Admin → Services Page (core service cards).
// Copy the Home page design texts (badge, description, points, link, colour, order) onto those cards
// and add the "Training Academy" card, which is shown on the Home page only.
return new class extends Migration
{
    private $home = [
        'Laboratory Solutions' => [
            'badge' => 'Engineering Laboratory Equipment',
            'home_description' => 'Supply of engineering laboratory equipment for polytechnic, university, TVET and institutional projects with proper installation and user training.',
            'points' => ['Electrical, Electronics & Mechanical Lab', 'Industrial Automation & Robotics Trainers', 'Installation, Testing & Commissioning'],
            'home_color' => '#2eb453', 'home_order' => 1, 'link_page' => null, 'link_text' => null,
        ],
        'e-GP Tender Projects' => [
            'badge' => 'Institutional Procurement',
            'home_description' => 'Professional participation in government and institutional tender-based procurement with compliant documentation, engineering equipment supply and execution support.',
            'points' => ['Technical Specification Matching', 'Government & Institutional Supply', 'Delivery, Documentation & Handover'],
            'home_color' => '#ec2331', 'home_order' => 2, 'link_page' => null, 'link_text' => null,
        ],
        'Solar System Complete Solution' => [
            'badge' => 'Renewable Energy Solutions',
            'home_description' => 'Rainbow Automation also provides complete solar solution services for homes, institutions, farms, industry and commercial facilities.',
            'points' => ['On-Grid, Off-Grid & Hybrid Systems', 'Rooftop, Shed & Industrial Solar Design', 'Net Metering, Structure & Installation'],
            'home_color' => '#2684d9', 'home_order' => 3, 'link_page' => null, 'link_text' => null,
        ],
        'Industrial Solution' => [
            'badge' => 'Industrial Automation & Control Systems',
            'home_description' => 'End-to-end industrial automation, intelligent control and machine integration for factories, training laboratories and modern production facilities.',
            'points' => ['PLC, VFD, HMI & SCADA Integration', 'Servo Drive, Machine Control & Robotics', 'Industrial IIoT, Commissioning & Support'],
            'home_color' => '#8b28a4', 'home_order' => 4, 'link_page' => 'industrial', 'link_text' => 'Explore Industrial Solutions',
        ],
    ];

    private $training = [
        'icon' => '✦',
        'title' => 'Training Academy',
        'description' => 'Practical engineering and technical training for students, professionals and industrial teams across automation, design and advanced manufacturing.',
        'color' => '#f3a51b',
        'show_services' => false,
        'show_home' => true,
        'badge' => 'Professional Engineering Skill Development',
        'home_description' => null,
        'points' => ['PLC, VFD, HMI, SCADA & IIoT', 'Electro-Pneumatic & Electro-Hydraulic', 'SolidWorks, CAD/CAM, CNC & Mechatronics'],
        'link_page' => 'training',
        'link_url' => null,
        'link_text' => 'Explore All {count} Courses',
        'home_color' => '#f3a51b',
        'home_order' => 5,
    ];

    public function up(): void
    {
        $page = DB::table('posts')->where('type', 0)->where('template', 'Services')->orderBy('id')->first();
        if (!$page) {
            return;
        }

        $cards = DB::table('post_extras')->where('type', 3)->where('src_id', $page->id)->get();
        foreach ($cards as $card) {
            $data = json_decode($card->content ?: '[]', true) ?: [];
            if (array_key_exists('show_home', $data) || !isset($this->home[$data['title'] ?? $card->name])) {
                continue; // already set up in the admin, or a card that is not on the Home page design
            }
            $data += ['show_services' => true, 'show_home' => true, 'link_url' => null] + $this->home[$data['title'] ?? $card->name];
            DB::table('post_extras')->where('id', $card->id)->update([
                'content' => json_encode($data, JSON_UNESCAPED_UNICODE),
                'updated_at' => now(),
            ]);
        }

        $hasTraining = $cards->contains(function ($card) {
            return (json_decode($card->content ?: '[]', true)['title'] ?? $card->name) == 'Training Academy';
        });
        if (!$hasTraining) {
            DB::table('post_extras')->insert([
                'src_id' => $page->id,
                'type' => 3,
                'name' => $this->training['title'],
                'content' => json_encode($this->training, JSON_UNESCAPED_UNICODE),
                'drag' => ($cards->max('drag') ?? -1) + 1,
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
