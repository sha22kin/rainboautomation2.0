@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Student Login')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
@endsection
@push('css')
@include('welcome.student.partials.portalStyle')
@endpush
@section('contents')
{{-- Student login (StudentPortalController@login), design: desing/Student_Portal.html --}}
<section class="sp">
<div class="sp-wrap">
@include('welcome.student.partials.portalTop', ['active' => 'login'])
<div class="sp-shell">
<aside class="sp-info">
<span class="sp-eyebrow">Welcome Back</span>
<h3>One account.<br>Your learning journey.</h3>
<p>Access your training account and stay connected with your course information.</p>
<ul><li>My courses &amp; enrollment</li><li>Batch schedule &amp; attendance</li><li>Payments &amp; receipts</li><li>Certificates &amp; training history</li></ul>
</aside>
<div class="sp-panel">
<h2 class="sp-panel-title">Student Login</h2>
<p>Sign in to your Rainbow Automation student account with the email and password you registered with.</p>

@if(session('error'))
<div class="sp-alert" role="alert">{{session('error')}}</div>
@elseif($errors->any())
<div class="sp-alert" role="alert"><strong>Login failed.</strong> {{$errors->first()}}</div>
@endif
@if(session('success'))
<div class="sp-alert good" role="status">{{session('success')}}</div>
@endif

<form action="{{route('student.login')}}" method="post" novalidate>
@csrf
<div class="sp-fields sp-login">
<div class="sp-field"><label for="email">Email Address <b>*</b></label><input id="email" name="email" type="email" value="{{old('email')}}" class="{{$errors->has('email') ? 'bad' : ''}}" required maxlength="150" placeholder="name@example.com" autocomplete="username" autofocus>@error('email')<span class="sp-err">{{$message}}</span>@enderror</div>
<div class="sp-field"><label for="password">Password <b>*</b></label><div class="sp-pass"><input id="password" name="password" type="password" class="{{$errors->has('password') ? 'bad' : ''}}" required maxlength="100" placeholder="Enter password" autocomplete="current-password"><button class="sp-btn outline" type="button" data-sp-show="password" style="padding:8px 12px">SHOW</button></div>@error('password')<span class="sp-err">{{$message}}</span>@enderror</div>
</div>
<div class="sp-foot"><label class="sp-check"><input type="checkbox" name="remember" value="1" {{old('remember') ? 'checked' : ''}}> Remember me</label><a class="sp-link" href="{{route('student.password.forgot')}}">Forgot password?</a></div>
<div class="sp-foot"><button class="sp-btn" type="submit">Sign In Securely ↗</button><a class="sp-btn outline" href="{{route('student.register')}}">Create Account</a></div>
</form>
</div>
</div>
</div>
</section>
@endsection
@push('js')
@include('welcome.student.partials.portalScript')
@endpush
