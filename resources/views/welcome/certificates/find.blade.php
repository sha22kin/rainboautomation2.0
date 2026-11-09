@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Find Certificate')}}</title>
@endsection
@section('SEO')
<meta name="description" content="Check a {{general()->title}} training certificate by its certificate number and download it." />
@if($searched)<meta name="robots" content="noindex, nofollow" />@endif
@endsection
@push('css')
<style>
    .fc-wrap { padding: 56px 0 72px; background: radial-gradient(circle at 88% 0%, #e3f0ff, transparent 34%), radial-gradient(circle at 0% 100%, #e9faf0, transparent 30%), #f6f9fc; }
    .fc-box { max-width: 760px; margin: 0 auto; }
    .fc-head { text-align: center; margin-bottom: 26px; }
    .fc-badge { display: inline-flex; align-items: center; gap: 8px; padding: 6px 14px; border-radius: 999px; background: #eaf3ff; color: #1e6fc4; font-weight: 800; font-size: 12px; letter-spacing: .6px; text-transform: uppercase; }
    .fc-head h1 { font-size: clamp(28px, 4vw, 40px); margin: 14px 0 8px; color: #172338; letter-spacing: -1px; }
    .fc-head p { color: #5d6f86; margin: 0; }
    .fc-search { display: flex; gap: 10px; background: #fff; border: 1px solid #e1e9f2; border-radius: 20px; padding: 10px; box-shadow: 0 18px 40px rgba(17, 32, 60, .07); }
    .fc-search .fc-input { flex: 1; display: flex; align-items: center; gap: 10px; padding: 0 12px; }
    .fc-search .fc-input i { color: #8aa0b8; }
    .fc-search input { flex: 1; border: 0; outline: 0; font-size: 16px; padding: 12px 0; background: transparent; letter-spacing: .5px; min-width: 0; }
    .fc-search button { border: 0; border-radius: 14px; padding: 12px 22px; font-weight: 800; color: #fff; background: linear-gradient(135deg, #287dcc, #1e64ad); cursor: pointer; white-space: nowrap; }
    .fc-hint { text-align: center; color: #7a8aa0; font-size: 13px; margin-top: 12px; }
    .fc-result { margin-top: 26px; background: #fff; border: 1px solid #e1e9f2; border-radius: 24px; overflow: hidden; box-shadow: 0 18px 40px rgba(17, 32, 60, .06); }
    .fc-ok-top { display: flex; align-items: center; gap: 14px; padding: 18px 24px; background: linear-gradient(110deg, #e8f8ee, #eaf3ff); border-bottom: 1px solid #e1e9f2; }
    .fc-ok-top .seal { width: 48px; height: 48px; border-radius: 14px; display: grid; place-items: center; background: #fff; color: #1f8a46; font-size: 22px; box-shadow: 0 6px 16px rgba(31, 138, 70, .15); flex: 0 0 48px; }
    .fc-ok-top b { display: block; color: #1f6b3b; font-size: 17px; }
    .fc-ok-top span { color: #4b6a58; font-size: 13px; }
    .fc-details { display: grid; grid-template-columns: 1fr 1fr; gap: 0; padding: 8px 24px; }
    .fc-details div { padding: 14px 0; border-bottom: 1px solid #f0f3f7; }
    .fc-details div:nth-last-child(-n+2) { border-bottom: 0; }
    .fc-details small { display: block; font-size: 12px; color: #7a8aa0; font-weight: 700; text-transform: uppercase; letter-spacing: .5px; }
    .fc-details b { font-size: 16px; color: #172338; word-break: break-word; }
    .fc-actions { display: flex; gap: 10px; flex-wrap: wrap; padding: 18px 24px 24px; }
    .fc-btn { display: inline-flex; align-items: center; gap: 8px; border-radius: 12px; padding: 12px 20px; font-weight: 800; font-size: 14px; text-decoration: none; border: 1px solid transparent; }
    .fc-btn.main { background: linear-gradient(135deg, #287dcc, #1e64ad); color: #fff; }
    .fc-btn.main:hover { color: #fff; }
    .fc-btn.light { background: #fff; color: #22466d; border-color: #dce5ef; }
    .fc-none { margin-top: 26px; display: flex; align-items: center; gap: 16px; padding: 22px 24px; border-radius: 22px; background: #fff5f6; border: 1px solid #f6cbd4; color: #9b2236; }
    .fc-none i { font-size: 30px; color: #e05a72; }
    .fc-none b { display: block; font-size: 18px; }
    @media (max-width: 560px) { .fc-search { flex-direction: column; } .fc-details { grid-template-columns: 1fr; } .fc-details div:nth-last-child(2) { border-bottom: 1px solid #f0f3f7; } }
</style>
@endpush
@section('contents')
{{-- Website certificate search (CertificateFinderController@find) --}}
<section class="fc-wrap">
    <div class="wrap">
        <div class="fc-box">
            <div class="fc-head">
                <span class="fc-badge"><i class="fa-solid fa-shield-halved"></i> Certificate Verification</span>
                <h1>Find Certificate</h1>
                <p>Enter a certificate number or a Student ID to check the certificate and download a copy.</p>
            </div>

            <form class="fc-search" action="{{route('certificates.find')}}" method="get" role="search">
                <label class="fc-input" for="number">
                    <i class="fa-solid fa-magnifying-glass"></i>
                    <input type="text" id="number" name="number" value="{{$number}}" placeholder="Certificate No. (RA-XXXX-XXXX) or Student ID (STU-XXXX)" maxlength="40" autocomplete="off" required>
                </label>
                <button type="submit"><i class="fa-solid fa-magnifying-glass"></i> Search</button>
            </form>
            <div class="fc-hint">Both are at the bottom of the certificate: "Certificate No. RA-XXXX-XXXX · Student ID STU-XXXX".</div>

            @if($searched && $certificates->count())
            @if($certificates->count() > 1)
            <p style="text-align:center;margin:22px 0 -8px;color:#4b6a58;font-weight:700;">{{$certificates->count()}} certificates found for Student ID {{$certificates->first()->student?->code}}</p>
            @endif
            @foreach($certificates as $certificate)
            <div class="fc-result">
                <div class="fc-ok-top">
                    <span class="seal"><i class="fa-solid fa-circle-check"></i></span>
                    <div><b>Certificate found</b><span>This certificate was issued by {{general()->title}}.</span></div>
                </div>
                <div class="fc-details">
                    <div><small>Certificate No.</small><b>{{$certificate->code}}</b></div>
                    <div><small>Student</small><b>{{$certificate->student?->name ?: '—'}}</b></div>
                    <div><small>Course</small><b>{{$certificate->course?->course('title') ?: '—'}}</b></div>
                    <div><small>Issued</small><b>{{$certificate->issued?->format('d F Y')}}</b></div>
                    <div><small>Result / Grade</small><b>{{$certificate->grade ?: '—'}}@if($certificate->points()) · {{$certificate->points()}}@endif</b></div>
                    <div><small>Student ID</small><b>{{$certificate->student?->code ?: '—'}}</b></div>
                </div>
                <div class="fc-actions">
                    <a class="fc-btn main" href="{{route('certificates.show', [$certificate->code, 'download' => 1])}}" target="_blank" rel="noopener"><i class="fa-solid fa-download"></i> Download Certificate</a>
                    <a class="fc-btn light" href="{{route('certificates.show', $certificate->code)}}" target="_blank" rel="noopener"><i class="fa-regular fa-eye"></i> View</a>
                </div>
            </div>
            @endforeach
            @elseif($searched)
            <div class="fc-none" role="alert">
                <i class="fa-solid fa-circle-xmark"></i>
                <div><b>No certificate found</b>No certificate was found for "{{\Illuminate\Support\Str::limit($number, 40)}}". Please check the certificate number or Student ID and try again.</div>
            </div>
            @endif
        </div>
    </div>
</section>
@endsection
