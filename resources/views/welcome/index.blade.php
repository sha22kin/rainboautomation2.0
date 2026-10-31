@extends('welcome.layouts.master')
@section('title')
<title>{{ isset($page) && $page->seo_title ? $page->seo_title : 'Rainbow Automation | Engineering Solutions • Solar • Training' }}</title>
@endsection
@section('SEO')
<meta content="{{ isset($page) && $page->seo_description ? $page->seo_description : 'Rainbow Automation provides laboratory equipment solutions, e-GP projects, solar system solutions and professional industrial training in Bangladesh.' }}" name="description"/>
@endsection
@push('js')
<script src="{{asset('welcome/js/stats-counter.js')}}?v={{@filemtime(public_path('welcome/js/stats-counter.js'))}}"></script>
@endpush
@push('css')
<link href="{{asset('welcome/css/home.css')}}" rel="stylesheet"/>
@endpush
@section('body')
@php $courseCount = trainingCourses()->count(); @endphp
@include('welcome.includes.header')
<main>
<section class="hero" id="home">
<div class="wrap hero-grid">
<div class="hero-copy">
<h1 class="hero-title"><span class="line plain">Engineering the</span><span class="line plain">Future <span class="with-word">with</span></span><span class="line animated-rainbow">Automation, Solar &amp;</span><span class="line animated-rainbow">Innovation</span><span aria-hidden="true" class="hero-subglow"></span></h1>
<p>
            Rainbow Automation supplies engineering laboratory equipment and industrial automation solutions, participates in e-GP tender projects,
            delivers complete solar system solutions, and provides professional industrial training in PLC, VFD,
            HMI, SCADA, IIoT, Pneumatic, Hydraulic, CNC, CAD/CAM and more.
          </p>
<div class="hero-actions">
<a class="btn btn-primary" href="#services">Discover Our Solutions ↗</a>
<a class="btn btn-outline" href="{{pageLink('training')}}">Explore Training ↗</a>
</div>
<div class="hero-info-panel">
<div class="hero-panel-title">
<strong>Core Business Strengths</strong>
<span>Integrated Engineering • Energy • Automation</span>
</div>
<div class="hero-points">
<span class="pill green">Laboratory Equipment Supply</span>
<span class="pill blue">Industrial Automation Solution</span>
<span class="pill gold">Solar EPC &amp; Installation</span>
<span class="pill red">e-GP Tender &amp; Institutional Projects</span>
<span class="pill purple">Hands-on Technical Training</span>
<span class="pill blue">Consultation, Implementation &amp; Support</span>
</div>

