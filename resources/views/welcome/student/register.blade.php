@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Student Registration')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
@endsection
@push('css')
@include('welcome.student.partials.portalStyle')
@endpush
@section('contents')
{{-- Student registration (StudentPortalController@register), design: desing/Student_Portal.html --}}
<section class="sp">
<div class="sp-wrap">
@include('welcome.student.partials.portalTop', ['active' => 'register'])
<div class="sp-shell">
<aside class="sp-info">
<span class="sp-eyebrow">Join the Academy</span>
<h3>Build your skills.<br>Start your journey.</h3>
<p>Create your student account to see your courses, payments and certificates in one place.</p>
<ul><li>Personal profile</li><li>Secure account creation</li><li>Courses added by our office</li><li>Certificates in your profile</li></ul>
</aside>
<div class="sp-panel">
<h2 class="sp-panel-title">Create your student account</h2>
<p>Fill in the form below. Fields marked <b style="color:#d94646">*</b> are required. The Rainbow Automation office adds your course after enrollment.</p>

@if(session('error'))
<div class="sp-alert" role="alert">{{session('error')}}</div>
@elseif($errors->any())
<div class="sp-alert" role="alert"><strong>Registration failed.</strong> Please fix the following:<ul>@foreach(array_unique($errors->all()) as $message)<li>{{$message}}</li>@endforeach</ul></div>
@endif

<form action="{{route('student.register')}}" method="post" novalidate>
@csrf
<div class="sp-hp" aria-hidden="true"><label for="ra_hp_field">Leave this empty</label><input type="text" id="ra_hp_field" name="ra_hp_field" value="" tabindex="-1" autocomplete="new-password" data-lpignore="true" data-1p-ignore></div>
@include('welcome.student.partials.registerFields', ['p' => '', 'keep' => true])
<div class="sp-foot"><button class="sp-btn" type="submit">Create Account ↗</button><a class="sp-btn outline" href="{{route('student.login')}}">Already Registered? Login</a></div>
</form>
</div>
</div>
</div>
</section>
@endsection
@push('js')
@include('welcome.student.partials.portalScript')
@endpush
