@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('My Student Profile')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
@endsection
@push('css')
@include('welcome.student.partials.style')
@endpush
@section('contents')
{{-- Student profile: account, linked course records (ERP students) and certificates (StudentPortalController@profile) --}}
@php
    $money = fn ($n) => '৳ '.number_format((float) $n);
    $totalFee = $enrollments->sum('fee');
    $totalPaid = $enrollments->sum(fn ($e) => $e->payments->sum('amount'));
    $totalDue = $enrollments->sum(fn ($e) => $e->due());
@endphp
<section class="sp-wrap">
    <div class="wrap">
        @if(session('success'))
        <div class="sp-alert ok" role="status"><i class="fa-solid fa-circle-check"></i> {{session('success')}}</div>
        @endif

        @include('welcome.student.partials.hero', ['actions' => 'profile'])

        <div class="sp-stats">
            <div class="sp-stat"><span class="ic" style="background:#eaf3ff;color:#287dcc"><i class="fa-solid fa-book-open"></i></span><div><small>Courses</small><b>{{$enrollments->count()}}</b></div></div>
            <div class="sp-stat"><span class="ic" style="background:#fff4dc;color:#c98a06"><i class="fa-solid fa-award"></i></span><div><small>Certificates</small><b>{{$certificates->count()}}</b></div></div>
            <div class="sp-stat"><span class="ic" style="background:#e8f8ee;color:#1f8a46"><i class="fa-solid fa-wallet"></i></span><div><small>Total Paid</small><b>{{$money($totalPaid)}}</b></div></div>
            <div class="sp-stat"><span class="ic" style="background:#fff1f3;color:#d22d4b"><i class="fa-solid fa-hourglass-half"></i></span><div><small>Total Due</small><b>{{$money($totalDue)}}</b></div></div>
        </div>

        {{-- Everything given on the registration form (editable on Edit Profile); empty ones show "Not given" --}}
        @php
            $details = [
                ['Full Name', $account->name, 'fa-regular fa-user'],
                ['Mobile Number', $account->phone, 'fa-solid fa-mobile-screen'],
                ['Email Address', $account->email, 'fa-regular fa-envelope'],
                ['Date of Birth', $account->dob?->format('d M Y'), 'fa-regular fa-calendar'],
                ['Profession', $account->profession, 'fa-solid fa-briefcase'],
                ['Institute / Company', $account->organization, 'fa-solid fa-building-columns'],
                ['Highest Education', $account->education, 'fa-solid fa-user-graduate'],
                ['Preferred Training Course', $account->preferredCourse?->course('title'), 'fa-solid fa-book-open'],
                ['Training Preference', $account->training_mode, 'fa-solid fa-chalkboard-user'],
                ['Preferred Training Location', $account->training_location, 'fa-solid fa-location-dot'],
                ['Present Address', $account->address, 'fa-solid fa-house'],
            ];
        @endphp
        <div class="sp-section">
            <div class="sp-section-head"><h2><i class="fa-regular fa-id-badge"></i> My Information</h2><a class="sp-btn sp-btn-light" href="{{route('student.profile.edit')}}"><i class="fa-solid fa-user-pen"></i> Edit</a></div>
            <div class="sp-details">
                @foreach($details as [$label, $value, $icon])
                <div class="{{$label === 'Present Address' ? 'wide' : ''}}"><small><i class="{{$icon}}"></i> {{$label}}</small>@if(filled($value))<b>{{$value}}</b>@else<b class="none">Not given</b>@endif</div>
                @endforeach
            </div>
        </div>

        <div class="sp-section">
            <div class="sp-section-head">
                <h2><i class="fa-solid fa-graduation-cap"></i> My Courses</h2>
                <span class="sp-count">{{$enrollments->count()}} {{\Illuminate\Support\Str::plural('course', $enrollments->count())}}</span>
            </div>
            @if($enrollments->count())
            <div class="sp-grid">
                @foreach($enrollments as $enrollment)
                @php
                    $paid = $enrollment->payments->sum('amount');
                    $due = $enrollment->due();
                    $percent = $enrollment->fee > 0 ? min(100, round($paid / $enrollment->fee * 100)) : 100;
                @endphp
                <div class="sp-course">
                    <h3>{{$enrollment->course?->course('title') ?: 'Course'}}</h3>
                    <div class="sp-meta">
                        <span class="sp-tag"><i class="fa-solid fa-hashtag"></i> {{$enrollment->code}}</span>
                        <span class="sp-tag"><i class="fa-solid fa-users"></i> {{$enrollment->batch?->code ?: 'Batch not assigned'}}</span>
                        @if($enrollment->admitted_on)<span class="sp-tag"><i class="fa-regular fa-calendar-check"></i> Admitted {{$enrollment->admitted_on->format('d M Y')}}</span>@endif
                    </div>
                    <div class="sp-money">
                        <div><small>Course Fee</small><b>{{$money($enrollment->fee)}}</b></div>
                        <div><small>Paid</small><b>{{$money($paid)}}</b></div>
                        <div class="{{$due > 0 ? 'due' : 'ok'}}"><small>Due</small><b>{{$due > 0 ? $money($due) : 'Paid'}}</b></div>
                    </div>
                    <div class="sp-bar" role="progressbar" aria-valuenow="{{$percent}}" aria-valuemin="0" aria-valuemax="100"><span style="width:{{$percent}}%"></span></div>
                    <div class="sp-bar-label"><span>Payment progress</span><span>{{$percent}}%</span></div>
                </div>
                @endforeach
            </div>
            @else
            <div class="sp-empty"><i class="fa-solid fa-book-open-reader"></i><div>No course is linked to your account yet. After you are admitted to a course, our office links it to this account and it appears here.</div></div>
            @endif
        </div>

        <div class="sp-section">
            <div class="sp-section-head">
                <h2><i class="fa-solid fa-award"></i> Certificate History</h2>
                <span class="sp-count">{{$certificates->count()}} {{\Illuminate\Support\Str::plural('certificate', $certificates->count())}}</span>
            </div>
            @if($certificates->count())
            <div class="sp-grid">
                @foreach($certificates as $certificate)
                <div class="sp-cert">
                    <span class="seal"><i class="fa-solid fa-certificate"></i></span>
                    <div class="sp-cert-info">
                        <b>{{$certificate->course?->course('title') ?: 'Course'}}</b>
                        <span>{{$certificate->code}} · Issued {{$certificate->issued?->format('d M Y')}} @if($certificate->grade)<span class="sp-grade">{{$certificate->grade}}</span>@endif</span>
                        @if($certificate->points())<span>{{$certificate->points()}}</span>@endif
                    </div>
                    <div class="sp-cert-actions">
                        <a class="sp-btn sp-btn-primary" href="{{route('student.certificate', [$certificate->id, 'download' => 1])}}" target="_blank" rel="noopener"><i class="fa-solid fa-download"></i> Download</a>
                        <a class="sp-btn sp-btn-light" href="{{route('student.certificate', $certificate->id)}}" target="_blank" rel="noopener"><i class="fa-regular fa-eye"></i> View</a>
                    </div>
                </div>
                @endforeach
            </div>
            @else
            <div class="sp-empty"><i class="fa-solid fa-award"></i><div>No certificate yet. Certificates appear here when they are issued for your courses.</div></div>
            @endif
        </div>
    </div>
</section>
@endsection
