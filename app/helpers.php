<?php

use App\Models\General;
use App\Models\Media;
use App\Models\Country;
use App\Models\Post;
use App\Models\PostExtra;
use App\Models\Attribute;
use Carbon\Carbon;


function general(){
  return $general =General::first();
}

function websiteTitle($title=null){
  
  $hasTitle =$title?' - ':'';
  $text =general()->title;
  $text1=general()->subtitle;
  $text2=$text&&$text1?' - ':'';
  return $title.$hasTitle.$text.$text2.$text1;
}

// Rainbow Automation design pages that can be assigned from Admin → Pages → Page Template.
// key: template name, route: fixed fallback route, view: theme view
function designTemplates(){
  return [
    'Front Page' => ['route'=>'index','view'=>'index'],
    'About Us' => ['route'=>'about','view'=>'pages.about'],
    'Services' => ['route'=>'services','view'=>'pages.services'],
    'Solar Solution' => ['route'=>'solar','view'=>'pages.solar'],
    'Training Academy' => ['route'=>'training','view'=>'pages.training'],
    'Industrial Solution' => ['route'=>'industrial','view'=>'pages.industrial'],
    'Contact Us' => ['route'=>'contact','view'=>'pages.contact'],
  ];
}

// Active admin page that uses the given design template (cached per request).
function templatePage($template){
  static $pages = null;
  if($pages === null){
    $pages = Post::where('type',0)->where('status','active')
      ->whereIn('template',array_keys(designTemplates()))
      ->orderBy('id')->get()->unique('template')->keyBy('template');
  }
  return $pages->get($template);
}

