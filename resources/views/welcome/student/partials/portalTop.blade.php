{{-- Student login / register pages: portal heading + tabs (links to the pages). $active = register | login | verify --}}
<div class="sp-head">
<div class="sp-eyebrow">Student Self-Service</div>
<h1 class="sp-title">Student Portal &amp; Certificate Services</h1>
<p class="sp-lead">Register for training, access your student account and verify course certificates from one convenient platform.</p>
</div>
<nav class="sp-tabs" aria-label="Student portal options">
<a class="sp-tab {{$active === 'register' ? 'active' : ''}}" href="{{route('student.register')}}" @if($active === 'register') aria-current="page" @endif>✎ Student Registration</a>
<a class="sp-tab {{$active === 'login' ? 'active' : ''}}" href="{{route('student.login')}}" @if($active === 'login') aria-current="page" @endif>➜ Student Login</a>
<a class="sp-tab {{$active === 'verify' ? 'active' : ''}}" href="{{route('certificates.find')}}" @if($active === 'verify') aria-current="page" @endif>✓ Verify Certificate</a>
</nav>
