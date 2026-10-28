<?php

namespace App\Support;

use App\Models\Erp\Expense;
use App\Models\Erp\Payment;
use App\Models\Erp\Student;
use App\Models\PostExtra;

// Server side versions of the CMS design helpers (fmt, badge, cn, gross, costs, totalDue)
class Erp
{
    // Sidebar of the CMS design: route => [icon, label]
    const MENU = [
        'dashboard' => ['◫', 'Dashboard'],
        'inquiries' => ['✉', 'Website Inquiries'],
        'courses' => ['▤', 'Course Management'],
        'categories' => ['◈', 'Course Categories'],
        'students' => ['♙', 'Student Admissions'],
        'batches' => ['▦', 'Batch & Schedule'],
        'trainers' => ['♧', 'Trainer Management'],
        'attendance' => ['☑', 'Attendance'],
        'fees' => ['৳', 'Fees & Receipts'],
        'expenses' => ['↗', 'Expenses & Payroll'],
        'labs' => ['⚙', 'Lab & Equipment'],
        'certificates' => ['★', 'Certificates'],
        'certificate-template' => ['★', 'Certificate Template'],
        'reports' => ['▥', 'Reports & Analytics'],
        'settings' => ['⚙', 'Settings & Access'],
    ];

    // Sidebar groups: parent menu => its sub pages (route => label); sub pages stay in MENU for the page title
    const SUBMENU = [
        'courses' => ['courses' => 'All Courses', 'categories' => 'Course Categories'],
        'certificates' => ['certificates' => 'All Certificates', 'certificate-template' => 'Certificate Template'],
    ];

    const INQUIRY_STAGES = ['New', 'Follow-up', 'Converted'];

    // Website Training Academy courses (post_extras type 4), in website order (loaded once per request)
    public static function courses(){
        if(!app()->bound('erp.courses')){
            app()->instance('erp.courses', PostExtra::where('type', 4)->where('status', '<>', 'temp')->orderBy('drag')->get());
        }
        return app('erp.courses');
    }

    // Course categories with the number of courses in each (Course Categories page)
    public static function categories(){
        $counts = static::courses()->countBy(fn ($c) => $c->course('category'));
        return collect(trainingCategories())->map(fn ($c, $key) => (object) [
            'key' => $key, 'label' => $c['label'], 'filter' => $c['filter'] ?? $c['label'], 'color' => $c['color'], 'courses' => $counts[$key] ?? 0,
        ])->values();
    }

    public static function course($id){
        return static::courses()->firstWhere('id', $id);
    }

    // Course name, or an em dash when the course no longer exists
    public static function courseName($id){
        return static::course($id)?->course('title') ?: '—';
    }

    public static function courseCode($course){
        return 'CRS'.str_pad($course->id, 3, '0', STR_PAD_LEFT);
    }

    public static function courseCategory($course){
        return trainingCategories()[$course->course('category')]['label'] ?? $course->course('category');
    }

    // "30 Hours" → 30
    public static function courseHours($course){
        return (int) preg_replace('/[^0-9]/', '', (string) $course->course('duration'));
    }

    public static function courseFee($course){
        $fee = $course?->course('fee');
        return is_numeric($fee) ? (float) $fee : 0;
    }

    // ৳ 1,25,000 — same grouping as toLocaleString('en-BD') in the design
    public static function fmt($amount){
        return '৳ '.static::number($amount);
    }

    public static function number($amount){
        $amount = (float) $amount;
        $negative = $amount < 0;
        $amount = abs($amount);
        $int = (string) floor($amount);
        $fraction = rtrim(substr(number_format($amount - floor($amount), 2, '.', ''), 2), '0');
        if(strlen($int) > 3){
            $last = substr($int, -3);
            $rest = substr($int, 0, -3);
            $int = implode(',', str_split(strrev($rest), 2));
            $int = strrev($int).','.$last;
        }
        return ($negative ? '-' : '').$int.($fraction !== '' ? '.'.$fraction : '');
    }

    // Coloured status pill of the design
    public static function badge($status){
        $variant = preg_match('/paid|present|available|converted|completed/i', $status) ? 'green'
            : (preg_match('/due|absent|cancelled/i', $status) ? 'red' : 'orange');
        return '<span class="pill '.$variant.'">'.e($status).'</span>';
    }

    public static function gross(){
        return (float) Payment::sum('amount');
    }