// Industrial Solution / Solar Solution / Services page texts (Admin → Industrial Solution, Solar Solution, Services Page).
// Empty fields fall back to the design text of that page.
// Services has no hero chips but a second card section ("support") with a quick strip (one item per line).
function solutionDefaults($template){
  $defaults = [
    'Industrial Solution' => [
      'hero' => [
        'overline' => 'Advanced Industrial Automation',
        'title' => 'Smart Control.',
        'highlight' => 'Real Industry Performance.',
        'description' => 'Industrial automation system engineering, PLC programming, VFD drives, HMI and SCADA integration, servo motion, industrial networking, IIoT monitoring and commissioning.',
        'primary_text' => 'Discuss Automation Project ↗',
        'primary_link' => '',
        'secondary_text' => 'Explore Company →',
        'secondary_link' => '',
      ],
      'features' => [
        ['title' => 'PLC Control', 'text' => 'Reliable process logic', 'color' => '#2eb454'],
        ['title' => 'VFD Motor Drive', 'text' => 'Dynamic speed and torque control', 'color' => '#f3a51b'],
        ['title' => 'Industrial IIoT', 'text' => 'Cloud-ready monitoring', 'color' => '#287dcc'],
      ],
      'capabilities' => [
        'overline' => 'INDUSTRIAL SOLUTIONS',
        'title' => 'Industrial Automation Capabilities',
        'description' => 'Integrated control and automation services for engineering applications.',
      ],
      'workflow' => [
        'overline' => 'PROJECT EXECUTION',
        'title' => 'Reliable Engineering Workflow',
        'steps' => [
          ['title' => 'Technical Consultation', 'text' => 'Discuss machine requirements, control architecture and process constraints.'],
          ['title' => 'System Engineering', 'text' => 'Prepare I/O, hardware, electrical and communication design.'],
          ['title' => 'Integration & Testing', 'text' => 'Program, integrate and test controls with field devices.'],
          ['title' => 'Training & Support', 'text' => 'Commission, document and provide practical operator guidance.'],
        ],
      ],
      'cta' => [
        'title' => 'Automate Your Next Project',
        'description' => 'Speak with our technical team about control systems and integration.',
        'button_text' => 'Request Consultation ↗',
        'button_link' => '',
      ],
    ],
    'Solar Solution' => [
      'hero' => [
        'overline' => 'Complete Solar Power Systems',
        'title' => 'Engineering a',
        'highlight' => 'Cleaner Energy Future',
        'description' => 'End-to-end solar system consultation, technical design, equipment selection, rooftop installation, commissioning and system support for residential, commercial, institutional and industrial projects.',
        'primary_text' => 'Discuss Solar Project ↗',
        'primary_link' => '',
        'secondary_text' => 'Explore Company →',
        'secondary_link' => '',
      ],
      'features' => [
        ['title' => 'On-Grid Solar', 'text' => 'Net metering ready', 'color' => '#2eb454'],
        ['title' => 'Hybrid Backup', 'text' => 'Battery & inverter integrated', 'color' => '#f3a51b'],
        ['title' => 'Smart Monitoring', 'text' => 'Web and mobile dashboard', 'color' => '#287dcc'],
      ],
      'capabilities' => [
        'overline' => 'ENERGY EXPERTISE',
        'title' => 'Solar Power Solutions',
        'description' => 'Proposed system solutions tailored to the project requirements and site conditions.',
      ],
      'workflow' => [
        'overline' => 'OUR WORKFLOW',
        'title' => 'From Concept to Commissioning',
        'steps' => [
          ['title' => 'Site Assessment', 'text' => 'Review rooftop area, structural constraints, load and electrical integration.'],
          ['title' => 'System Design', 'text' => 'Develop proposed solar capacity, equipment selection and protection plan.'],
          ['title' => 'Implementation', 'text' => 'Coordinate mounting structure, installation and electrical integration.'],
          ['title' => 'Commissioning', 'text' => 'Test performance, hand over documentation and train operators.'],
        ],
      ],
      'cta' => [
        'title' => 'Planning a Solar Project?',
        'description' => 'Contact Rainbow Automation for a consultation and tailored proposal.',
        'button_text' => 'Request Consultation ↗',
        'button_link' => '',
      ],
    ],
    'Services' => [
      'hero' => [
        'overline' => 'ENGINEERING & TECHNICAL SERVICES',
        'title' => 'Solutions Designed for',
        'highlight' => 'Industry & Education.',
        'description' => 'Explore our laboratory, procurement, industrial automation and renewable energy capabilities, supported by technical consultation and delivery coordination.',
        'primary_text' => 'Request Consultation ↗',
        'primary_link' => '',
        'secondary_text' => 'Explore Services →',
        'secondary_link' => '',
      ],
      'features' => [],
      'capabilities' => [
        'overline' => 'SERVICE PORTFOLIO',
        'title' => 'Our Core Engineering Services',
        'description' => 'Integrated engineering and implementation capability with a focus on project-specific technical requirements.',
      ],
      'support' => [
        'overline' => 'CAPABILITIES',
        'title' => 'What We Can Support',
        'strip' => "Laboratory Equipment\nIndustrial Control\nSolar EPC\nInstitutional Procurement",
      ],
      'workflow' => [
        'overline' => 'DELIVERY PROCESS',
        'title' => 'Project Delivery Workflow',
        'steps' => [
          ['title' => 'Discover', 'text' => 'Discuss objectives, scope and compliance requirements.'],
          ['title' => 'Specify', 'text' => 'Prepare appropriate technical requirements and quotations.'],
          ['title' => 'Deliver', 'text' => 'Coordinate equipment supply and technical implementation.'],
          ['title' => 'Commission', 'text' => 'Complete testing, orientation and handover where included.'],
        ],
      ],
      'cta' => [
        'title' => 'Discuss Your Project Requirements',
        'description' => 'Tell us what you need and request an engineering consultation.',
        'button_text' => 'Contact Our Team ↗',
        'button_link' => '',
      ],
    ],
  ];
  return $defaults[$template] ?? $defaults['Industrial Solution'];
}

// Home page "Our Core Services" cards = Services page core cards marked "Show on Home page", in their home order
function homeServiceCards(){
  $page = templatePage('Services');
  if(!$page){
    return collect();
  }
  return $page->capabilityCards
    ->filter(function($card){ return $card->card('show_home'); })
    ->sortBy(function($card){ return [(int)$card->card('home_order'), $card->drag]; })
    ->values();
}

// Link of a Home page service card: a design page (by route name) or a custom URL
function serviceCardLink($card){
  $page = $card->card('link_page');
  if(!$page){
    return null;
  }
  return $page=='custom' ? ($card->card('link_url') ?: null) : pageLink($page);
}

// Plain text groups of a solution page (edited in one form); lists (chips, steps, cards) have their own pages
function solutionTextGroups($template){
  return array_values(array_diff(array_keys(solutionDefaults($template)), ['features','workflow']));
}