</div>
</div>
<div class="visual-stack ra-showcase" aria-label="Animated industrial robotic manipulator showcase">
  <div class="ra-showcase-light"></div>
  <div class="ra-robot-shell">
    <div class="ra-robot-top"><span class="ra-live"><i></i> ENGINEERING IN MOTION</span><span class="ra-small-label">INDUSTRY 4.0</span></div>
    <div class="ra-robot-stage"><div class="ra-stage-grid"></div><svg class="robotics-svg ra-manipulator" viewBox="0 0 620 310" role="img" aria-label="Animated articulated six-axis industrial robotic manipulator with electric control panel" xmlns="http://www.w3.org/2000/svg">
 <defs>
  <linearGradient id="rMetal" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#ffffff"/><stop offset=".32" stop-color="#dce7f5"/><stop offset=".76" stop-color="#afc2da"/><stop offset="1" stop-color="#eff5ff"/></linearGradient>
  <linearGradient id="rBlue" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#47b2ff"/><stop offset=".48" stop-color="#266ed7"/><stop offset="1" stop-color="#5634ba"/></linearGradient>
  <linearGradient id="rOrange" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#ffdf84"/><stop offset=".4" stop-color="#ffaa35"/><stop offset="1" stop-color="#ed701d"/></linearGradient>
  <linearGradient id="rBg" x1="0" y1="0" x2="1" y2="1"><stop stop-color="#edf5ff"/><stop offset="1" stop-color="#fff"/></linearGradient>
  <filter id="rShadow" x="-50%" y="-70%" width="220%" height="250%"><feDropShadow dx="0" dy="8" stdDeviation="8" flood-color="#284a74" flood-opacity=".20"/></filter>
 </defs>
 <g opacity=".5" stroke="#bdd3eb" stroke-width="1.2" fill="none"><path d="M32 241h553M32 260h553M32 279h553"/><path d="M77 225v68M139 225v68M201 225v68M263 225v68M325 225v68M387 225v68M449 225v68M511 225v68"/></g>
 <g fill="none" stroke-linecap="round" stroke-dasharray="7 10" stroke-width="3"><path class="ra-wire-a" d="M24 183C80 183 88 120 148 120S226 171 282 155 353 83 407 87 480 136 587 81" stroke="#63b3ea"/><path class="ra-wire-b" d="M68 214C116 168 157 185 204 139S321 105 383 129 501 170 592 132" stroke="#ffa85a" opacity=".65"/></g>
 <ellipse cx="234" cy="252" rx="160" ry="27" fill="#7fa7d6" opacity=".13"/>
 <g class="ra-control" filter="url(#rShadow)"><rect x="447" y="156" width="121" height="98" rx="18" fill="#ffffff" stroke="#dbe7f6" stroke-width="2"/><rect x="460" y="172" width="95" height="52" rx="9" fill="#122e50"/><path d="M473 201l13-12 12 8 13-14 12 8 18-14" fill="none" stroke="#3cdbb1" stroke-width="2.6"/><path d="M473 208h68" stroke="#6e9fd0" stroke-width="1" opacity=".5"/><circle cx="472" cy="239" r="5" fill="#2eb453"/><circle cx="490" cy="239" r="5" fill="#ffb329"/><circle cx="508" cy="239" r="5" fill="#ed4556"/><text x="528" y="243" font-size="10" fill="#637997" font-weight="800">PLC</text></g>
 <g class="ra-robot-machine" filter="url(#rShadow)">
  <path d="M116 257h154l17-15H99z" fill="#7d92ac"/><rect x="111" y="234" width="155" height="26" rx="11" fill="url(#rBlue)"/><path d="M136 233v-31q0-16 16-16h81q16 0 16 16v31" fill="url(#rMetal)" stroke="#94afce" stroke-width="3"/>
  <rect x="159" y="201" width="68" height="37" rx="14" fill="#f7fbff" stroke="#a2bad6" stroke-width="3"/>
  <circle cx="193" cy="203" r="36" fill="#c3d5eb"/><circle cx="193" cy="203" r="27" fill="url(#rBlue)"/><circle cx="193" cy="203" r="15" fill="#e9f3ff"/><circle cx="193" cy="203" r="6" fill="#2dbd92"/>
  <g transform="translate(193 203)">
   <g class="ra-shoulder">
    <path d="M-10 -14L54 -133Q62-144 76-134L100-113Q112-102 103-86L31 9Q18 23 4 12Z" fill="url(#rMetal)" stroke="#92adca" stroke-width="4"/>
    <path d="M20-8L77-113" stroke="url(#rBlue)" stroke-width="15" stroke-linecap="round"/><path d="M34-38L61-88" stroke="#97d3ff" stroke-width="4" stroke-linecap="round"/>
    <circle cx="88" cy="-112" r="29" fill="#b6cce4"/><circle cx="88" cy="-112" r="22" fill="url(#rBlue)"/><circle cx="88" cy="-112" r="11" fill="#edf6ff"/>
    <g transform="translate(88 -112)">
     <g class="ra-elbow">
      <path d="M-3 -12L89 -44Q107-49 114-33L121-11Q125 3 108 10L9 25Q-4 25-10 11Z" fill="url(#rMetal)" stroke="#95b0cd" stroke-width="4"/>
      <path d="M23 4L99-20" stroke="url(#rOrange)" stroke-width="13" stroke-linecap="round"/>
      <circle cx="110" cy="-17" r="24" fill="#b7cce3"/><circle cx="110" cy="-17" r="17" fill="url(#rBlue)"/><circle cx="110" cy="-17" r="8" fill="#eff7ff"/>
      <g transform="translate(110 -17)">
       <g class="ra-wrist">
        <rect x="0" y="-16" width="79" height="31" rx="15" fill="url(#rMetal)" stroke="#9db7d2" stroke-width="3"/>
        <rect x="26" y="-13" width="25" height="25" rx="8" fill="url(#rBlue)"/>
        <circle cx="76" cy="0" r="17" fill="#dae6f6" stroke="#91acc9" stroke-width="3"/>
        <g transform="translate(87 0)">
         <rect x="0" y="-13" width="30" height="26" rx="9" fill="url(#rBlue)"/>
         <g class="ra-grip-a"><path d="M24 -10l22 -19 9 8 -20 20Z" fill="url(#rMetal)" stroke="#90aeca" stroke-width="2"/></g>
         <g class="ra-grip-b"><path d="M24 10l22 19 9-8 -20-20Z" fill="url(#rMetal)" stroke="#90aeca" stroke-width="2"/></g>
        </g>
       </g>
      </g>
     </g>
    </g>
   </g>
  </g>
 </g>
 <g class="ra-pick-cube" filter="url(#rShadow)"><path d="M449 91l30-14 30 14-30 16Z" fill="#7ce5ca"/><path d="M449 91l30 16v29l-30-16Z" fill="#18b698"/><path d="M479 107l30-16v29l-30 16Z" fill="#2389c7"/></g>
