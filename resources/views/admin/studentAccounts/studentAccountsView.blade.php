@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle($account->name.' | Registered Students')}}</title>
@endsection
@push('css')
@include(adminTheme().'studentAccounts.style')
<style>
.sv-head{display:flex;flex-wrap:wrap;align-items:center;gap:16px;padding:18px}
.sv-head .rs-avatar{width:72px;height:72px;flex-basis:72px;font-size:24px}
.sv-head h2{margin:0;font-size:22px;font-weight:800;color:#172338}
.sv-meta{display:flex;flex-wrap:wrap;gap:6px 14px;margin-top:6px;font-size:12px;color:#5d6f86}
.sv-meta i{color:#287dcc;margin-right:4px}
.sv-acts{margin-left:auto;display:flex;gap:6px;align-items:center}
.sv-acts form{margin:0}
.sv-stats{display:grid;grid-template-columns:repeat(auto-fit,minmax(150px,1fr));gap:10px;margin:14px 0}
.sv-stat{background:#fff;border:1px solid #e3eaf2;border-radius:12px;padding:12px 14px}
.sv-stat small{display:block;font-size:11px;color:#7a8aa0;font-weight:800;text-transform:uppercase;letter-spacing:.4px}
.sv-stat b{font-size:20px;color:#172338}
.sv-sec{margin-top:14px}
.sv-sec h4{display:flex;align-items:center;gap:8px;margin:0;padding:12px 14px;font-size:14px;font-weight:800;color:#172338;border-bottom:1px solid #edf1f6}
.sv-sec h4 i{color:#287dcc}
.sv-sec h4 .rs-chip{margin-left:auto}
.sv-body{padding:12px 14px}
.sv-table{width:100%;border-collapse:collapse;font-size:12px}
.sv-table th{font-size:10px;text-transform:uppercase;letter-spacing:.5px;color:#7a8aa0;font-weight:800;padding:7px 8px;text-align:left;border-bottom:1px solid #edf1f6;white-space:nowrap}
.sv-table td{padding:7px 8px;border-bottom:1px solid #f0f3f7;vertical-align:middle;color:#33475f}
.sv-table tr:last-child td{border-bottom:0}
.sv-table .num{text-align:right;white-space:nowrap}
.sv-table form{margin:0;display:inline}
.sv-empty{color:#9aa8b8;font-size:12px;padding:6px 0}
.sv-scroll{overflow-x:auto}
@media(max-width:700px){.sv-acts{margin-left:0;width:100%}}
</style>
@endpush
@section('contents')
{{-- One registered student (AdminController@studentAccountsView) --}}
@php
    $money = fn ($n) => '৳ '.number_format((float) $n);
    $waiting = !$account->isApproved();
    $courseRows = $enrollments->filter(fn ($e) => $e->course_id);
    $paid = $payments->sum('amount');
    $due = $enrollments->sum(fn ($e) => $e->due());
    $allAttendance = $attendance->flatten();
    $present = $allAttendance->where('status', 'Present')->count();
    $byId = $enrollments->keyBy('id');
    $details = [
        'Full Name' => $account->name,
        'Mobile Number' => $account->phone,
        'Email Address' => $account->email,
        'Date of Birth' => $account->dob?->format('d M Y'),
        'Profession' => $account->profession,
        'Institute / Company' => $account->organization,
        'Highest Education' => $account->education,
        'Preferred Training Course' => $account->preferredCourse?->course('title'),
        'Training Preference' => $account->training_mode,
        'Preferred Training Location' => $account->training_location,
        'Present Address' => $account->address,
        'Registered' => $account->created_at?->format('d M Y, h:i A'),
        'Approved' => $account->approved_at?->format('d M Y, h:i A'),
        'Last Login' => $account->last_login_at?->format('d M Y, h:i A'),
    ];
@endphp
<div class="content-header row">
    <div class="content-header-left col-md-8 col-12 mb-2">
        <h3 class="content-header-title mb-0">{{$account->name}}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.studentAccounts')}}">Registered Students</a></li>
                    <li class="breadcrumb-item active">{{$account->name}}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-4 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            <a class="btn btn-outline-primary" href="{{route('admin.studentAccounts')}}"><i class="fa-solid fa-arrow-left"></i> All Students</a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')

        {{-- Student --}}
        <div class="rs-card">
            <div class="sv-head">
                <span class="rs-avatar">@if($account->photoUrl())<img src="{{$account->photoUrl()}}" alt="">@else{{$account->initials()}}@endif</span>
                <div style="min-width:0">
                    <h2>{{$account->name}}
                        @if($waiting)<span class="rs-chip wait">Waiting for Approval</span>@elseif(!$account->status)<span class="rs-chip off">Blocked</span>@else<span class="rs-chip ok">Active</span>@endif
                    </h2>
                    <div class="sv-meta">
                        <span><i class="fa-solid fa-id-card"></i>{{$account->code}}</span>
                        @if($enrollments->first())<span><i class="fa-solid fa-user-graduate"></i>{{$enrollments->first()->code}}</span>@endif
                        <span><i class="fa-regular fa-envelope"></i>{{$account->email}}</span>
                        <span><i class="fa-solid fa-mobile-screen"></i>{{$account->phone}}</span>
                    </div>
                </div>
                <div class="sv-acts">
                    @if($waiting)
                    <form action="{{route('admin.studentAccountsAction',['approve',$account->id])}}" method="post" onsubmit="return confirm('Approve {{e($account->name)}}? The student can then log in and the profile is made.')">
                        @csrf
                        <button type="submit" class="rs-ic approve" title="Approve"><i class="fa-solid fa-check"></i></button>
                    </form>
                    <form action="{{route('admin.studentAccountsAction',['delete',$account->id])}}" method="post" onsubmit="return confirm('Reject and delete this registration?')">
                        @csrf
                        <button type="submit" class="rs-ic reject" title="Reject"><i class="fa-solid fa-xmark"></i></button>
                    </form>
                    @else
                    <form action="{{route('admin.studentAccountsAction',[$account->status ? 'block' : 'unblock',$account->id])}}" method="post" onsubmit="return confirm('{{$account->status ? 'Block' : 'Unblock'}} {{e($account->name)}}?')">
                        @csrf
                        <button type="submit" class="rs-ic {{$account->status ? 'block' : 'unblock'}}" title="{{$account->status ? 'Block' : 'Unblock'}}"><i class="fa-solid {{$account->status ? 'fa-ban' : 'fa-unlock'}}"></i></button>
                    </form>
                    <form action="{{route('admin.studentAccountsAction',['delete',$account->id])}}" method="post" onsubmit="return confirm('Delete this student account? Course records stay in the ERP.')">
                        @csrf
                        <button type="submit" class="rs-ic del" title="Delete"><i class="fa-regular fa-trash-can"></i></button>
                    </form>
                    @endif
                </div>
            </div>
        </div>

        <div class="sv-stats">
            <div class="sv-stat"><small>Courses</small><b>{{$courseRows->count()}}</b></div>
            <div class="sv-stat"><small>Certificates</small><b>{{$certificates->count()}}</b></div>
            <div class="sv-stat"><small>Total Paid</small><b>{{$money($paid)}}</b></div>
            <div class="sv-stat"><small>Total Due</small><b style="color:{{$due > 0 ? '#c4313b' : '#1f8a46'}}">{{$money($due)}}</b></div>
            <div class="sv-stat"><small>Attendance</small><b>{{$allAttendance->count() ? round($present * 100 / $allAttendance->count()).'%' : '—'}}</b></div>
        </div>

        {{-- Registration form details --}}
        <div class="rs-card sv-sec">
            <h4><i class="fa-regular fa-id-badge"></i> Registration Details</h4>
            <div class="sv-body">
                <div class="rs-grid">
                    @foreach($details as $label => $value)
                    <div><small>{{$label}}</small>@if(filled($value))<b>{{$value}}</b>@else<b class="none">Not given</b>@endif</div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- ERP course records --}}
        <div class="rs-card sv-sec">
            <h4><i class="fa-solid fa-book-open"></i> Courses <span class="rs-chip id">{{$courseRows->count()}}</span></h4>
            <div class="sv-body sv-scroll">
                @if($enrollments->count())
                <table class="sv-table">
                    <thead><tr><th>Student ID</th><th>Course</th><th>Batch</th><th>Admitted</th><th class="num">Fee</th><th class="num">Paid</th><th class="num">Due</th><th class="num">Attendance</th><th></th></tr></thead>
                    <tbody>
                        @foreach($enrollments as $enrollment)
                        @php $att = $attendance[$enrollment->id] ?? collect(); @endphp
                        <tr>
                            <td><span class="rs-chip id">{{$enrollment->code}}</span></td>
                            <td><b>{{$enrollment->course?->course('title') ?: 'No course yet'}}</b></td>
                            <td>{{$enrollment->batch?->code ?: '—'}}</td>
                            <td style="white-space:nowrap;">{{$enrollment->admitted_on?->format('d M Y') ?: '—'}}</td>
                            <td class="num">{{$money($enrollment->fee)}}</td>
                            <td class="num">{{$money($enrollment->payments->sum('amount'))}}</td>
                            <td class="num" style="color:{{$enrollment->due() > 0 ? '#c4313b' : '#1f8a46'}};font-weight:800;">{{$money($enrollment->due())}}</td>
                            <td class="num">{{$att->count() ? $att->where('status', 'Present')->count().' / '.$att->count() : '—'}}</td>
                            <td class="num">
                                <form action="{{route('admin.studentAccountsAction',['unlink',$account->id])}}" method="post" onsubmit="return confirm('Unlink this course record from the website account?')">
                                    @csrf
                                    <input type="hidden" name="student" value="{{$enrollment->id}}" />
                                    <button type="submit" class="rs-ic del" title="Unlink from account" style="width:26px;height:26px;"><i class="fa-solid fa-link-slash"></i></button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="sv-empty">{{$waiting ? 'The ERP student record is made when you approve.' : 'No course record is linked yet.'}}</div>
                @endif

                @if($suggestions->count())
                <div class="rs-recs">
                    <h6>Office records with the same mobile number</h6>
                    @foreach($suggestions as $suggestion)
                    <div class="rs-rec" style="color:#7a8aa0;">
                        <span class="rs-chip off">{{$suggestion->code}}</span>
                        <span>{{$suggestion->name}} · {{$suggestion->phone}} · {{$suggestion->course?->course('title') ?: '—'}}</span>
                        <form action="{{route('admin.studentAccountsAction',['link',$account->id])}}" method="post">
                            @csrf
                            <input type="hidden" name="student" value="{{$suggestion->id}}" />
                            <button type="submit" class="rs-mini link"><i class="fa-solid fa-link"></i> Link</button>
                        </form>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
        </div>

        {{-- Payments --}}
        <div class="rs-card sv-sec">
            <h4><i class="fa-solid fa-wallet"></i> Payments <span class="rs-chip id">{{$payments->count()}}</span></h4>
            <div class="sv-body sv-scroll">
                @if($payments->count())
                <table class="sv-table">
                    <thead><tr><th>Receipt</th><th>Course</th><th>Date</th><th>Method</th><th class="num">Amount</th></tr></thead>
                    <tbody>
                        @foreach($payments as $payment)
                        <tr>
                            <td>{{$payment->code}}</td>
                            <td>{{$byId[$payment->student_id]?->course?->course('title') ?: '—'}}</td>
                            <td style="white-space:nowrap;">{{$payment->date?->format('d M Y')}}</td>
                            <td>{{$payment->method ?: '—'}}</td>
                            <td class="num"><b>{{$money($payment->amount)}}</b></td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="sv-empty">No payment yet.</div>
                @endif
            </div>
        </div>

        {{-- Certificates --}}
        <div class="rs-card sv-sec">
            <h4><i class="fa-solid fa-award"></i> Certificates <span class="rs-chip id">{{$certificates->count()}}</span></h4>
            <div class="sv-body sv-scroll">
                @if($certificates->count())
                <table class="sv-table">
                    <thead><tr><th>Certificate No.</th><th>Course</th><th>Issued</th><th>Result</th><th class="num">Open</th></tr></thead>
                    <tbody>
                        @foreach($certificates as $certificate)
                        <tr>
                            <td><b>{{$certificate->code}}</b></td>
                            <td>{{$certificate->course?->course('title') ?: '—'}}</td>
                            <td style="white-space:nowrap;">{{$certificate->issued?->format('d M Y')}}</td>
                            <td>{{$certificate->grade ?: '—'}}@if($certificate->points()) · {{$certificate->points()}}@endif</td>
                            <td class="num">
                                <a class="rs-ic view" style="width:26px;height:26px;" title="Print" href="{{route('erp.certificatePrint', $certificate->id)}}" target="_blank" rel="noopener"><i class="fa-solid fa-print"></i></a>
                                <a class="rs-ic view" style="width:26px;height:26px;" title="Website certificate page" href="{{route('certificates.show', $certificate->code)}}" target="_blank" rel="noopener"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
                @else
                <div class="sv-empty">No certificate yet.</div>
                @endif
            </div>
        </div>
    </section>
</div>
@endsection