function solutionContent($page, $template){
  $defaults = solutionDefaults($template);
  $saved = $page && $page->sectionContent ? (json_decode($page->sectionContent->content ?: '[]', true) ?: []) : [];

  $content = [];
  foreach(solutionTextGroups($template) as $group){
    $content[$group] = $defaults[$group];
    foreach($saved[$group] ?? [] as $key => $value){
      if(array_key_exists($key, $defaults[$group]) && $value !== null && $value !== ''){
        $content[$group][$key] = $value;
      }
    }
  }
  $content['workflow'] = $defaults['workflow'];
  foreach(['overline','title'] as $key){
    if(!empty($saved['workflow'][$key])) $content['workflow'][$key] = $saved['workflow'][$key];
  }
  // Lists: a saved list (even an edited one) replaces the design list
  $content['features'] = isset($saved['features']) ? $saved['features'] : $defaults['features'];
  $content['workflow']['steps'] = isset($saved['workflow']['steps']) ? $saved['workflow']['steps'] : $defaults['workflow']['steps'];

  // Empty button links go to the design pages
  $content['hero']['primary_href'] = $content['hero']['primary_link'] ?: pageLink('contact');
  $content['hero']['secondary_href'] = $content['hero']['secondary_link'] ?: ($template=='Services' ? pageLink('services') : route('index'));
  if(isset($content['support'])){
    $content['support']['strip_items'] = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string)$content['support']['strip']))));
  }
  $content['cta']['button_href'] = $content['cta']['button_link'] ?: pageLink('contact');

  return $content;
}

// Training Academy page texts (Admin → Training Courses → Page Content). Empty fields fall back to the design text.
// {count} is replaced with the number of courses, **text** is shown bold.
function trainingDefaults(){
  return [
    'hero' => [
      'eyebrow' => 'RAINBOW AUTOMATION TRAINING ACADEMY',
      'title' => 'Build Practical Skills.',
      'highlight' => 'Engineer Your Future.',
      'description' => 'Explore {count} industry-focused courses in PLC, VFD, HMI, SCADA, IIoT, Pneumatics, Hydraulics, SolidWorks, AutoCAD, CNC, CAD/CAM and Smart Manufacturing. Select **Details** to view each proposed module outline, or choose **Inquiry** to contact the training team.',
      'button_text' => 'Explore All {count} Courses ↓',
    ],
    'chips' => [
      ['title' => 'PLC', 'text' => 'Automation'],
      ['title' => 'VFD', 'text' => 'Drive Control'],
      ['title' => 'HMI', 'text' => 'Operator Panel'],
      ['title' => 'SCADA', 'text' => 'Process View'],
      ['title' => 'CNC', 'text' => 'Manufacturing'],
      ['title' => 'IIoT', 'text' => 'Smart Monitoring'],
    ],
    'catalog' => [
      'title' => 'Our Training Programs',
      'description' => '{count} professional programs · Course durations shown · Fees available on inquiry',
    ],
  ];
}

function trainingContent($page=null){
  $defaults = trainingDefaults();
  $saved = $page && $page->trainingContent ? (json_decode($page->trainingContent->content ?: '[]', true) ?: []) : [];

  $content = [];
  foreach(['hero','catalog'] as $group){
    $content[$group] = $defaults[$group];
    foreach($saved[$group] ?? [] as $key => $value){
      if(array_key_exists($key, $defaults[$group]) && $value !== null && $value !== ''){
        $content[$group][$key] = $value;
      }
    }
  }
  $content['chips'] = isset($saved['chips']) ? $saved['chips'] : $defaults['chips'];
  return $content;
}

// Escaped page text with {count} filled in and **bold** marks turned into <b>
function pageText($text, $count=null){
  $html = e(str_replace('{count}', (string)$count, (string)$text));
  return preg_replace('/\*\*(.+?)\*\*/', '<b>$1</b>', $html);
}

// Link of a design page by its route name: the admin page slug when one exists, otherwise the fixed route.
function pageLink($routeName){
  if($routeName=='index'){
    return route('index');
  }
  foreach(designTemplates() as $template=>$config){
    if($config['route']==$routeName && $page=templatePage($template)){
      return route('pageView',$page->slug);
    }
  }
  return route($routeName);
}

// Training Academy course categories: filter button key => card label, filter label, accent colour.
// Managed in CMS → Course Management (saved as JSON in erp_settings.course_categories, shared by CMS and website);
// the five built-in categories are used until the list is saved there.
function trainingCategories(){
  static $categories = null;
  if($categories === null){
    $categories = [
      'automation' => ['label'=>'Industrial Automation','filter'=>'Automation','color'=>'#287dcc'],
      'fluid' => ['label'=>'Fluid Power','filter'=>'Fluid Power','color'=>'#ed3b56'],
      'design' => ['label'=>'CAD & Product Design','filter'=>'CAD & Design','color'=>'#25a86c'],
      'cnc' => ['label'=>'CNC & Manufacturing','filter'=>'CNC & Manufacturing','color'=>'#e7a121'],
      'integrated' => ['label'=>'Professional Programs','filter'=>'Integrated Programs','color'=>'#8b28a4'],
    ];
    try{
      $saved = json_decode((string) \Illuminate\Support\Facades\DB::table('erp_settings')->where('key','course_categories')->value('value'), true);
      if(is_array($saved) && $saved){
        $categories = $saved;
      }
    }catch(\Throwable $e){
      // erp_settings not migrated yet: keep the built-in list
    }
  }
  return $categories;
}

