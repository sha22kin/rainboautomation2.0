@extends('welcome.layouts.master')
@section('title')
<title>{{ isset($page) && $page->seo_title ? $page->seo_title : 'Services | Rainbow Automation' }}</title>
@endsection
@section('SEO')
<meta content="{{ isset($page) && $page->seo_description ? $page->seo_description : 'Rainbow Automation services for laboratories, e-GP tenders, industrial automation and solar systems.' }}" name="description"/>
@endsection
@push('css')
<link href="{{asset('welcome/css/pages.css')}}" rel="stylesheet"/>
<link href="{{asset('welcome/css/services.css')}}?v={{@filemtime(public_path('welcome/css/services.css'))}}" rel="stylesheet"/>
@endpush
@section('body')
@php
    $servicesPage = $page ?? templatePage('Services');
    $content = solutionContent($servicesPage, 'Services');
    $hero = $content['hero'];
@endphp
@include('welcome.includes.header')<section class="ra-banner"><div class="wrap ra-banner-grid"><div><span class="ra-overline">{{$hero['overline']}}</span><h1>{{$hero['title']}} <span class="ra-rainbow-text">{{$hero['highlight']}}</span></h1><p>{{$hero['description']}}</p><div class="ra-banner-actions">@if($hero['primary_text'])<a class="ra-primary" href="{{$hero['primary_href']}}">{{$hero['primary_text']}}</a>@endif @if($hero['secondary_text'])<a class="ra-secondary" href="{{$hero['secondary_href']}}">{{$hero['secondary_text']}}</a>@endif</div></div><div aria-label="Rainbow Automation animated engineering services" class="ra-visual-feature ra-service-motion">
<div class="ra-motion-head"><span class="ra-motion-live"><i></i> RAINBOW AUTOMATION</span><span class="ra-motion-pill">ENGINEERING IN MOTION</span></div>
<div class="ra-motion-stage">
<svg aria-hidden="true" class="ra-motion-wires" preserveaspectratio="none" viewbox="0 0 500 285"><defs><lineargradient id="raMotionGrad"><stop stop-color="#54e0ad"></stop><stop offset=".5" stop-color="#67bbff"></stop><stop offset="1" stop-color="#c48dff"></stop></lineargradient></defs><path d="M110 85 C160 85 172 139 230 143"></path><path d="M390 85 C342 85 330 139 270 143"></path><path d="M110 218 C165 218 177 164 230 154"></path><path d="M390 218 C338 218 324 164 270 154"></path></svg>
<div class="ra-motion-hub"><div class="ra-hub-ring"></div><div class="ra-hub-core"><span>RA</span><small>SMART<br/>SOLUTIONS</small></div></div>
<div class="ra-motion-node lab"><div class="ra-motion-art lab-art"><span class="lab-stand"></span><span class="lab-post"></span><span class="lab-scope"></span><span class="lab-platform"></span><i></i></div><strong>Laboratory Systems</strong></div>
<div class="ra-motion-node tender"><div class="ra-motion-art document-art"><span class="doc-sheet"><i></i><i></i><i></i></span><span class="doc-seal">✓</span></div><strong>e-GP Procurement</strong></div>
<div class="ra-motion-node industrial"><div class="ra-motion-art factory-art"><span class="factory-gear">⚙</span><span class="factory-track"><i></i><i></i><i></i></span></div><strong>Industrial Automation</strong></div>
<div class="ra-motion-node solar"><div class="ra-motion-art solar-art"><span class="solar-sun">☀</span><span class="solar-panel"><i></i><i></i><i></i><i></i></span></div><strong>Solar Energy</strong></div>
<div class="ra-motion-orb o1"></div><div class="ra-motion-orb o2"></div><div class="ra-motion-orb o3"></div><div class="ra-motion-orb o4"></div>
</div>
<div class="ra-motion-footer"><span><i></i> Engineering Expertise</span><span>Practical Solutions</span></div>
</div></div></section>
<section class="ra-section"><div class="wrap"><div class="ra-section-head"><span class="ra-overline">{{$content['capabilities']['overline']}}</span><h2>{{$content['capabilities']['title']}}</h2><p>{{$content['capabilities']['description']}}</p></div><div class="ra-service-grid four">@foreach((optional($servicesPage)->capabilityCards ?? collect())->filter(function ($card) { return $card->card('show_services'); }) as $card)<article class="ra-service"><div class="ra-service-icon" style="background:{{$card->card('color')}}">{{$card->card('icon')}}</div><h3>{{$card->card('title')}}</h3><p>{{$card->card('description')}}</p></article>@endforeach</div></div></section>
<section class="ra-section ra-alt"><div class="wrap"><div class="ra-section-head"><span class="ra-overline">{{$content['support']['overline']}}</span><h2>{{$content['support']['title']}}</h2></div><div class="ra-service-grid">@foreach(optional($servicesPage)->supportCards ?? [] as $card)<article class="ra-service"><div class="ra-service-icon" style="background:{{$card->card('color')}}">{{$card->card('icon')}}</div><h3>{{$card->card('title')}}</h3><p>{{$card->card('description')}}</p></article>@endforeach</div>@if(count($content['support']['strip_items']))<div class="ra-quick-strip">@foreach($content['support']['strip_items'] as $strip)<div>{{$strip}}</div>@endforeach</div>@endif</div></section>
@if(count($content['workflow']['steps']))<section class="ra-section"><div class="wrap"><div class="ra-section-head"><span class="ra-overline">{{$content['workflow']['overline']}}</span><h2>{{$content['workflow']['title']}}</h2></div><div class="ra-process">@foreach($content['workflow']['steps'] as $i => $step)<div class="ra-step"><small>{{str_pad($i + 1, 2, '0', STR_PAD_LEFT)}}</small><h3>{{$step['title'] ?? ''}}</h3><p>{{$step['text'] ?? ''}}</p></div>@endforeach</div></div></section>@endif
<section class="ra-cta"><div class="wrap"><div><h2>{{$content['cta']['title']}}</h2><p>{{$content['cta']['description']}}</p></div>@if($content['cta']['button_text'])<a class="ra-primary" href="{{$content['cta']['button_href']}}">{{$content['cta']['button_text']}}</a>@endif</div></section>@include('welcome.includes.footer')<script>document.addEventListener('DOMContentLoaded',()=>{const io=new IntersectionObserver(items=>items.forEach(e=>{if(e.isIntersecting){e.target.classList.add('ra-visible');io.unobserve(e.target)}}),{threshold:.09});document.querySelectorAll('.ra-service,.ra-step,.ra-section-head').forEach((el,i)=>{el.classList.add('ra-reveal');el.style.setProperty('--ra-delay',(i%3)*95+'ms');io.observe(el)})})</script>
@endsection
