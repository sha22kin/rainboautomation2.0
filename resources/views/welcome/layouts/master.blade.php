<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8"/>
<meta content="width=device-width,initial-scale=1" name="viewport"/>
<meta name="csrf-token" content="{{csrf_token()}}"/>
@yield('title')
@yield('SEO')
<link rel="icon" href="{{asset(general()->favicon())}}"/>
{{-- Bootstrap 5 grid only (row / col / display utilities, no component styles), used for the responsive layout --}}
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap-grid.min.css" rel="stylesheet"/>
@stack('css')
<link href="{{asset('welcome/css/layout.css')}}?v={{@filemtime(public_path('welcome/css/layout.css'))}}" rel="stylesheet"/>
<link href="{{asset('welcome/css/responsive.css')}}?v={{@filemtime(public_path('welcome/css/responsive.css'))}}" rel="stylesheet"/>
{{-- Admin → Setting → General: custom CSS / head script --}}
@if(general()->custom_css)<style>{!!general()->custom_css!!}</style>@endif
{!!general()->script_head!!}
</head>
<body>
@yield('body')
<script src="{{asset('welcome/js/responsive.js')}}?v={{@filemtime(public_path('welcome/js/responsive.js'))}}"></script>
@stack('js')
<script>window.RA_TRANSLATIONS=@json((object) websiteTranslations());window.RA_TRANSLATE_URL=@json(route('translateTexts'));</script>
<script src="{{asset('welcome/js/language.js')}}?v={{@filemtime(public_path('welcome/js/language.js'))}}"></script>
{{-- Admin → Setting → General: body script / custom JS --}}
{!!general()->script_body!!}
@if(general()->custom_js)<script>{!!general()->custom_js!!}</script>@endif
</body>
</html>