// Active courses of the Training Academy page, in admin order (cached per request)
function trainingCourses(){
  static $courses = null;
  if($courses === null){
    $page = templatePage('Training Academy');
    $courses = $page ? $page->trainingCourses()->where('status','active')->get() : collect();
  }
  return $courses;
}

function isMobileDevice() {
  return preg_match("/(android|avantgo|blackberry|bolt|boost|cricket|docomo 
|fone|hiptop|mini|mobi|palm|phone|pie|tablet|up\.browser|up\.link|webos|wos)/i" 
, $_SERVER["HTTP_USER_AGENT"] ?? "");
}

function adminTheme(){
  return assetLinkAdmin().'.';
}

function welcomeTheme(){
  return assetLink().'.';
}

function assetLink(){
  return general()->theme ?: 'welcome';
}

function assetLinkAdmin(){
  return general()->adminTheme ?: 'admin';
}

function geoData($type=null,$parent=null,$id=null){

  $data =Country::orderBy('name')->select(['id','type','parent_id','name']);
  if($type){
    $data =$data->where('type',$type);
  }

  if($parent){
    $data =$data->where('parent_id',$parent);
  }

  if($id){
    $data =$data->find($id);
  }else{
    $data =$data->get();
  }
  
  return $data;
}

function bn2enNumber ($number){
  $search_array= array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
  $replace_array= array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
  $en_number = str_replace($search_array, $replace_array, $number);

  return $en_number;
}

function en2bnNumber ($number){
  $search_array= array("1", "2", "3", "4", "5", "6", "7", "8", "9", "0");
  $replace_array= array("১", "২", "৩", "৪", "৫", "৬", "৭", "৮", "৯", "০");
  $en_number = str_replace($search_array, $replace_array, $number);

  return $en_number;
}

function en2bnMonth($month){
  $search_array= array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October","November","December","Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct","Nov","Dec","01", "02", "03", "04", "05", "06", "07", "08", "09", "10","11","12");
  $replace_array= array("জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগষ্ট", "সেপ্টেম্বর", "অক্টোবর","নভেম্বর","ডিসেম্বর","জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগষ্ট", "সেপ্টেম্বর", "অক্টোবর","নভেম্বর","ডিসেম্বর","জানুয়ারী", "ফেব্রুয়ারী", "মার্চ", "এপ্রিল", "মে", "জুন", "জুলাই", "আগষ্ট", "সেপ্টেম্বর", "অক্টোবর","নভেম্বর","ডিসেম্বর");
  $en_number = str_replace($search_array, $replace_array, $month);
  return $en_number;
}

function priceFormat($amount=0)
{
  $formatAmount ='';

  $formatAmount = number_format($amount,0);

  return $formatAmount;

}


function priceFullFormat($amount=0)
{
  $formatAmount ='';

  $amountFormet = number_format($amount,0);
  
  $formatAmount = general()->currency.' '.$amountFormet;
 
  return $formatAmount;

}

function sendMail($toEmail,$toName,$subject,$datas,$template,$attachments=null,$replyTo=null){

  try {
    Mail::send($template,compact('datas'), function ($message) use ($toEmail,$toName,$subject,$attachments,$replyTo) {
        $message->from(general()->mail_from_address, general()->mail_from_name);
        $message->to($toEmail,$toName);
        //To bb mail 
        //$message->cc($ccRecipients);
        //To Replay diffrent mail 
        //$message->replyTo('replyto@example.com', 'Reply To Name');
        if($replyTo){
            $message->replyTo($replyTo['email'], $replyTo['name'] ?? null);
        }
        
        $message->subject($subject);
        
        if($attachments){
            // Attachments
            foreach ($attachments as $attachment) {
                $message->attach($attachment['path'], [
                    'as' => $attachment['name'],
                    'mime' => $attachment['mime'],
                ]);
            }
        }
        
    });
      return true;
  } catch (Exception $ex) {
      \Log::error('sendMail failed: '.$ex->getMessage());
      return false;
  }

}

