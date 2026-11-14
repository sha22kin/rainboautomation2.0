@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('My Student Profile')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
@endsection
@push('css')
@include('welcome.student.partials.style')
<style>
.sp-wait{display:flex;gap:18px;align-items:flex-start;background:linear-gradient(110deg,#fff8e6,#fffdf6);border:1px solid #f2d69b;border-radius:20px;padding:24px 26px;margin-top:20px}
.sp-wait .ic{flex:0 0 56px;width:56px;height:56px;border-radius:16px;background:#fff;color:#c98a06;display:grid;place-items:center;font-size:26px;box-shadow:0 8px 20px rgba(201,138,6,.15)}
.sp-wait .ic i{animation:spWait 2.4s ease-in-out infinite}
@keyframes spWait{0%,100%{transform:rotate(0)}50%{transform:rotate(180deg)}}
.sp-wait h2{margin:0 0 6px;font-size:22px;color:#7a4b00;font-weight:800}
.sp-wait p{margin:0 0 6px;color:#6b5a33;line-height:1.7}
.sp-wait ul{margin:10px 0 0;padding-left:18px;color:#6b5a33;line-height:1.9}
@media(max-width:600px){.sp-wait{flex-direction:column;padding:20px}}
@media(prefers-reduced-motion:reduce){.sp-wait .ic i{animation:none}}
</style>
@endpush
@section('contents')
{{-- Student profile before the admin approves the registration (StudentPortalController@profile) --}}
<section class="sp-wrap">
    <div class="wrap">
        @include('welcome.student.partials.hero', ['actions' => 'pending'])

        <div class="sp-wait" role="status">
            <span class="ic"><i class="fa-solid fa-hourglass-half"></i></span>
            <div>
                <h2>Please wait, your registration is under review</h2>
                <p>Thank you for registering, {{$account->name}}. Rainbow Automation will check your registration and approve your account soon.</p>
                <p>Once it is approved, your full student profile opens here:</p>
                <ul>
                    <li>Your Student ID and your courses</li>
                    <li>Payments, due amount and receipts</li>
                    <li>Certificates you can view and download</li>
                </ul>
                <p style="margin-top:10px;">Registered on {{$account->created_at?->format('d M Y, h:i A')}}. If it takes long, please contact our office.</p>
            </div>
        </div>
    </div>
</section>
@endsection
