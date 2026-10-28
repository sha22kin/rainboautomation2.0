<?php

namespace App\Support;

use App\Models\Erp\Certificate;
use App\Models\Erp\Setting;

// Certificate design shared by the template editor (Certificates → Certificate Template) and certificate printing.
// Every text is editable and saved as JSON in erp_settings.certificate_template; {placeholders} are filled per certificate.
class CertificateTemplate
{
    // field => [label, default text, input type]
    const FIELDS = [
        'org_name' => ['Organization Name', 'RAINBOW AUTOMATION', 'text'],
        'org_tagline' => ['Organization Tagline', 'Industrial Automation Training Academy', 'text'],
        'title' => ['Certificate Title', 'Certificate', 'text'],
        'subtitle' => ['Certificate Subtitle', 'of Completion', 'text'],
        'intro' => ['Intro Line', 'This is to certify that', 'text'],
        'name_line' => ['Name Line (large)', '{student}', 'text'],
        'body' => ['Main Text', 'has successfully completed the professional training program {course} ({duration}) at {branch} and achieved grade {grade}.', 'textarea'],
        'points_line' => ['Points Line (hidden when no GPA / CGPA)', 'with {points}', 'text'],
        'note' => ['Date Line', 'Awarded on {issued}', 'text'],
        'sign1_name' => ['Left Signature — Name', 'Engr. Rahim Uddin', 'text'],
        'sign1_title' => ['Left Signature — Title', 'Lead Trainer', 'text'],
        'sign2_name' => ['Right Signature — Name', 'Managing Director', 'text'],
        'sign2_title' => ['Right Signature — Title', 'Rainbow Automation', 'text'],
        'footer' => ['Footer Text', 'Certificate No. {code}  ·  Student ID {student_id}', 'text'],
        'color' => ['Accent Colour', '#1f5fa8', 'color'],
    ];

    // Placeholder => description (shown in the editor)
    const PLACEHOLDERS = [
        '{student}' => 'Student name', '{student_id}' => 'Student ID', '{course}' => 'Course name', '{duration}' => 'Course duration',
        '{grade}' => 'Result / grade', '{points}' => 'GPA / CGPA that are filled', '{gpa}' => 'GPA point', '{cgpa}' => 'CGPA point', '{issued}' => 'Issue date', '{code}' => 'Certificate number', '{batch}' => 'Batch code', '{branch}' => 'Training center',
    ];

    public static function get(){
        $saved = json_decode((string) Setting::get('certificate_template'), true) ?: [];
        $template = [];
        foreach(self::FIELDS as $key => [, $default]){
            $template[$key] = array_key_exists($key, $saved) ? (string) $saved[$key] : $default;
        }
        return $template;
    }

    public static function save(array $values){
        $template = [];
        foreach(self::FIELDS as $key => [, $default]){
            $template[$key] = trim((string) ($values[$key] ?? ''));
        }
        Setting::put('certificate_template', json_encode($template, JSON_UNESCAPED_UNICODE));
    }

    public static function reset(){
        Setting::put('certificate_template', null);
    }

    // Placeholder values of one certificate
    public static function values(Certificate $c){
        $course = Erp::course($c->course_id);
        return [
            '{student}' => $c->student?->name ?? '—',
            '{student_id}' => $c->student?->code ?? '—',
            '{course}' => Erp::courseName($c->course_id),
            '{duration}' => $course?->course('duration') ?: '—',
            '{grade}' => $c->grade,
            '{points}' => $c->points(),
            '{gpa}' => $c->gpa !== null ? number_format($c->gpa, 2) : '',
            '{cgpa}' => $c->cgpa !== null ? number_format($c->cgpa, 2) : '',
            '{issued}' => $c->issued?->format('d F Y') ?? '—',
            '{code}' => $c->code,
            '{batch}' => $c->student?->batch?->code ?? '—',
            '{branch}' => Setting::get('branch', 'Training Center'),
        ];
    }

    // Values used by the editor preview
    public static function sample(){
        return [
            '{student}' => 'Student Name', '{student_id}' => 'STU-1001', '{course}' => 'PLC Programming & Industrial Control System',
            '{duration}' => '30 Hours', '{grade}' => 'A+', '{points}' => 'GPA 3.75 · CGPA 3.80', '{gpa}' => '3.75', '{cgpa}' => '3.80', '{issued}' => now()->format('d F Y'), '{code}' => 'CERT-01', '{batch}' => 'BAT-01',
            '{branch}' => Setting::get('branch', 'Training Center'),
        ];
    }

    public static function fill($text, array $values){
        return strtr((string) $text, $values);
    }
}