function sendSMS($to,$msg){
  $userId = general()->sms_username;
  $pass = general()->sms_password;
  $masking = general()->sms_senderid;
  if(general()->sms_type=='Non Masking'){
  $url =general()->sms_url_nonmasking; 
  return "{$url}?username={$userId}&password={$pass}&number={$to}&message={$msg}";
  }else{
  $url =general()->sms_url_masking;
  return "{$url}?username={$userId}&password={$pass}&number={$to}&message={$msg}&senderid={$masking}";
  }
}

function slider($location=null){
  return Attribute::latest()->where('type',1)->where('status','active')->where('location',$location)->first();
}

function menu($location=null){
  return Attribute::latest()->where('type',8)->where('status','active')->where('location',$location)->first();
}

function page($id=null){
  return Post::where('type',0)->find($id);
}

function pageTemplate($template=null){
  return Post::where('type',0)->where('template',$template)->first();
}

// Extension to store an uploaded file with, taken from the file's real content (MIME type), never trusted from the
// client file name — so "shell.php" (even a valid image renamed) can never be saved with an executable extension.
// Returns null when the content is not one of the allowed media types.
function safeUploadExtension($file){
  $allowed = ['jpg','jpeg','png','gif','webp','svg','pdf','docx','zip','rar','mp4','webm','mov','wmv','mp3'];
  $valid = (new \Symfony\Component\Mime\MimeTypes())->getExtensions((string)$file->getMimeType());
  $client = strtolower((string)$file->getClientOriginalExtension());
  if(in_array($client, $allowed, true) && in_array($client, $valid, true)){
    return $client;
  }
  foreach($valid as $ext){
    if(in_array($ext, $allowed, true)){
      return $ext;
    }
  }
  return null;
}

// Safe stored file name: time.unique.<extension from content>; aborts the request for a disallowed file type
function safeUploadName($file){
  $ext = safeUploadExtension($file);
  if(!$ext){
    abort(422, 'This file type is not allowed.');
  }
  return time().'.'.uniqid().'.'.$ext;
}

function uploadFile($file,$src,$srcType,$fileUse,$author=null,$fileStatus=true){
  // Check the file type first, before anything (an old file) is deleted
  $img =safeUploadName($file);


  if($fileStatus){
      $media = Media::where('src_type',$srcType)->where('use_Of_file',$fileUse)->where('src_id',$src)->first();
  }else{
      $media = null;
  }

  if(!$media){
    $media =new Media();
    }else{
      if(File::exists($media->file_url)){
            File::delete($media->file_url);
        }
        if(File::exists($media->file_url_sm)){
            File::delete($media->file_url_sm);
        }
        if(File::exists($media->file_url_md)){
            File::delete($media->file_url_md);
        }
        if(File::exists($media->file_url_lg)){
            File::delete($media->file_url_lg);
        }
    }
    
    $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
    $fullname = basename($file->getClientOriginalName());
    $ext =pathinfo($img, PATHINFO_EXTENSION);
    $size =$file->getSize();
    $mimeType = $file->getMimeType();

    $year =carbon::now()->format('Y');
    $month =carbon::now()->format('M');
    $folder = $month.'_'.$year;

    $path ="medies/".$folder;
    $fullpath ="medies/".$folder.'/'.$img;
    $media->src_type=$srcType;
    $media->use_Of_file=$fileUse;
    $media->src_id=$src;
    $media->file_name=Str::limit($fullname,250);
    $media->alt_text=Str::limit($name,250);
    $media->file_rename=Str::limit($img,100);
    $media->file_size=$size;
    $media->mine_type=$mimeType;
    if($ext=='png' || $ext=='jpeg' || $ext=='svg' || $ext=='gif' || $ext=='jpg' || $ext=='webp'){
      $media->file_type=1;
      }elseif($ext=='pdf'){
      $media->file_type=2;
      }elseif($ext=='docx'){
      $media->file_type=3;
      }elseif($ext=='zip' || $ext=='rar'){
      $media->file_type=4;
      }elseif($ext=='mp4' || $ext=='webm' || $ext=='mov' || $ext=='wmv'){
      $media->file_type=5;
      }elseif($ext=='mp3'){
      $media->file_type=6;
    }
    $file->move(public_path($path), $img);
    $media->file_url =$fullpath;
    $media->file_path =$path;
    $media->addedby_id=$author;
    $media->save();

    return $media;

}
