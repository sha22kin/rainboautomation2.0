@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Register')}}</title>
@endsection
@section('SEO')
<meta name="description" content="{!!general()->meta_description!!}" />
<meta name="keywords" content="{{general()->meta_keyword}}" />
<meta property="og:title" content="{{websiteTitle('Register')}}" />
<meta property="og:description" content="{!!general()->meta_description!!}" />
<meta property="og:image" content="{!!general()->meta_description!!}" />
<meta property="og:url" content="{{route('register')}}" />
<link rel="canonical" href="{{route('register')}}">
@endsection
@push('css')
@include('auth.partials.authStyle')
@endpush
@php
    $privacyPage = pageTemplate('Privacy Policy');
    $privacyUrl = $privacyPage ? route('pageView', $privacyPage->slug ?: 'no-title') : '#';
@endphp
@section('contents')
<section class="auth-wrap">
    <div class="container">
        <div class="auth-card">

            @include('auth.partials.authAside', [
                'asideTitle' => 'Join '.general()->title,
                'asideText'  => 'Create a free account to follow our training courses, engineering services and solar solutions.',
            ])

            <div class="auth-main">
                <span class="auth-eyebrow"><i class="fa-solid fa-user-plus"></i> Create Account</span>
                <h1>Sign up for free</h1>
                <p class="auth-sub">It only takes a minute. Fields marked <span class="text-nlfb-red">*</span> are required.</p>

                @if(session('success'))
                <div class="auth-alert auth-alert-success" role="status"><i class="fa-solid fa-circle-check"></i><div>{{session('success')}}</div></div>
                @endif
                @if(session('error'))
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>{{session('error')}}</div></div>
                @endif
                @if($errors->any())
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>Please check the highlighted fields and try again.</div></div>
                @endif

                <form action="{{route('register')}}" method="post" data-auth-form novalidate>
                    @csrf

                    <div class="auth-field">
                        <label for="name">Full name <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('name') ? 'is-invalid' : ''}}">
                            <i class="fa-regular fa-user ai-lead"></i>
                            <input type="text" id="name" name="name" value="{{old('name')}}" placeholder="Your full name" autocomplete="name" maxlength="100" required>
                        </div>
                        @error('name')
                        <div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>
                        @enderror
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="auth-field mb-0">
                                <label for="email">Email address <span class="req">*</span></label>
                                <div class="auth-input {{$errors->has('email') ? 'is-invalid' : ''}}">
                                    <i class="fa-regular fa-envelope ai-lead"></i>
                                    <input type="email" id="email" name="email" value="{{old('email')}}" placeholder="you@example.com" autocomplete="email" maxlength="100" required>
                                </div>
                                @error('email')
                                <div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>
                                @enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="auth-field mb-0">
                                <label for="mobile">Mobile number <span class="req">*</span></label>
                                <div class="auth-input {{$errors->has('mobile') ? 'is-invalid' : ''}}">
                                    <i class="fa-solid fa-mobile-screen-button ai-lead"></i>
                                    <input type="tel" id="mobile" name="mobile" value="{{old('mobile')}}" placeholder="01XXXXXXXXX" autocomplete="tel" maxlength="20" required>
                                </div>
                                @error('mobile')
                                <div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>
                                @enderror
                            </div>
                        </div>
                    </div>

                    <div class="auth-field mt-3">
                        <label for="password">Password <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('password') ? 'is-invalid' : ''}}">
                            <i class="fa-solid fa-lock ai-lead"></i>
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="At least 5 characters" autocomplete="new-password" minlength="5" required>
                            <button type="button" class="ai-toggle" data-toggle-password="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        <div class="auth-strength" data-strength-for="password" aria-live="polite">
                            <div class="bars"><span></span><span></span><span></span><span></span></div>
                            <small></small>
                        </div>
                        @error('password')
                        <div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>
                        @enderror
                    </div>

                    <p class="auth-note">
                        By creating an account you agree that your personal data will be used to support your experience on this website and to manage your account, as described in our <a class="auth-link" href="{{$privacyUrl}}">Privacy Policy</a>.
                    </p>

                    <button type="submit" class="auth-btn">Create Account <i class="fa-solid fa-arrow-right"></i></button>
                </form>

                <div class="auth-divider">Already a member?</div>
                <p class="auth-switch">Already have an account? <a class="auth-link" href="{{route('login')}}">Sign in</a></p>
            </div>

        </div>
    </div>
</section>
@endsection
@push('js')
@include('auth.partials.authScript')
@endpush
