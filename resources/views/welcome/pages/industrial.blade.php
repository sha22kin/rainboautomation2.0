@extends('welcome.layouts.master')
@section('title')
<title>{{ isset($page) && $page->seo_title ? $page->seo_title : 'Industrial Automation Solution | Rainbow Automation' }}</title>
@endsection
@section('SEO')
<meta content="{{ isset($page) && $page->seo_description ? $page->seo_description : 'PLC HMI SCADA VFD industrial automation solutions' }}" name="description"/>
@endsection
@push('css')
<link href="{{asset('welcome/css/solutions.css')}}" rel="stylesheet"/>
<link href="{{asset('welcome/css/industrial.css')}}?v={{@filemtime(public_path('welcome/css/industrial.css'))}}" rel="stylesheet"/>
@endpush
@push('js')
<script src="{{asset('welcome/js/industrial.js')}}?v={{@filemtime(public_path('welcome/js/industrial.js'))}}"></script>
@endpush
@section('body')
@php
    $industrialPage = $page ?? templatePage('Industrial Solution');
    $content = solutionContent($industrialPage, 'Industrial Solution');
    $hero = $content['hero'];
@endphp
@include('welcome.includes.header')<section class="ra-banner"><div class="wrap ra-banner-grid"><div><span class="ra-overline">{{$hero['overline']}}</span><h1>{{$hero['title']}} <span class="ra-rainbow-text">{{$hero['highlight']}}</span></h1><p>{{$hero['description']}}</p><div class="ra-action-row">@if($hero['primary_text'])<a class="ra-primary" href="{{$hero['primary_href']}}">{{$hero['primary_text']}}</a>@endif @if($hero['secondary_text'])<a class="ra-secondary" href="{{$hero['secondary_href']}}">{{$hero['secondary_text']}}</a>@endif</div></div><div aria-label="Animated industrial automation overview" class="ra-visual ra-industrial-hero"><div class="ra-ind-topbar">
<div class="ra-live-badge"><i></i> Factory Live</div>
<div class="ra-pill-group"><span class="ra-mode-pill">Ethernet/IP</span><span class="ra-mode-pill accent">Cloud Sync</span></div>
</div>
<div class="ra-ind-stage">
<div class="ra-grid-back"></div><div class="ra-glow a"></div><div class="ra-glow b"></div>
<div class="ra-hmi-card"><span class="ra-mini-title">HMI Touch Panel</span><div class="hmi-top"><span></span><span></span><span></span></div><div class="hmi-graph"><div class="grid"></div><svg viewBox="0 0 100 40" preserveAspectRatio="none"><polyline points="0,30 18,26 36,28 52,12 72,16 100,8"></polyline></svg></div><div class="hmi-tags"><i>HMI</i><i>START</i><i>MODE</i></div></div>
<div class="ra-plc-dock"><span class="ra-mini-title">PLC Control Rack</span><div class="plc-rack"><span></span><span></span><span></span><span></span><span></span><span></span></div><div class="led-row"><i></i><i></i><i></i></div></div>
<div class="ra-vfd-card"><span class="ra-mini-title">VFD Drive</span><div class="vfd-screen" id="ra-vfd-frequency-display">48.5 Hz</div><div class="vfd-dials"><span></span><span></span><span></span></div><div class="vfd-note">Frequency control active</div></div>
<div class="ra-scada-card"><span class="ra-mini-title">SCADA Monitor</span><div class="scada-top"><div><div class="ra-big-number">LIVE</div><div class="ra-small-text">Process overview</div></div><i>📊</i></div><div class="scada-bars"><span></span><span></span><span></span><span></span></div></div>
<div class="ra-iiot-card"><span class="ra-mini-title">IIoT Gateway</span><div class="cloud-pill">Remote Link Active</div><div class="iiot-tiles"><span>MQTT</span><span>Modbus</span><span>Cloud</span><span>Edge</span></div><div class="ra-small-text" style="margin-top:10px">Remote data sync</div></div>
<svg class="ra-flow-svg" viewBox="0 0 700 360" preserveAspectRatio="none" aria-hidden="true"><defs><linearGradient id="raIndFlowA" x1="0" y1="0" x2="1" y2="0"><stop offset="0%" stop-color="#2eb454"></stop><stop offset="100%" stop-color="#287dcc"></stop></linearGradient><linearGradient id="raIndFlowB" x1="0" y1="0" x2="1" y2="0"><stop offset="0%" stop-color="#f3a51b"></stop><stop offset="100%" stop-color="#8b28a4"></stop></linearGradient></defs><path class="f1" d="M172 78 C 202 78, 212 98, 244 124 S 278 130, 322 128"></path><path class="f2" d="M78 234 C 148 234, 172 212, 210 176 S 258 136, 322 126"></path><path class="f3" d="M322 126 C 422 126, 462 104, 540 96"></path><path class="f4" d="M420 202 C 482 202, 530 216, 612 240"></path><circle cx="208" cy="104" r="6"></circle><circle class="d2" cx="172" cy="214" r="6"></circle><circle class="d3" cx="352" cy="126" r="6"></circle><circle class="d4" cx="522" cy="100" r="6"></circle><circle class="d5" cx="566" cy="228" r="6"></circle></svg>
<div class="ra-inline-motor"><div class="pulley-wrap"><div class="pulley-glow"></div><div class="pulley-base"></div><div class="shaft-line"></div><div class="belt top"></div><div class="belt bottom"></div><div class="stand s1"></div><div class="stand s2"></div><div class="stand s3"></div><div class="pulley large"></div><div class="pulley medium"></div><div class="pulley small"></div></div></div>
</div>
@if(count($content['features']))<div class="ra-ind-features">@foreach($content['features'] as $feature)<div class="ra-feature-chip"><span class="chip-dot" style="background:{{$feature['color'] ?? '#287dcc'}}"></span><div><b>{{$feature['title'] ?? ''}}</b><small>{{$feature['text'] ?? ''}}</small></div></div>@endforeach</div>@endif</div></div></section><section class="ra-section"><div class="wrap"><div class="ra-section-head"><span class="ra-overline">{{$content['capabilities']['overline']}}</span><h2>{{$content['capabilities']['title']}}</h2><p>{{$content['capabilities']['description']}}</p></div><div class="ra-service-grid">@foreach(optional($industrialPage)->capabilityCards ?? [] as $card)<article class="ra-service" style="--accent:{{$card->card('color')}}"><span class="ra-emoji">{{$card->card('icon')}}</span><h3>{{$card->card('title')}}</h3><p>{{$card->card('description')}}</p></article>@endforeach</div></div></section>@if(count($content['workflow']['steps']))<section class="ra-section ra-alt"><div class="wrap"><div class="ra-section-head"><span class="ra-overline">{{$content['workflow']['overline']}}</span><h2>{{$content['workflow']['title']}}</h2></div><div class="ra-process">@foreach($content['workflow']['steps'] as $i => $step)<div class="ra-step"><small>{{str_pad($i + 1, 2, '0', STR_PAD_LEFT)}}</small><h3>{{$step['title'] ?? ''}}</h3><p>{{$step['text'] ?? ''}}</p></div>@endforeach</div></div></section>@endif<section class="ra-cta"><div class="wrap"><div><h2>{{$content['cta']['title']}}</h2><p>{{$content['cta']['description']}}</p></div>@if($content['cta']['button_text'])<a class="ra-primary" href="{{$content['cta']['button_href']}}">{{$content['cta']['button_text']}}</a>@endif</div></section>@include('welcome.includes.footer')<script>document.addEventListener('DOMContentLoaded',()=>{const io=new IntersectionObserver(items=>items.forEach(e=>{if(e.isIntersecting){e.target.classList.add('ra-visible');io.unobserve(e.target)}}),{threshold:.09});document.querySelectorAll('.ra-service,.ra-step,.ra-section-head').forEach((el,i)=>{el.classList.add('ra-reveal');el.style.setProperty('--ra-delay',(i%3)*95+'ms');io.observe(el)})})</script>
@endsection
