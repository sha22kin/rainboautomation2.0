@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Registered Students')}}</title>
@endsection
@push('css')
@include(adminTheme().'studentAccounts.style')
@endpush
@section('contents')

{{-- Students registered on the website ($accounts = StudentAccount, with enrollments = linked ERP student records) --}}
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Registered Students</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Registered Students</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            <a class="btn btn-outline-primary" href="{{route('student.register')}}" target="_blank"><i class="fa-solid fa-arrow-up-right-from-square"></i> Website Register Page</a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')

        @php
            $filterLink = fn ($filter) => route('admin.studentAccounts', array_filter(['filter' => $filter, 'search' => request('search')]));
            $filters = [
                ['', 'All', $totals->all, ''],
                ['pending', 'Waiting for Approval', $totals->pending, 'wait'],
                ['unlinked', 'No Course Linked', $totals->unlinked, ''],
                ['linked', 'Linked', $totals->all - $totals->unlinked, ''],
                ['blocked', 'Blocked', $totals->blocked, ''],
            ];
        @endphp
        <div class="rs-card">
            <div class="rs-top">
                <div class="rs-filters">
                    @foreach($filters as [$key, $label, $count, $class])
                    <a class="{{$class}} {{(string) request('filter') === $key ? 'on' : ''}}" href="{{$filterLink($key)}}">{{$label}}<b>{{$count}}</b></a>
                    @endforeach
                </div>
                <form class="rs-search" action="{{route('admin.studentAccounts')}}" method="get">
                    <input type="hidden" name="filter" value="{{request('filter')}}" />
                    <input type="text" name="search" value="{{request('search')}}" placeholder="Name, email or mobile" />
                    <button type="submit" title="Search"><i class="fa-solid fa-magnifying-glass"></i></button>
                </form>
            </div>
            <p class="rs-note">
                <i class="fa-solid fa-circle-info"></i>
                New registrations wait for your <b>approval</b> <i class="fa-solid fa-check"></i>: only then can the student log in, and the profile and ERP student record are made.
                Click <i class="fa-regular fa-eye"></i> to open the student's page.
            </p>

            <div class="rs-wrap">
                <table class="rs-table rs-list">
                    <thead>
                        <tr>
                            <th>Student</th>
                            <th class="c-mob">Mobile</th>
                            <th class="c-course">Wants Course</th>
                            <th class="c-status">Status</th>
                            <th class="c-date">Registered</th>
                            <th class="c-act" style="text-align:right;">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($accounts as $account)
                        @php
                            $waiting = !$account->isApproved();
                            $studentId = $account->enrollments()->orderBy('id')->first()?->code;
                        @endphp
                        <tr class="rs-row {{$waiting ? 'wait' : ''}}">
                            <td>
                                <div class="rs-who">
                                    <span class="rs-avatar">@if($account->photoUrl())<img src="{{$account->photoUrl()}}" alt="">@else{{$account->initials()}}@endif</span>
                                    <div style="min-width:0">
                                        <div class="rs-name">{{$account->name}}</div>
                                        <div class="rs-sub">{{$account->code}}@if($studentId) · {{$studentId}}@endif · {{$account->email}}</div>
                                    </div>
                                </div>
                            </td>
                            <td class="c-mob rs-cut">{{$account->phone}}</td>
                            <td class="c-course"><div class="rs-course" title="{{$account->preferredCourse?->course('title')}}">{{$account->preferredCourse?->course('title') ?: '—'}}</div></td>
                            <td class="c-status">
                                @if($waiting)<span class="rs-chip wait">Waiting</span>
                                @elseif(!$account->status)<span class="rs-chip off">Blocked</span>
                                @else<span class="rs-chip ok">Active</span>@endif
                            </td>
                            <td class="c-date"><div class="rs-date">{{$account->created_at?->format('d M Y')}}<small>{{$account->created_at?->format('h:i A')}}</small></div></td>
                            <td>
                                <div class="rs-acts">
                                    <a class="rs-ic view" title="View student" href="{{route('admin.studentAccountsView', $account->id)}}"><i class="fa-regular fa-eye"></i></a>
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
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="6" class="rs-empty"><i class="fa-regular fa-user"></i> No student found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div style="margin-top:15px;">{{$accounts->links('pagination')}}</div>
    </section>
</div>

@endsection
