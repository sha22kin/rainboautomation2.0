@php
    // Website pages (former website admin) are Bootstrap based: they get the theme CSS + the CMS skin for it
    $website = $website ?? false;
    $erpPage = $erpPage ?? null;
    $topTitle = $website ? App\Support\WebsiteMenu::activeLabel() : (App\Support\Erp::MENU[$erpPage][1] ?? 'Dashboard');
@endphp
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="csrf-token" content="{{ csrf_token() }}">
@hasSection('title')
@yield('title')
@else
<title>Rainbow Automation | Training ERP</title>
@endif
<link rel="shortcut icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
@if($website)
@include('cms.partials.website-head')
<link rel="stylesheet" href="{{ asset('cms/cms-shell.css') }}?v={{ filemtime(public_path('cms/cms-shell.css')) }}">
<link rel="stylesheet" href="{{ asset('cms/cms-website.css') }}?v={{ filemtime(public_path('cms/cms-website.css')) }}">
@else
<link rel="stylesheet" href="{{ asset('cms/cms.css') }}?v={{ filemtime(public_path('cms/cms.css')) }}">
@endif
<link rel="stylesheet" href="{{ asset('cms/cms-extra.css') }}?v={{ filemtime(public_path('cms/cms-extra.css')) }}">
@stack('css')
</head><body class="{{ $website ? 'ws' : '' }}"><div class="app">@include('cms.partials.sidebar', ['erpPage' => $erpPage, 'website' => $website])<div class="content"><header class="topbar"><div style="display:flex;align-items:center;gap:12px"><button class="mobile-menu" onclick="toggleMenu()">☰</button><div><div class="title" id="topTitle">{{ $topTitle }}</div><div class="sub">{{ $website ? 'Website Content Management' : 'Training Academy Management System' }}</div></div></div>@include('cms.partials.profile')</header><main class="main"><div class="demo"><b>ERP + WEBSITE CMS:</b> {{ App\Models\Erp\Setting::get('branch', 'Training Center') }} · Records are saved to the central database. Website changes are published to the public site as soon as they are saved.</div>@if($website)@yield('contents')@else<div id="screen">@yield('screen')</div>@endif</main></div></div>@unless($website)<div id="modalBack" class="modal-back"><div class="modal"><div class="modalhead"><h2 id="modalTitle"></h2><button class="close" onclick="closeModal()">×</button></div><form id="modalForm" method="post" enctype="multipart/form-data">@csrf<div id="modalFields" class="formgrid"></div><div class="foot-actions"><button type="button" class="btn alt" onclick="closeModal()">Cancel</button><button type="submit" class="btn">Save record</button></div></form></div></div>@endunless<div id="toast" class="notification"></div>
@if($website)
@include('cms.partials.website-scripts')
@endif
<script src="{{ asset('cms/cms.js') }}?v={{ filemtime(public_path('cms/cms.js')) }}"></script>
@if(session('toast'))<script>toast(@json(session('toast')))</script>@endif
@stack('js')
</body></html>
