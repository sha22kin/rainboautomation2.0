@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Student Login')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
@endsection
@push('css')
@include('auth.partials.authStyle')
@endpush
@section('contents')
{{-- Student login (StudentPortalController@login) --}}
<section class="auth-wrap">
    <div class="container">
        <div class="auth-card">

            @include('auth.partials.authAside', [
                'asideTitle' => 'Welcome back, student',
                'asideText'  => 'Log in to see your courses, payments and certificate history.',
            ])

            <div class="auth-main">
                <span class="auth-eyebrow"><i class="fa-solid fa-user-graduate"></i> Student Login</span>
                <h1>Log in to your student account</h1>
                <p class="auth-sub">Use the email and password you registered with.</p>

                @if(session('error'))
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>{{session('error')}}</div></div>
                @elseif($errors->any())
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div><strong>Login failed.</strong> {{$errors->first()}}</div></div>
                @endif
                @if(session('success'))
                <div class="auth-alert auth-alert-success" role="status"><i class="fa-solid fa-circle-check"></i><div>{{session('success')}}</div></div>
                @endif

                <form action="{{route('student.login')}}" method="post" data-auth-form novalidate>
                    @csrf

                    <div class="auth-field">
                        <label for="email">Email <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('email') ? 'is-invalid' : ''}}">
                            <i class="fa-regular fa-envelope ai-lead"></i>
                            <input type="email" id="email" name="email" value="{{old('email')}}" placeholder="you@example.com" autocomplete="email" maxlength="150" required autofocus>
                        </div>
                        @error('email')<div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>@enderror
                    </div>

                    <div class="auth-field">
                        <label for="password">Password <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('password') ? 'is-invalid' : ''}}">
                            <i class="fa-solid fa-lock ai-lead"></i>
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="Enter your password" autocomplete="current-password" maxlength="100" required>
                            <button type="button" class="ai-toggle" data-toggle-password="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        @error('password')<div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>@enderror
                    </div>

                    <div class="auth-row">
                        <label class="auth-check"><input type="checkbox" name="remember" value="1" {{old('remember') ? 'checked' : ''}}> Remember me</label>
                        <a class="auth-link" href="{{route('student.password.forgot')}}">Forgot password?</a>
                    </div>

                    <button type="submit" class="auth-btn">Log In <i class="fa-solid fa-arrow-right"></i></button>
                </form>

                <div class="auth-divider">New student?</div>
                <p class="auth-switch">No account yet? <a class="auth-link" href="{{route('student.register')}}">Register as a student</a></p>
            </div>

        </div>
    </div>
</section>
@endsection
@push('js')
@include('auth.partials.authScript')
@endpush
