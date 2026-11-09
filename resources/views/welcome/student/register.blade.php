@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Student Registration')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
@endsection
@push('css')
@include('auth.partials.authStyle')
<style>.auth-hp{position:absolute!important;left:-10000px!important;width:1px;height:1px;overflow:hidden}</style>
@endpush
@section('contents')
{{-- Student registration (StudentPortalController@register) --}}
<section class="auth-wrap">
    <div class="container">
        <div class="auth-card">

            @include('auth.partials.authAside', [
                'asideTitle' => 'Join the '.general()->title.' Training Academy',
                'asideText'  => 'Create your student account to see your courses, payments and certificates in one place.',
            ])

            <div class="auth-main">
                <span class="auth-eyebrow"><i class="fa-solid fa-user-graduate"></i> Student Registration</span>
                <h1>Create your student account</h1>
                <p class="auth-sub">Fill in the form below. Fields marked <span class="req">*</span> are required.</p>

                @if(session('error'))
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>{{session('error')}}</div></div>
                @elseif($errors->any())
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div><strong>Registration failed.</strong> Please fix the following:<ul style="margin:6px 0 0; padding-left:18px;">@foreach(array_unique($errors->all()) as $message)<li>{{$message}}</li>@endforeach</ul></div></div>
                @endif

                <form action="{{route('student.register')}}" method="post" data-auth-form novalidate>
                    @csrf
                    <div class="auth-hp" aria-hidden="true"><label for="ra_hp_field">Leave this empty</label><input type="text" id="ra_hp_field" name="ra_hp_field" value="" tabindex="-1" autocomplete="new-password" data-lpignore="true" data-1p-ignore></div>

                    <div class="auth-field">
                        <label for="name">Full name <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('name') ? 'is-invalid' : ''}}">
                            <i class="fa-regular fa-user ai-lead"></i>
                            <input type="text" id="name" name="name" value="{{old('name')}}" placeholder="Your full name" autocomplete="name" maxlength="150" required autofocus>
                        </div>
                        @error('name')<div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>@enderror
                    </div>

                    <div class="auth-field">
                        <label for="email">Email <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('email') ? 'is-invalid' : ''}}">
                            <i class="fa-regular fa-envelope ai-lead"></i>
                            <input type="email" id="email" name="email" value="{{old('email')}}" placeholder="you@example.com" autocomplete="email" maxlength="150" required>
                        </div>
                        @error('email')<div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>@enderror
                    </div>

                    <div class="auth-field">
                        <label for="phone">Mobile number <span class="req">*</span></label>
                        <div class="auth-input {{$errors->hasAny(['phone','phone_key']) ? 'is-invalid' : ''}}">
                            <i class="fa-solid fa-mobile-screen ai-lead"></i>
                            <input type="tel" id="phone" name="phone" value="{{old('phone')}}" placeholder="01X-XXXXXXXX" autocomplete="tel" maxlength="20" required>
                        </div>
                        @error('phone')<div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>@enderror
                        @error('phone_key')<div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>@enderror
                    </div>

                    <div class="auth-field">
                        <label for="password">Password <span class="req">*</span></label>
                        <div class="auth-input {{$errors->has('password') ? 'is-invalid' : ''}}">
                            <i class="fa-solid fa-lock ai-lead"></i>
                            <input type="password" id="password" name="password" class="has-toggle" placeholder="At least 8 characters, letters and numbers" autocomplete="new-password" maxlength="100" required>
                            <button type="button" class="ai-toggle" data-toggle-password="password" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        @error('password')<div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>@enderror
                    </div>

                    <div class="auth-field">
                        <label for="password_confirmation">Confirm password <span class="req">*</span></label>
                        <div class="auth-input">
                            <i class="fa-solid fa-lock ai-lead"></i>
                            <input type="password" id="password_confirmation" name="password_confirmation" class="has-toggle" placeholder="Type the password again" autocomplete="new-password" maxlength="100" required>
                            <button type="button" class="ai-toggle" data-toggle-password="password_confirmation" aria-label="Show password"><i class="fa-regular fa-eye"></i></button>
                        </div>
                    </div>

                    <div class="auth-row">
                        <label class="auth-check"><input type="checkbox" name="agree" value="1" {{old('agree') ? 'checked' : ''}}> The information I entered is correct</label>
                    </div>
                    @error('agree')<div class="auth-error"><i class="fa-solid fa-circle-exclamation"></i> {{$message}}</div>@enderror

                    <button type="submit" class="auth-btn">Register <i class="fa-solid fa-arrow-right"></i></button>
                </form>

                <div class="auth-divider">Already registered?</div>
                <p class="auth-switch">Have a student account? <a class="auth-link" href="{{route('student.login')}}">Student login</a></p>
            </div>

        </div>
    </div>
</section>
@endsection
@push('js')
@include('auth.partials.authScript')
@endpush
