@php $onHome = request()->routeIs('index'); $general = general(); @endphp
<div class="top-ribbon"></div>
{{-- Top bar: two texts as in the design; on phones (below md) they run as one scrolling line (copy for a seamless loop) --}}
<div class="meta-bar">
<div class="wrap">
<div class="meta-track">
<div>Engineering Laboratory Solutions • e-GP Projects • Solar Systems • Technical Training</div>
<div>Professional B2B &amp; Institutional Technology Partner</div>
<div aria-hidden="true" class="meta-copy">Engineering Laboratory Solutions • e-GP Projects • Solar Systems • Technical Training</div>
<div aria-hidden="true" class="meta-copy">Professional B2B &amp; Institutional Technology Partner</div>
</div>
</div>
</div>
{{-- Desktop (xl, 1200px+): the design header as is. Smaller screens: logo + ☰, the menu opens in the left sidebar below. --}}
<header class="ra-header">
<div class="wrap nav">
<a class="brand" href="{{$onHome ? '#home' : route('index')}}">
<img alt="{{$general->title}} Logo" src="{{asset($general->logo())}}"/>
<div class="brand-text">
<strong>{{$general->title}}</strong>
@if($general->subtitle)<small>{{$general->subtitle}}</small>@endif
</div>
</a>
@include('welcome.includes.navLinks', ['navId' => 'navLinks', 'navClass' => 'ra-nav-five d-none d-xl-flex'])<button aria-label="Switch between Bangla and English" class="ra-language-switch d-none d-xl-inline-flex" id="ra-language-switch" title="Switch language" type="button">বাংলা</button>
<a class="btn btn-primary d-none d-xl-inline-flex" href="{{pageLink('contact')}}">Request Consultation ↗</a>
<button aria-controls="raSidebar" aria-expanded="false" aria-label="Open menu" class="menu-btn d-block d-xl-none" id="menuBtn" type="button">☰</button>
</div>
</header>
<div class="ra-sidebar-backdrop" data-sidebar-close hidden></div>
<aside aria-hidden="true" aria-label="Site menu" class="ra-sidebar" id="raSidebar" tabindex="-1">
<div class="ra-sidebar-head">
<a class="ra-sidebar-brand" href="{{route('index')}}">
<img alt="{{$general->title}} Logo" src="{{asset($general->logo())}}"/>
<span><strong>{{$general->title}}</strong>@if($general->subtitle)<small>{{$general->subtitle}}</small>@endif</span>
</a>
<button aria-label="Close menu" class="ra-sidebar-close" data-sidebar-close type="button">✕</button>
</div>
<div class="ra-sidebar-body">
@include('welcome.includes.navLinks', ['navId' => 'raSidebarNav', 'navClass' => 'ra-sidebar-nav'])
<div class="ra-sidebar-lang">
<span>Language</span>
<button aria-label="Switch between Bangla and English" class="ra-language-switch" title="Switch language" type="button">বাংলা</button>
</div>
<a class="ra-sidebar-cta" href="{{pageLink('contact')}}">Request Consultation ↗</a>
</div>
</aside>
