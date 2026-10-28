<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// Move the 19 courses of the Training Academy design into Admin → Training Courses.
return new class extends Migration
{
    private $courses = [
        ['category' => 'automation', 'title' => 'PLC Programming & Industrial Control System', 'description' => 'Industrial control logic, PLC fundamentals, wiring concepts and practical industrial automation training.', 'duration' => '30 Hours', 'fee' => 12000,
         'image' => 'https://images.unsplash.com/photo-1563770660941-10a636076f70?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'PLC control panel and industrial automation',
         'modules' => ['PLC hardware, architecture and electrical safety', 'Digital I/O wiring, sensors and actuators', 'Ladder logic, timers, counters and interlocking', 'Motor starters and industrial sequencing', 'Fault diagnosis, testing and mini-project']],
        ['category' => 'automation', 'title' => 'VFD & AC Motor Speed Control System', 'description' => 'Motor drive control, parameter setting, speed variation and industrial drive application training.', 'duration' => '20 Hours', 'fee' => 8000,
         'image' => 'https://images.unsplash.com/photo-1581092160607-ee22621dd758?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'VFD and motor control training setup',
         'modules' => ['Three-phase motor and drive fundamentals', 'VFD wiring, safety and parameter setup', 'Speed control, acceleration and deceleration', 'Forward/reverse, multi-speed and protection', 'PLC–VFD integration and troubleshooting']],
        ['category' => 'automation', 'title' => 'SCADA System & Industrial Process Monitoring', 'description' => 'Supervisory control, industrial process visualization and modern plant monitoring concepts.', 'duration' => '25 Hours', 'fee' => 12000,
         'image' => 'https://images.unsplash.com/photo-1581092919535-7146ff1a5905?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'SCADA monitoring screens and process control',
         'modules' => ['SCADA architecture, tags and communication', 'Process mimic and HMI/SCADA screens', 'Alarms, trends and historical data logging', 'PLC communication and process simulation', 'Monitoring dashboard and final exercise']],
        ['category' => 'automation', 'title' => 'Servo Motor & Motion Control System', 'description' => 'Servo technology, precise motion control and automation-focused motion system integration.', 'duration' => '25 Hours', 'fee' => 12000,
         'image' => 'https://images.unsplash.com/photo-1565043589221-1a6fd9ae45c7?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Servo motor and motion system components',
         'modules' => ['Servo drive, motor and encoder fundamentals', 'Wiring, commissioning and parameter tuning', 'Position, speed and torque control modes', 'Homing, indexing and motion sequences', 'Motion diagnostics and system integration']],
        ['category' => 'automation', 'title' => 'HMI Programming & Operator Interface Design', 'description' => 'Touch interface design, operator panel configuration and HMI integration in control systems.', 'duration' => '20 Hours', 'fee' => 8000,
         'image' => 'https://images.unsplash.com/photo-1581092580497-e0d23cbdf1dc?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'HMI operator interface and industrial screen',
         'modules' => ['HMI hardware and screen navigation design', 'Data tags and PLC connectivity', 'Buttons, indicators, trends and alarms', 'Recipes, access levels and screen testing', 'Operator interface mini-project']],
        ['category' => 'automation', 'title' => 'Industrial Internet of Things (IIoT) & Smart Monitoring', 'description' => 'Smart connectivity, remote monitoring and Industry 4.0 oriented industrial data integration.', 'duration' => '25 Hours', 'fee' => 12000,
         'image' => 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'IIoT smart monitoring and connectivity',
         'modules' => ['IIoT architecture, networks and protocols', 'Sensors, gateways and field data acquisition', 'MQTT/Modbus and cloud data transfer concepts', 'Remote dashboard and alarm configuration', 'Security, troubleshooting and smart monitoring project']],
        ['category' => 'fluid', 'title' => 'Electro-Pneumatic Control & Automation System', 'description' => 'Actuators, valves, sequencing and practical automation using pneumatic control methods.', 'duration' => '25 Hours', 'fee' => 10000,
         'image' => 'https://images.unsplash.com/photo-1581092334651-ddf26d9a09d0?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Pneumatic control valves and automation trainer',
         'modules' => ['Compressed air preparation and safety', 'Pneumatic actuators, valves and circuits', 'Solenoid control and electrical interlocks', 'Sequential motion and sensor feedback', 'Electro-pneumatic fault finding']],
        ['category' => 'fluid', 'title' => 'Electro-Hydraulic Control & Automation System', 'description' => 'Hydraulic control, electro-hydraulic circuits and industrial machine actuator applications.', 'duration' => '30 Hours', 'fee' => 12000,
         'image' => 'https://images.unsplash.com/photo-1581092588425-c46a1da2a7b4?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Hydraulic control system and actuator training',
         'modules' => ['Hydraulic power units and safety', 'Cylinders, directional and pressure valves', 'Electro-hydraulic electrical control', 'Actuator sequencing and speed adjustment', 'System testing and troubleshooting']],
        ['category' => 'design', 'title' => 'SOLIDWORKS 3D Mechanical Design & Assembly Modeling', 'description' => '3D design, part modeling and assembly workflows for mechanical engineering design practice.', 'duration' => '60 Hours', 'fee' => 25000,
         'image' => 'https://images.unsplash.com/photo-1581092921461-eab62e97a780?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Mechanical CAD design and 3D assembly modeling',
         'modules' => ['Sketching and parametric part modeling', 'Features, configurations and design intent', 'Assemblies, mates and interference checks', 'Engineering drawings and BOM', 'Capstone assembly modeling exercise']],
        ['category' => 'design', 'title' => 'AutoCAD 2D & 3D Engineering Drawing', 'description' => '2D drafting and 3D drawing skills for engineering drawing and drafting-based applications.', 'duration' => '40 Hours', 'fee' => 12000,
         'image' => 'https://images.unsplash.com/photo-1451187580459-43490279c0fa?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Engineering drawing and AutoCAD drafting workspace',
         'modules' => ['AutoCAD workspace, units and layers', '2D drawing, modification and dimensioning', 'Blocks, layouts and plotting', '3D objects and basic modeling tools', 'Engineering drawing project']],
        ['category' => 'cnc', 'title' => 'CAD/CAM Programming & CNC Manufacturing Technology', 'description' => 'Digital manufacturing workflow, CAM programming and CNC production-oriented training.', 'duration' => '60 Hours', 'fee' => 30000,
         'image' => 'https://images.unsplash.com/photo-1581093458791-9d42e0f4e212?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'CAD CAM programming and CNC manufacturing machine',
         'modules' => ['CAD-to-CAM process planning and manufacturing workflow', 'Toolpath creation and machining strategies', 'Tool selection and cutting parameters', 'Post-processing and G-code fundamentals', 'CNC setup and verification exercise']],
        ['category' => 'design', 'title' => 'SOLIDWORKS Simulation & Product Development', 'description' => 'Simulation techniques and product development workflow for design validation and innovation.', 'duration' => '40 Hours', 'fee' => 18000,
         'image' => 'https://images.unsplash.com/photo-1532614338840-ab30cf10ed36?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Product development and simulation concept',
         'modules' => ['Material properties and simulation fundamentals', 'Boundary conditions and meshing', 'Static structural analysis and interpretation', 'Design validation and optimization', 'Simulation-led product improvement case study']],
        ['category' => 'design', 'title' => '3D Printing & Rapid Prototyping Technology', 'description' => 'Rapid prototyping concepts, 3D printing workflow and modern product development support.', 'duration' => '30 Hours', 'fee' => 10000,
         'image' => 'https://images.unsplash.com/photo-1517077304055-6e89abbf09b0?auto=format&fit=crop&w=1200&q=80',
         'alt' => '3D printing and rapid prototyping machine',
         'modules' => ['3D model preparation and export formats', 'FDM/FFF printing principles and materials', 'Slicer settings and print preparation', 'Printer calibration, operation and safety', 'Post-processing and prototype inspection']],
        ['category' => 'cnc', 'title' => 'CNC Lathe Programming, Operation & Machining Technology', 'description' => 'Lathe setup, CNC programming, operation process and machining technology in workshop practice.', 'duration' => '80 Hours', 'fee' => 30000,
         'image' => 'https://images.unsplash.com/photo-1530124566582-a618bc2615dc?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'CNC lathe machine in workshop',
         'modules' => ['CNC lathe components and machine safety', 'Work offsets, tools and machining setup', 'Turning cycles and G/M-code programming', 'Simulation, dry run and part machining', 'Dimensional inspection and troubleshooting']],
        ['category' => 'cnc', 'title' => 'CNC Milling Programming, Operation & Machining Technology', 'description' => 'Milling setup, program execution and practical machining skills for manufacturing applications.', 'duration' => '80 Hours', 'fee' => 35000,
         'image' => 'https://images.unsplash.com/photo-1581092795360-fd1ca04f0952?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'CNC milling machine and machining process',
         'modules' => ['CNC milling components and machine safety', 'Workholding, tooling and work coordinates', 'Milling strategies, cycles and G/M codes', 'Program proving and machine operation', 'Part inspection and process refinement']],
        ['category' => 'integrated', 'title' => 'Integrated Industrial Automation & Industry 4.0 Professional Training', 'description' => 'Comprehensive industrial automation program covering advanced systems and modern smart factory concepts.', 'duration' => '240 Hours', 'fee' => 65000,
         'image' => 'https://images.unsplash.com/photo-1563770660941-10a636076f70?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Integrated industrial automation and industry 4.0 training',
         'modules' => ['PLC, HMI, VFD and SCADA integration', 'Servo drives, field devices and industrial networking', 'Electro-pneumatics and electro-hydraulics', 'IIoT monitoring and Industry 4.0 concepts', 'System integration, commissioning and capstone project']],
        ['category' => 'integrated', 'title' => 'Integrated CAD/CAM & Digital Manufacturing Professional Training', 'description' => 'Integrated digital manufacturing pathway with CAD, CAM and production-oriented engineering workflow.', 'duration' => '260 Hours', 'fee' => 80000,
         'image' => 'https://images.unsplash.com/photo-1581092918056-0c4c3acd3789?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Digital manufacturing and CAD CAM workflow',
         'modules' => ['CAD modeling and digital product definition', 'CAM process planning and toolpath optimization', 'CNC programming and simulation', 'Manufacturing documentation and QA', 'End-to-end digital manufacturing project']],
        ['category' => 'integrated', 'title' => 'Integrated CNC Lathe & CNC Milling Professional Training', 'description' => 'Combined lathe and milling workshop-focused CNC professional training program.', 'duration' => '200 Hours', 'fee' => 55000,
         'image' => 'https://images.unsplash.com/photo-1565043589221-1a6fd9ae45c7?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Integrated CNC lathe and milling training',
         'modules' => ['Combined lathe and milling machine safety', 'G-code programming and CNC simulation', 'Setup, tools, offsets and tooling selection', 'Turning and milling manufacturing operations', 'Inspection, troubleshooting and final production job']],
        ['category' => 'integrated', 'title' => 'Advanced Mechatronics & Smart Manufacturing Technology', 'description' => 'Modern mechatronics, smart systems and manufacturing technology for future-focused engineering skills.', 'duration' => '100 Hours', 'fee' => 45000,
         'image' => 'https://images.unsplash.com/photo-1485827404703-89b55fcc595e?auto=format&fit=crop&w=1200&q=80',
         'alt' => 'Advanced mechatronics and smart manufacturing robotics',
         'modules' => ['Mechatronic subsystems and integrated control', 'Sensors, actuators and intelligent automation', 'PLC, servo and industrial communication', 'Robotics and smart production concepts', 'Digital monitoring and system capstone']],
    ];

    public function up(): void
    {
        $page = DB::table('posts')->where('type', 0)->where('template', 'Training Academy')->orderBy('id')->first();
        if (!$page || DB::table('post_extras')->where('type', 4)->where('src_id', $page->id)->exists()) {
            return;
        }

        foreach ($this->courses as $i => $course) {
            DB::table('post_extras')->insert([
                'src_id' => $page->id,
                'type' => 4,
                'name' => $course['title'],
                'content' => json_encode($course, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
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
