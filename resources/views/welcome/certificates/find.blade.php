@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Find Certificate')}}</title>
@endsection
@section('SEO')
<meta name="description" content="Check a {{general()->title}} training certificate by its certificate number and download it." />
@if($searched)<meta name="robots" content="noindex, nofollow" />@endif
@endsection
@push('css')
@include('welcome.student.partials.portalStyle')
<style>
.fcv{border:1px solid #c9e4df;border-radius:13px;padding:22px;margin-top:22px;background:#f7fefb}
.fcv-top{display:flex;align-items:center;justify-content:space-between;gap:10px;flex-wrap:wrap}
.fcv-top strong{color:#087d60;font-size:14px}
.fcv-tag{display:inline-block;padding:4px 9px;border-radius:15px;background:#dff5ea;color:#13704f;font-size:10px;font-weight:800;letter-spacing:.4px;text-transform:uppercase}
.fcv-grid{display:grid;grid-template-columns:1fr 1fr;gap:12px;margin-top:17px}
.fcv-grid div{background:#fff;padding:11px;border:1px solid #e2f0ea;border-radius:8px;min-width:0}
.fcv-grid small{display:block;color:#63788b;font-size:10px}
.fcv-grid b{font-size:12px;color:#102333;overflow-wrap:anywhere}
.fcv-none{margin-top:22px;border:1px solid #f2d69b;background:#fff8e8;color:#7a5600;border-radius:13px;padding:18px 20px;font-size:12px}
.fcv-none strong{display:block;font-size:14px;color:#8a4b00;margin-bottom:4px}
.fcv-count{margin:22px 0 -8px;color:#13704f;font-weight:800;font-size:12px}
@media(max-width:500px){.fcv-grid{grid-template-columns:1fr}}
</style>
@endpush
@section('contents')
{{-- Website certificate search (CertificateFinderController@find), design: desing/Student_Portal.html --}}
<section class="sp">
<div class="sp-wrap">
@include('welcome.student.partials.portalTop', ['active' => 'verify'])
<div class="sp-shell">
<aside class="sp-info">
<span class="sp-eyebrow">Certificate Services</span>
<h3>Verify learning.<br>Build confidence.</h3>
<p>Check the authenticity of issued {{general()->title}} training certificates.</p>
<ul><li>Search by certificate number</li><li>Or search by Student ID</li><li>Confirm course &amp; issue date</li><li>View and download certificate</li></ul>
</aside>
<div class="sp-panel">
<h2 class="sp-panel-title">Certificate Verification</h2>
<p>Enter a certificate number or a Student ID to check the certificate and download a copy.</p>

<form action="{{route('certificates.find')}}" method="get" role="search">
<div class="sp-fields"><div class="sp-field full"><label for="number">Certificate Number or Student ID <b>*</b></label><input id="number" name="number" value="{{$number}}" required maxlength="40" placeholder="Certificate No. (RA-XXXX-XXXX) or Student ID (STU-XXXX)" autocomplete="off" @unless($searched) autofocus @endunless></div></div>
<div class="sp-foot"><button type="submit" class="sp-btn green">✓ Verify Certificate</button></div>
</form>

@if($searched && $certificates->count())
@if($certificates->count() > 1)
<p class="fcv-count">{{$certificates->count()}} certificates found for Student ID {{$certificates->first()->student?->code}}</p>
@endif
@foreach($certificates as $certificate)
<div class="fcv">
<div class="fcv-top"><strong>✓ Certificate found</strong><span class="fcv-tag">Issued by {{general()->title}}</span></div>
<div class="fcv-grid">
<div><small>Certificate Number</small><b>{{$certificate->code}}</b></div>
<div><small>Student Name</small><b>{{$certificate->student?->name ?: '—'}}</b></div>
<div><small>Training Course</small><b>{{$certificate->course?->course('title') ?: '—'}}</b></div>
<div><small>Student ID</small><b>{{$certificate->student?->code ?: '—'}}</b></div>
<div><small>Issue Date</small><b>{{$certificate->issued?->format('d F Y')}}</b></div>
<div><small>Result / Grade</small><b>{{$certificate->grade ?: '—'}}@if($certificate->points()) · {{$certificate->points()}}@endif</b></div>
</div>
<div class="sp-foot">
<a class="sp-btn" href="{{route('certificates.show', [$certificate->code, 'download' => 1])}}" target="_blank" rel="noopener">⬇ Download Certificate</a>
<a class="sp-btn outline" href="{{route('certificates.show', $certificate->code)}}" target="_blank" rel="noopener">View Certificate ↗</a>
</div>
</div>
@endforeach
@elseif($searched)
<div class="fcv-none" role="alert"><strong>No certificate found</strong>No certificate was found for "{{\Illuminate\Support\Str::limit($number, 40)}}". Please check the certificate number or Student ID and try again.</div>
@endif

<div class="sp-notice">Both numbers are at the bottom of the certificate: "Certificate No. RA-XXXX-XXXX · Student ID STU-XXXX".</div>
</div>
</div>
</div>
</section>
@endsection
