{{-- Brand panel shared by the login and sign-up pages --}}
<aside class="auth-aside">
    <a href="{{route('index')}}" class="auth-logo" aria-label="{{general()->title}} home">
        <img src="{{asset(general()->logo())}}" alt="{{general()->title}}">
    </a>

    <h2>{{$asideTitle ?? 'Engineering, training and solar solutions'}}</h2>
    <p>{{$asideText ?? 'Your '.general()->title.' account for training, project and consultation updates.'}}</p>

    <ul class="auth-points">
        <li>
            <span class="ap-icon"><i class="fa-solid fa-graduation-cap"></i></span>
            <div><strong>Training Academy</strong><span>Hear first about new courses, batches and practical training sessions.</span></div>
        </li>
        <li>
            <span class="ap-icon"><i class="fa-solid fa-gears"></i></span>
            <div><strong>Engineering solutions</strong><span>Laboratory equipment, industrial automation and e-GP projects.</span></div>
        </li>
        <li>
            <span class="ap-icon"><i class="fa-solid fa-solar-panel"></i></span>
            <div><strong>Solar projects</strong><span>Keep your consultations and project inquiries in one place.</span></div>
        </li>
    </ul>

    <div class="auth-secure"><i class="fa-solid fa-shield-halved"></i> Your information is kept private and secure.</div>
    <span class="auth-flag"></span>
</aside>
