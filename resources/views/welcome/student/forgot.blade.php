@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Forgot Password')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
@endsection
@push('css')
@include('auth.partials.authStyle')
@endpush
@section('contents')
{{-- Student forgot password: email a reset link (StudentPortalController@sendResetLink) --}}
<section class="auth-wrap">
    <div class="container">
        <div class="auth-card">

            @include('auth.partials.authAside', [
                'asideTitle' => 'Forgot your password?',
                'asideText'  => 'Enter the email of your student account and we will send you a link to set a new password.',
            ])

            <div class="auth-main">
                <span class="auth-eyebrow"><i class="fa-solid fa-key"></i> Forgot Password</span>
                <h1>Reset your password</h1>
                <p class="auth-sub">We will email you a link to choose a new password. The link is valid for 60 minutes.</p>

                @if(session('error'))
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>{{session('error')}}</div></div>
                @elseif($errors->any())
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>{{$errors->first()}}</div></div>
                @endif
                @if(session('success'))
                <div class="auth-alert auth-alert-success" role="status"><i class="fa-solid fa-circle-check"></i><div>{{session('success')}}</div></div>
                @endif

                <form action="{{route('student.password.forgot')}}" method="post" data-auth-form novalidate>
                    @csrf
                    <div class="auth-field">
                        <label for="email">Email <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('email') ? 'is-invalid' : ''}}">
                            <i class="fa-regular fa-envelope ai-lead"></i>
                            <input type="email" id="email" name="email" value="{{old('email')}}" placeholder="you@example.com" autocomplete="email" maxlength="150" required autofocus>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn">Send Reset Link <i class="fa-solid fa-paper-plane"></i></button>
                </form>

                <div class="auth-divider">Remembered it?</div>
                <p class="auth-switch"><a class="auth-link" href="{{route('student.login')}}">Back to student login</a></p>
            </div>

        </div>
    </div>
</section>
@endsection
@push('js')
@include('auth.partials.authScript')
@endpush