    public static function costs(){
        return (float) Expense::sum('amount');
    }

    public static function totalDue(){
        return Student::with('payments')->get()->sum(fn ($s) => $s->due());
    }

    // Website course inquiries (post_extras type 5), oldest first like the design list
    public static function inquiries(){
        return PostExtra::where('type', 5)->orderBy('id')->get();
    }

    public static function inquiryCode($inquiry){
        return 'INQ-'.str_pad($inquiry->id, 2, '0', STR_PAD_LEFT);
    }

    // Minimal one-sheet .xlsx (inline strings, numbers as numbers, bold header row); returns a temp file path
    public static function xlsx(array $rows, $sheet = 'Sheet1'){
        $col = function ($i) { $s = ''; for($i++; $i > 0; $i = intdiv($i - 1, 26)) $s = chr(65 + ($i - 1) % 26).$s; return $s; };
        $xml = '';
        foreach(array_values($rows) as $r => $row){
            $xml .= '<row r="'.($r + 1).'">';
            foreach(array_values($row) as $c => $v){
                $ref = $col($c).($r + 1).'"'.($r == 0 ? ' s="1"' : '');
                $xml .= is_int($v) || is_float($v)
                    ? '<c r="'.$ref.'><v>'.$v.'</v></c>'
                    : '<c r="'.$ref.' t="inlineStr"><is><t xml:space="preserve">'.htmlspecialchars((string) $v, ENT_XML1 | ENT_QUOTES, 'UTF-8').'</t></is></c>';
            }
            $xml .= '</row>';
        }
        $sheet = htmlspecialchars(mb_substr(str_replace(['\\', '/', '?', '*', '[', ']', ':'], '', $sheet), 0, 31) ?: 'Sheet1', ENT_XML1);
        $ns = 'xmlns="http://schemas.openxmlformats.org/spreadsheetml/2006/main"';
        $rel = 'http://schemas.openxmlformats.org/officeDocument/2006/relationships';
        $head = '<?xml version="1.0" encoding="UTF-8" standalone="yes"?>';
        $file = tempnam(sys_get_temp_dir(), 'xlsx');
        $zip = new \ZipArchive;
        $zip->open($file, \ZipArchive::OVERWRITE);
        $zip->addFromString('[Content_Types].xml', $head.'<Types xmlns="http://schemas.openxmlformats.org/package/2006/content-types"><Default Extension="rels" ContentType="application/vnd.openxmlformats-package.relationships+xml"/><Default Extension="xml" ContentType="application/xml"/><Override PartName="/xl/workbook.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet.main+xml"/><Override PartName="/xl/worksheets/sheet1.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.worksheet+xml"/><Override PartName="/xl/styles.xml" ContentType="application/vnd.openxmlformats-officedocument.spreadsheetml.styles+xml"/></Types>');
        $zip->addFromString('_rels/.rels', $head.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="'.$rel.'/officeDocument" Target="xl/workbook.xml"/></Relationships>');
        $zip->addFromString('xl/workbook.xml', $head.'<workbook '.$ns.' xmlns:r="'.$rel.'"><sheets><sheet name="'.$sheet.'" sheetId="1" r:id="rId1"/></sheets></workbook>');
        $zip->addFromString('xl/_rels/workbook.xml.rels', $head.'<Relationships xmlns="http://schemas.openxmlformats.org/package/2006/relationships"><Relationship Id="rId1" Type="'.$rel.'/worksheet" Target="worksheets/sheet1.xml"/><Relationship Id="rId2" Type="'.$rel.'/styles" Target="styles.xml"/></Relationships>');
        $zip->addFromString('xl/styles.xml', $head.'<styleSheet '.$ns.'><fonts count="2"><font><sz val="11"/><name val="Calibri"/></font><font><b/><sz val="11"/><name val="Calibri"/></font></fonts><fills count="2"><fill><patternFill patternType="none"/></fill><fill><patternFill patternType="gray125"/></fill></fills><borders count="1"><border/></borders><cellStyleXfs count="1"><xf/></cellStyleXfs><cellXfs count="2"><xf/><xf fontId="1" applyFont="1"/></cellXfs></styleSheet>');
        $zip->addFromString('xl/worksheets/sheet1.xml', $head.'<worksheet '.$ns.'><sheetData>'.$xml.'</sheetData></worksheet>');
        $zip->close();
        return $file;
    }
}
