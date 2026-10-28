@extends('welcome.layouts.master')
@section('title')
<title>{{ isset($page) && $page->seo_title ? $page->seo_title : 'Solar System Complete Solution | Rainbow Automation' }}</title>
@endsection
@section('SEO')
<meta content="{{ isset($page) && $page->seo_description ? $page->seo_description : 'Solar system design and complete engineering services' }}" name="description"/>
@endsection
@push('css')
<link href="{{asset('welcome/css/solutions.css')}}" rel="stylesheet"/>
<link href="{{asset('welcome/css/solar.css')}}?v={{@filemtime(public_path('welcome/css/solar.css'))}}" rel="stylesheet"/>
@endpush
@section('body')
@php
    $solarPage = $page ?? templatePage('Solar Solution');
    $content = solutionContent($solarPage, 'Solar Solution');
    $hero = $content['hero'];
@endphp
@include('welcome.includes.header')<section class="ra-banner"><div class="wrap ra-banner-grid"><div><span class="ra-overline">{{$hero['overline']}}</span><h1>{{$hero['title']}} <span class="ra-rainbow-text">{{$hero['highlight']}}</span></h1><p>{{$hero['description']}}</p><div class="ra-action-row">@if($hero['primary_text'])<a class="ra-primary" href="{{$hero['primary_href']}}">{{$hero['primary_text']}}</a>@endif @if($hero['secondary_text'])<a class="ra-secondary" href="{{$hero['secondary_href']}}">{{$hero['secondary_text']}}</a>@endif</div></div><div aria-label="Animated solar system overview" class="ra-visual ra-solar-hero"><div class="ra-solar-topbar">
<div class="ra-live-badge"><i></i> Solar Live System</div>
<div class="ra-status-pill">Efficiency 98%</div>
</div>
<div class="ra-solar-stage">
<div class="ra-sun-core">
<div class="ra-sun"></div>
<span>Sunlight Input</span>
</div>
<div class="ra-house-wrap">
<div class="ra-roof"></div>
<div class="ra-house-body"></div>
<div class="ra-window left"></div>
<div class="ra-window right"></div>
<div class="ra-door"></div>
<div class="ra-panel-array">
<span class="panel p1"></span>
<span class="panel p2"></span>
<span class="panel p3"></span>
<span class="panel p4"></span>
</div>
<div class="ra-inverter-box">INV</div>
<div class="ra-battery-box">
<div class="ra-battery-level"></div>
</div>
<div class="ra-meter-box">
<span class="line l1"></span>
<span class="line l2"></span>
<span class="line l3"></span>
</div>
</div>
<div class="ra-grid-tower">
<span class="tower-line t1"></span>
<span class="tower-line t2"></span>
<span class="tower-line t3"></span>
<div class="tower-head"></div>
<small>Utility Grid</small>
</div>
<svg aria-hidden="true" class="ra-energy-svg" preserveaspectratio="none" viewbox="0 0 700 360">
<defs>
<lineargradient id="raFlowA" x1="0" x2="1" y1="0" y2="0">
<stop offset="0%" stop-color="#f8c447"></stop>
<stop offset="50%" stop-color="#36b46b"></stop>
<stop offset="100%" stop-color="#2e7be3"></stop>
</lineargradient>
<lineargradient id="raFlowB" x1="0" x2="1" y1="0" y2="0">
<stop offset="0%" stop-color="#2e7be3"></stop>
<stop offset="100%" stop-color="#8b28a4"></stop>
</lineargradient>
</defs>
<path class="energy-path main" d="M88 118 C 178 118, 208 138, 286 160 S 430 186, 468 186"></path>
<path class="energy-path secondary" d="M470 186 C 560 186, 575 154, 642 138"></path>
<path class="energy-path tertiary" d="M470 198 C 536 224, 578 236, 628 250"></path>
<circle class="pulse-dot d1" cx="132" cy="118" r="6"></circle>
<circle class="pulse-dot d2" cx="252" cy="151" r="6"></circle>
<circle class="pulse-dot d3" cx="430" cy="184" r="6"></circle>
<circle class="pulse-dot d4" cx="592" cy="153" r="6"></circle>
</svg>
<div class="ra-info-card generation">
<strong>PV Generation</strong>
<b>24.8 kW</b>
<span>Real-time daytime output</span>
</div>
<div class="ra-info-card battery">
<strong>Battery Storage</strong>
<b>82%</b>
<span>Smart backup ready</span>
</div>

</div>
@if(count($content['features']))<div class="ra-hero-features">@foreach($content['features'] as $feature)<div class="ra-feature-chip"><span class="chip-dot" style="background:{{$feature['color'] ?? '#287dcc'}}"></span><b>{{$feature['title'] ?? ''}}</b><small>{{$feature['text'] ?? ''}}</small></div>@endforeach</div>@endif</div></div></section><section class="ra-section"><div class="wrap"><div class="ra-section-head"><span class="ra-overline">{{$content['capabilities']['overline']}}</span><h2>{{$content['capabilities']['title']}}</h2><p>{{$content['capabilities']['description']}}</p></div><div class="ra-service-grid">@foreach(optional($solarPage)->capabilityCards ?? [] as $card)<article class="ra-service" style="--accent:{{$card->card('color')}}"><span class="ra-emoji">{{$card->card('icon')}}</span><h3>{{$card->card('title')}}</h3><p>{{$card->card('description')}}</p></article>@endforeach</div></div></section>@if(count($content['workflow']['steps']))<section class="ra-section ra-alt"><div class="wrap"><div class="ra-section-head"><span class="ra-overline">{{$content['workflow']['overline']}}</span><h2>{{$content['workflow']['title']}}</h2></div><div class="ra-process">@foreach($content['workflow']['steps'] as $i => $step)<div class="ra-step"><small>{{str_pad($i + 1, 2, '0', STR_PAD_LEFT)}}</small><h3>{{$step['title'] ?? ''}}</h3><p>{{$step['text'] ?? ''}}</p></div>@endforeach</div></div></section>@endif<section class="ra-cta"><div class="wrap"><div><h2>{{$content['cta']['title']}}</h2><p>{{$content['cta']['description']}}</p></div>@if($content['cta']['button_text'])<a class="ra-primary" href="{{$content['cta']['button_href']}}">{{$content['cta']['button_text']}}</a>@endif</div></section>@include('welcome.includes.footer')<script>document.addEventListener('DOMContentLoaded',()=>{const io=new IntersectionObserver(items=>items.forEach(e=>{if(e.isIntersecting){e.target.classList.add('ra-visible');io.unobserve(e.target)}}),{threshold:.09});document.querySelectorAll('.ra-service,.ra-step,.ra-section-head').forEach((el,i)=>{el.classList.add('ra-reveal');el.style.setProperty('--ra-delay',(i%3)*95+'ms');io.observe(el)})})</script>
@endsection