</svg></div>
    <div class="ra-robot-caption"><div><small>SMART MANUFACTURING</small><strong>Animated 6-Axis Robotic Manipulator &amp; Intelligent Control</strong></div><span class="ra-status">● LIVE AUTOMATION</span></div>
  </div>
  <div class="ra-capabilities"><a href="{{pageLink('industrial')}}"><span class="ra-cicon">⚙</span><span><strong>Industrial Automation</strong><small>PLC · HMI · SCADA · IIoT</small></span><b>↗</b></a><a href="{{pageLink('solar')}}"><span class="ra-cicon gold">☀</span><span><strong>Solar Energy</strong><small>Design · Supply · EPC</small></span><b>↗</b></a><a href="{{pageLink('training')}}"><span class="ra-cicon purple">◈</span><span><strong>Training Academy</strong><small>{{$courseCount}} Practical Courses</small></span><b>↗</b></a></div>
</div>
</div>
</section>
<div class="stats">
<div class="wrap stats-grid">
<div class="stat"><b>4+</b><span>Core Business Verticals</span></div>
<div class="stat"><b>{{$courseCount}}</b><span>Professional Training Courses</span></div>
<div class="stat"><b>360°</b><span>Institutional Solution Capability</span></div>
<div class="stat"><b>End-to-End</b><span>Supply, Training &amp; Support</span></div>
</div>
</div>
<section id="services">
<div class="wrap">
<div class="section-head">
<span class="eyebrow"><i></i> Our Core Services</span>
<h2>Integrated Engineering, Automation, Energy &amp; Training</h2>
<p>
            Explore Rainbow Automation’s five core capabilities in engineering laboratory systems, institutional procurement, solar energy, industrial automation and industry-focused professional training.
          </p>
</div>
<div class="services">
@foreach(homeServiceCards() as $card)
<div class="card" style="--accent: {{$card->card('home_color') ?: $card->card('color')}}">
@if($card->card('badge'))<span class="badge">{{$card->card('badge')}}</span>@endif
<h3>{{$card->card('title')}}</h3>
<p>{{$card->card('home_description') ?: $card->card('description')}}</p>
@if(count($card->card('points')))
<ul class="list">
@foreach($card->card('points') as $point)
<li>{{$point}}</li>
@endforeach
</ul>
@endif
@if($cardLink = serviceCardLink($card))
<a class="service-detail-link" href="{{$cardLink}}">{{str_replace('{count}', $courseCount, $card->card('link_text') ?: 'Learn More')}} <span aria-hidden="true">↗</span></a>
@endif
</div>
@endforeach
</div>
</div>
</section>
<section>
@php $why = homeWhyContent(); @endphp
<div class="wrap">
<div class="section-head">
@if($why['eyebrow'])<span class="eyebrow"><i></i> {{$why['eyebrow']}}</span>@endif
@if($why['title'])<h2>{{$why['title']}}</h2>@endif
@if($why['subtitle'])<p>
            {{$why['subtitle']}}
          </p>@endif
</div>
<div class="why-grid">
@foreach($why['items'] as $i => $item)
<div class="feature-card">
<div class="feature-icon" style="background:linear-gradient(135deg,{{$item['color']}},{{$item['color2'] ?? $item['color']}})">{{str_pad($i + 1, 2, '0', STR_PAD_LEFT)}}</div>
<div><h4>{{$item['title']}}</h4>@if($item['text'])<p>{{$item['text']}}</p>@endif</div>
</div>
@endforeach
</div>
</div>
</section>
<section class="section" id="get-in-touch" style="background:#f6f9fc"><div class="wrap" style="text-align:center"><div class="section-head"><h2>Training Academy &amp; Contact</h2><p>Browse all {{$courseCount}} courses with module outlines or reach our team at our Dhaka office and training center.</p></div><div style="display:flex;justify-content:center;flex-wrap:wrap;gap:14px"><a class="btn" href="{{pageLink('training')}}">Explore {{$courseCount}} Training Courses ↗</a><a class="btn btn-outline" href="{{pageLink('contact')}}">Contact Us &amp; Maps ↗</a></div></div></section><section class="ra-section ra-alt"><div class="wrap"><div class="ra-section-head"><span class="ra-overline">Explore our specialist divisions</span><h2>Engineering capability across five dedicated pages</h2><p>Visit our solar and industrial solutions pages, browse {{$courseCount}} professional courses, or contact our team.</p></div><div class="ra-action-row" style="justify-content:center"><a class="ra-primary" href="{{pageLink('solar')}}">Solar Solutions ↗</a><a class="ra-secondary" href="{{pageLink('industrial')}}">Industrial Solution ↗</a><a class="ra-secondary" href="{{pageLink('training')}}">Training Academy ↗</a></div></div></section></main>
@include('welcome.includes.footer')
<script>document.addEventListener('DOMContentLoaded',()=>{if(!('IntersectionObserver' in window))return;const nodes=document.querySelectorAll('.ra-service,.ra-step,.course-card,.course-card-home,.contact-card,.card,.feature-card,.section-head');const io=new IntersectionObserver(items=>{items.forEach(i=>{if(i.isIntersecting){i.target.classList.add('ra-visible');io.unobserve(i.target)}})},{threshold:.08});nodes.forEach((el,i)=>{el.classList.add('ra-reveal');el.style.setProperty('--ra-delay',(i%3)*90+'ms');io.observe(el)})});</script>
@endsection
