{{-- Home page: "Student Portal & Certificate Services" + "A simpler journey from registration to certification"
     (design: desing/Student_Portal.html). The forms post to the real student register / login and the certificate finder. --}}
@php
    $spStudent = auth('student')->user();
    // After a failed register / login the page comes back here: open that tab again and show the message
    $spTab = old('_portal') === 'register' ? 'register' : 'login';
    $spBack = old('_portal') || session('error') || $errors->any();
    $spError = session('error') ?: ($errors->any() ? $errors->first() : null);
@endphp
@include('welcome.student.partials.portalStyle')

<section class="sp" id="portal" data-sp-open="{{$spBack ? $spTab : ''}}">
<div class="wrap">
<div class="sp-head">
<div class="sp-eyebrow">Student Self-Service</div>
<h2>Student Portal &amp; Certificate Services</h2>
<p class="sp-lead">Register for training, access your student account and verify course certificates from one convenient platform.</p>
</div>
<div class="sp-tabs" role="tablist" aria-label="Student portal options">
<button class="sp-tab" type="button" role="tab" data-sp-tab="register">✎ Student Registration</button>
<button class="sp-tab" type="button" role="tab" data-sp-tab="login">➜ Student {{$spStudent ? 'Account' : 'Login'}}</button>
<button class="sp-tab" type="button" role="tab" data-sp-tab="verify">✓ Verify Certificate</button>
</div>
<div class="sp-shell">
<aside class="sp-info"><span class="sp-eyebrow" data-sp-eyebrow></span><h3 data-sp-title></h3><p data-sp-text></p><ul data-sp-points></ul></aside>

{{-- Registration --}}
<div class="sp-panel" data-sp-panel="register" hidden>
<h3>Student Registration</h3>
<p>Create your student account. The Rainbow Automation office adds your course after enrollment.</p>
@if($spStudent)
<div class="sp-alert good">You are already logged in as <strong>{{$spStudent->name}}</strong>.</div>
<div class="sp-foot"><a class="sp-btn" href="{{route('student.profile')}}">Open My Profile ↗</a></div>
@else
@if($spBack && $spTab === 'register' && $spError)<div class="sp-alert" role="alert">{{$spError}}</div>@endif
<form action="{{route('student.register')}}" method="post">
@csrf
<input type="hidden" name="_portal" value="register">
<div class="sp-hp" aria-hidden="true"><label for="sp-hp">Leave this empty</label><input type="text" id="sp-hp" name="ra_hp_field" value="" tabindex="-1" autocomplete="new-password" data-lpignore="true" data-1p-ignore></div>
@include('welcome.student.partials.registerFields', ['p' => 'sp-', 'keep' => old('_portal') === 'register'])
<div class="sp-foot"><button class="sp-btn" type="submit">Create Account ↗</button><button class="sp-btn outline" type="button" data-sp-tab="login">Already Registered? Login</button></div>
</form>
@endif
</div>

{{-- Login --}}
<div class="sp-panel" data-sp-panel="login" hidden>
@if($spStudent)
<h3>Welcome, {{$spStudent->name}}</h3>
<p>You are logged in to your Rainbow Automation student account.</p>
<div class="sp-foot"><a class="sp-btn" href="{{route('student.profile')}}">Open My Profile ↗</a>
<form action="{{route('student.logout')}}" method="post" style="margin:0">@csrf<button class="sp-btn outline" type="submit">Log Out</button></form></div>
@else
<h3>Student Login</h3>
<p>Sign in to your Rainbow Automation student account.</p>
@if($spBack && $spTab === 'login' && $spError)<div class="sp-alert" role="alert">{{$spError}}</div>@endif
<form action="{{route('student.login')}}" method="post">
@csrf
<input type="hidden" name="_portal" value="login">
<div class="sp-fields sp-login">
<div class="sp-field"><label for="sp-login-email">Email Address <b>*</b></label><input id="sp-login-email" name="email" type="email" value="{{old('_portal') !== 'register' ? old('email') : ''}}" required maxlength="150" placeholder="name@example.com" autocomplete="username"></div>
<div class="sp-field"><label for="sp-login-pass">Password <b>*</b></label><div class="sp-pass"><input id="sp-login-pass" name="password" type="password" required maxlength="100" placeholder="Enter password" autocomplete="current-password"><button class="sp-btn outline" type="button" data-sp-show="sp-login-pass" style="padding:8px 12px">SHOW</button></div></div>
</div>
<div class="sp-foot"><label class="sp-check"><input type="checkbox" name="remember" value="1"> Remember me</label><a class="sp-link" href="{{route('student.password.forgot')}}">Forgot password?</a></div>
<div class="sp-foot"><button class="sp-btn" type="submit">Sign In Securely ↗</button><button type="button" class="sp-btn outline" data-sp-tab="register">Create Account</button></div>
</form>
@endif
</div>

