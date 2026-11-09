@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Set New Password')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
<meta name="referrer" content="no-referrer" />
@endsection
@push('css')
@include('auth.partials.authStyle')
@endpush
@section('contents')
{{-- Student reset password from the emailed link (StudentPortalController@resetPassword) --}}
<section class="auth-wrap">
    <div class="container">
        <div class="auth-card">

            @include('auth.partials.authAside', [
                'asideTitle' => 'Choose a new password',
                'asideText'  => 'Use at least 8 characters with letters and numbers.',
            ])

            <div class="auth-main">
                <span class="auth-eyebrow"><i class="fa-solid fa-lock"></i> New Password</span>
                <h1>Set a new password</h1>
                <p class="auth-sub">Enter your email and your new password.</p>

                @if(session('error'))
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>{{session('error')}} <a class="auth-link" href="{{route('student.password.forgot')}}">Ask for a new link</a></div></div>
                @elseif($errors->any())
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>{{$errors->first()}}</div></div>
                @endif

                <form action="{{route('student.password.update')}}" method="post" data-auth-form novalidate>
                    @csrf
                    <input type="hidden" name="token" value="{{$token}}">

                    <div class="auth-field">
                        <label for="email">Email <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('email') ? 'is-invalid' : ''}}">
                            <i class="fa-regular fa-envelope ai-lead"></i>
                            <input type="email" id="email" name="email" value="{{old('email', $email)}}" autocomplete="email" maxlength="150" required>
                        </div>
                    </div>

                    <div class="auth-field">
                        <label for="password">New password <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('password') ? 'is-invalid' : ''}}">
                            <i class="fa-solid fa-lock ai-lead"></i>
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="At least 8 characters, letters and numbers" autocomplete="new-password" maxlength="100" required autofocus>
                            <button type="button" class="ai-toggle" data-toggle-password="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        @error('password')<div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>@enderror
                    </div>

                    <div class="auth-field">
                        <label for="password_confirmation">Confirm new password <span class="req">*</span></label>
                        <div class="auth-input">
                            <i class="fa-solid fa-lock ai-lead"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="has-toggle" placeholder="Type the new password again" autocomplete="new-password" maxlength="100" required>
                            <button type="button" class="ai-toggle" data-toggle-password="password_confirmation" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                        </div>
                    </div>

                    <button type="submit" class="auth-btn">Save New Password <i class="fa-solid fa-check"></i></button>
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
