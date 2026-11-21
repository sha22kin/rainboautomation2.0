@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Course Requests')}}</title>
@endsection
@push('css')
@include(adminTheme().'studentAccounts.style')
<style>
.rq-list th.c-course{width:34%}.rq-list th.c-status{width:92px}.rq-list th.c-date{width:92px}.rq-list th.c-act{width:112px}
.rq-pay{font-size:12px;color:#33506f;line-height:1.35}
.rq-pay b{color:#172338}
.rq-pay small{display:block;color:#8494a8;font-size:11px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.rq-note{font-size:11px;color:#8494a8;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
@media(max-width:1350px){.rq-list .c-date{display:none}}
@media(max-width:800px){.rq-list .c-course{display:none}}
</style>
@endpush
@section('contents')
{{-- "Buy a Course" requests from student profiles (AdminController@courseRequests) --}}
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Course Requests</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Course Requests</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')
        @php
            $money = fn ($n) => '৳ '.number_format((float) $n);
            $link = fn ($f) => route('admin.courseRequests', array_filter(['filter' => $f === 'pending' ? null : $f, 'search' => request('search')]));
            $filters = [['pending', 'Waiting for Approval', $totals['pending'] ?? 0, 'wait'], ['approved', 'Approved', $totals['approved'] ?? 0, ''], ['rejected', 'Rejected', $totals['rejected'] ?? 0, ''], ['all', 'All', $totals->sum(), '']];
        @endphp
        <div class="rs-card">
            <div class="rs-top">
                <div class="rs-filters">
                    @foreach($filters as [$key, $label, $count, $class])
                    <a class="{{$class}} {{$filter === $key ? 'on' : ''}}" href="{{$link($key)}}">{{$label}}<b>{{$count}}</b></a>
                    @endforeach
                </div>
                <form class="rs-search" action="{{route('admin.courseRequests')}}" method="get">
                    <input type="hidden" name="filter" value="{{$filter === 'pending' ? '' : $filter}}" />
                    <input type="text" name="search" value="{{request('search')}}" placeholder="Name, email or mobile" />
                    <button type="submit" title="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
            <p class="rs-note">
                <i class="fa-solid fa-circle-info"></i>
                Students ask for a course from their profile (<b>Buy a Course</b>). <b>Approve</b> <i class="fa-solid fa-check"></i> adds the course to the student
                (ERP → Student Admissions, with the course fee) and to the student's profile.
            </p>

            <div class="rs-wrap">
                <table class="rs-table rs-list rq-list">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th class="c-course">Course</th>
                            <th class="c-status">Status</th>
                            <th class="c-date">Sent</th>
                            <th class="c-act" style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($requests as $request)
                        @php $account = $request->account; $title = $request->course?->course('title') ?: 'Course removed'; @endphp
                        <tr class="rs-row {{$request->isPending() ? 'wait' : ''}}">
                            <td>
                                <div class="rs-who">
                                    <span class="rs-avatar">@if($account?->photoUrl())<img src="{{$account->photoUrl()}}" alt="">@else{{$account?->initials() ?: '?'}}@endif</span>
                                    <div style="min-width:0">
                                        <div class="rs-name">{{$account?->name ?: 'Deleted account'}}</div>
                                        <div class="rs-sub">{{$request->code}}@if($account) · {{$account->phone}}@endif</div>
                                    </div>
                                </div>
                            </td>
                            <td class="c-course">
                                <div class="rs-course" title="{{$title}}"><b>{{$title}}</b></div>
                                <div class="rq-note">{{$request->course?->courseFee()}}@if($request->note) · {{$request->note}}@endif</div>
                            </td>
                            <td class="c-status">
                                @if($request->status === 'pending')<span class="rs-chip wait">Waiting</span>
                                @elseif($request->status === 'approved')<span class="rs-chip ok">Approved</span>
                                @else<span class="rs-chip off" title="{{$request->admin_note}}">Rejected</span>@endif
                            </td>
                            <td class="c-date"><div class="rs-date">{{$request->created_at?->format('d M Y')}}<small>{{$request->created_at?->format('h:i A')}}</small></div></td>
                            <td>
                                <div class="rs-acts">
                                    @if($account)<a class="rs-ic view" title="View student" href="{{route('admin.studentAccountsView', $account->id)}}"><i class="fa-regular fa-eye"></i></a>@endif
                                    @if($request->isPending())
                                    <form action="{{route('admin.courseRequestsAction',['approve',$request->id])}}" method="post" onsubmit="return confirm('Approve? {{e($account?->name)}} is enrolled in {{e($title)}}.')">
                                        @csrf
                                        <button type="submit" class="rs-ic approve" title="Approve and enroll"><i class="fa-solid fa-check"></i></button>
                                    </form>
                                    <form action="{{route('admin.courseRequestsAction',['reject',$request->id])}}" method="post" onsubmit="var n=prompt('Reject this request? Reason for the student (optional):','');if(n===null)return false;this.admin_note.value=n;return true;">
                                        @csrf
                                        <input type="hidden" name="admin_note" value="" />
                                        <button type="submit" class="rs-ic reject" title="Reject"><i class="fa-solid fa-xmark"></i></button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="rs-empty"><i class="fa-solid fa-cart-shopping"></i> No course request here.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div style="margin-top:15px;">{{$requests->links('pagination')}}</div>
    </section>
</div>
@endsection
