@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Login')}}</title>
@endsection
@section('SEO')
<meta name="description" content="{!!general()->meta_description!!}" />
<meta name="keywords" content="{{general()->meta_keyword}}" />
<meta property="og:title" content="{{websiteTitle('Login')}}" />
<meta property="og:description" content="{!!general()->meta_description!!}" />
<meta property="og:image" content="{!!general()->meta_description!!}" />
<meta property="og:url" content="{{route('login')}}" />
<link rel="canonical" href="{{route('login')}}">
@endsection
@push('css')
@include('auth.partials.authStyle')
@endpush
@php
    // Messages set by AuthController@login
    $loginError = session('loginfail') ?: (session('loginfailP') ?: session('error'));
@endphp
@section('contents')
<section class="auth-wrap">
    <div class="container">
        <div class="auth-card">

            @include('auth.partials.authAside', [
                'asideTitle' => 'Welcome back to '.general()->title,
                'asideText'  => 'Sign in to manage your account, course inquiries and project consultations.',
            ])

            <div class="auth-main">
                <span class="auth-eyebrow"><i class="fa-solid fa-right-to-bracket"></i> Account Login</span>
                <h1>Sign in to your account</h1>
                <p class="auth-sub">Enter your details below to continue.</p>

                @if($loginError)
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>{{$loginError}}</div></div>
                @endif
                @if(session('success'))
                <div class="auth-alert auth-alert-success" role="status"><i class="fa-solid fa-circle-check"></i><div>{{session('success')}}</div></div>
                @endif
                @if(session('info'))
                <div class="auth-alert auth-alert-info" role="status"><i class="fa-solid fa-circle-info"></i><div>{{session('info')}}</div></div>
                @endif

                <form action="{{route('login')}}" method="post" data-auth-form novalidate>
                    @csrf

                    <div class="auth-field">
                        <label for="username">Email, mobile or username <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('username') ? 'is-invalid' : ''}}">
                            <i class="fa-regular fa-user ai-lead"></i>
                            <input type="text" id="username" name="username" value="{{old('username')}}" placeholder="you@example.com" autocomplete="username" required autofocus>
                        </div>
                        @error('username')
                        <div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>
                        @enderror
                    </div>

                    <div class="auth-field">
                        <label for="password">Password <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('password') ? 'is-invalid' : ''}}">
                            <i class="fa-solid fa-lock ai-lead"></i>
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="Enter your password" autocomplete="current-password" required>
                            <button type="button" class="ai-toggle" data-toggle-password="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        @error('password')
                        <div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>
                        @enderror
                    </div>

                    <div class="auth-row">
                        <label class="auth-check"><input type="checkbox" name="remember" value="1" {{old('remember') ? 'checked' : ''}}> Remember me</label>
                        <a class="auth-link" href="{{route('forgotPassword')}}">Forgot password?</a>
                    </div>

                    <button type="submit" class="auth-btn">Sign In <i class="fa-solid fa-arrow-right"></i></button>
                </form>

                <div class="auth-divider">New to {{general()->title}}?</div>
                <p class="auth-switch">Don't have an account? <a class="auth-link" href="{{route('register')}}">Create an account</a></p>
            </div>

        </div>
    </div>
</section>
@endsection
@push('js')
@include('auth.partials.authScript')
@endpush
