{{-- CMS sign in: the login page design of the website, without the website header / footer --}}
<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{{websiteTitle('Login')}}</title>
<link rel="shortcut icon" type="image/x-icon" href="{{asset(general()->favicon())}}">
<link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>
<style>
    /* Colour tokens of the website design used by the auth pages */
    :root{--nlfb-blue:#0b5fa5;--nlfb-blue-dark:#06264a;--nlfb-green:#00693e;--nlfb-red:#e4002b;--nlfb-cream:#f5f8fb;--nlfb-ink:#1b2733;--nlfb-heading:#06264a;--nl-bg:#fff;--nl-surface:#fff;--nl-surface-2:#eef2f6;--nl-line:#e5edf5;--nl-line-strong:#d6e3f0;--nl-text:#3a4a5a;--nl-muted:#5a6b7d;--nl-faint:#8fa0b3;--nl-tint-blue:#e7f0fa;--nl-tint-blue-2:#eef4fa;--nl-tint-red:#fde7eb;--nl-tint-red-2:#fff5f5;--nl-tint-green:#f0f9f4;--nl-tint-green-2:#e3f4ec}
    body{font-family:Inter,'Segoe UI',Arial,sans-serif;color:var(--nlfb-ink);background:var(--nlfb-cream)}
    a{text-decoration:none}
    .auth-wrap{min-height:100vh;display:flex;align-items:center}
    .auth-wrap>.container{position:relative}
</style>
@include('cms.auth.partials.authStyle')
</head><body>
<section class="auth-wrap">
    <div class="container">
        <div class="auth-card">

            @include('cms.auth.partials.authAside', [
                'asideTitle' => 'Welcome back to '.general()->title,
                'asideText'  => 'Sign in to manage the training ERP, website content, course inquiries and settings.',
            ])

            <div class="auth-main">
                <span class="auth-eyebrow"><i class="fa-solid fa-right-to-bracket"></i> Admin Login</span>
                <h1>Sign in to your account</h1>
                <p class="auth-sub">Enter your details below to continue.</p>

                @if(session('error'))
                <div class="auth-alert auth-alert-error" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div>{{session('error')}}</div></div>
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
                        <a class="auth-link" href="{{config('cms.website_url')}}" target="_blank" rel="noopener">Visit website</a>
                    </div>

                    <button type="submit" class="auth-btn">Sign In <i class="fa-solid fa-arrow-right"></i></button>
                </form>
            </div>

        </div>
    </div>
</section>
@include('cms.auth.partials.authScript')
</body></html>
