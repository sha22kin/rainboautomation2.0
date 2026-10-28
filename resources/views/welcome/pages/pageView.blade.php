@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{$page->seo_title?:websiteTitle($page->name)}}</title>
@endsection
@section('SEO')
<meta name="description" content="{!!$page->seo_description?:general()->meta_description!!}" />
<meta name="keywords" content="{{$page->seo_keyword?:general()->meta_keyword}}" />
<link rel="canonical" href="{{route('pageView',$page->slug?:'no-title')}}" />
@endsection
@section('contents')
<section class="page-hero"><div class="wrap"><span class="eyebrow">RAINBOW AUTOMATION</span><h1>{{$page->name}}</h1></div></section>
<section class="section"><div class="wrap">{!!$page->description!!}</div></section>
@endsection