{{-- Certificate verification (opens the Find Certificate page with the result) --}}
<div class="sp-panel" data-sp-panel="verify" hidden>
<h3>Certificate Verification</h3>
<p>Enter a certificate number or Student ID to check certificate details and validity.</p>
<form action="{{route('certificates.find')}}" method="get">
<div class="sp-fields"><div class="sp-field full"><label for="sp-cert">Certificate Number or Student ID <b>*</b></label><input id="sp-cert" name="number" required maxlength="40" placeholder="e.g. RA-XXXX-XXXX or STU-1001" autocomplete="off"></div></div>
<div class="sp-foot"><button type="submit" class="sp-btn green">✓ Verify Certificate</button></div>
</form>
<div class="sp-notice">Only certificates issued by Rainbow Automation are found here. If a certificate is found, you can view and download it.</div>
</div>
</div>
</div>
</section>

<section class="sp-steps">
<div class="wrap">
<div class="sp-eyebrow">How It Works</div>
<h2>A simpler journey from registration to certification</h2>
<div class="sp-stepgrid">
<article class="sp-step"><span class="sp-eyebrow">Step 01</span><h3>Register</h3><p>Create a student profile with your details. Our office then enrolls you in your preferred training course.</p></article>
<article class="sp-step"><span class="sp-eyebrow">Step 02</span><h3>Learn &amp; Complete</h3><p>Complete approved enrollment and attend practical training sessions and assessments.</p></article>
<article class="sp-step"><span class="sp-eyebrow">Step 03</span><h3>Get Verified</h3><p>Authorized staff issue unique certificates that can be checked using the public verification portal.</p></article>
</div>
</div>
</section>

<script>
(function () {
    var root = document.getElementById('portal');
    if (!root) return;
    var detail = {
        register: ['Join the Academy', 'Build your skills.<br>Start your journey.', 'Tell us about yourself and create your student account.', ['Personal profile', 'Secure account creation', 'Courses added by our office', 'Certificates in your profile']],
        login: ['Welcome Back', 'One account.<br>Your learning journey.', 'Access your training account and stay connected with your course information.', ['My courses & enrollment', 'Batch schedule & attendance', 'Payments & receipts', 'Certificates & training history']],
        verify: ['Certificate Services', 'Verify learning.<br>Build confidence.', 'Check the authenticity of issued Rainbow Automation training certificates.', ['Search by certificate number', 'Or search by Student ID', 'Confirm course & issue date', 'View and download certificate']]
    };
    function setTab(tab) {
        if (!detail[tab]) return;
        root.querySelectorAll('[data-sp-panel]').forEach(function (p) { p.hidden = p.dataset.spPanel !== tab; });
        root.querySelectorAll('.sp-tab').forEach(function (t) {
            var on = t.dataset.spTab === tab;
            t.classList.toggle('active', on);
            t.setAttribute('aria-selected', String(on));
        });
        var d = detail[tab];
        root.querySelector('[data-sp-eyebrow]').textContent = d[0];
        root.querySelector('[data-sp-title]').innerHTML = d[1];
        root.querySelector('[data-sp-text]').textContent = d[2];
        root.querySelector('[data-sp-points]').innerHTML = d[3].map(function (x) { return '<li>' + x.replace(/&/g, '&amp;') + '</li>'; }).join('');
    }
    root.querySelectorAll('[data-sp-tab]').forEach(function (el) {
        el.addEventListener('click', function () { setTab(el.dataset.spTab); });
    });
    root.querySelectorAll('[data-sp-show]').forEach(function (show) {
        show.addEventListener('click', function () {
            var input = document.getElementById(show.dataset.spShow);
            input.type = input.type === 'password' ? 'text' : 'password';
            show.textContent = input.type === 'password' ? 'SHOW' : 'HIDE';
        });
    });
    var back = root.dataset.spOpen;
    setTab(back || 'login');
    // Coming back after a failed register / login: show the form with its message
    if (back) root.scrollIntoView();
    // Header "Student Portal" from another page: land on the section once the page above it has finished loading
    window.addEventListener('load', function () { if (back || location.hash === '#portal') root.scrollIntoView(); });
})();
</script>
