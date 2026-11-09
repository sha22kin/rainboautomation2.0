@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Registered Students')}}</title>
@endsection
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
            <a class="btn btn-outline-primary" href="{{route('student.register')}}" target="_blank"><i class="fa fa-eye"></i> Website Register Page</a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')

        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                <div class="btn-group" role="group">
                    <a class="btn btn-sm {{!request('filter') ? 'btn-primary' : 'btn-outline-primary'}}" href="{{route('admin.studentAccounts')}}">All ({{$totals->all}})</a>
                    <a class="btn btn-sm {{request('filter')=='unlinked' ? 'btn-primary' : 'btn-outline-primary'}}" href="{{route('admin.studentAccounts',['filter'=>'unlinked'])}}">No Course Linked ({{$totals->unlinked}})</a>
                    <a class="btn btn-sm {{request('filter')=='linked' ? 'btn-primary' : 'btn-outline-primary'}}" href="{{route('admin.studentAccounts',['filter'=>'linked'])}}">Linked ({{$totals->all - $totals->unlinked}})</a>
                    <a class="btn btn-sm {{request('filter')=='blocked' ? 'btn-primary' : 'btn-outline-primary'}}" href="{{route('admin.studentAccounts',['filter'=>'blocked'])}}">Blocked ({{$totals->blocked}})</a>
                </div>
                <form action="{{route('admin.studentAccounts')}}" method="get" style="display:inline-flex; gap:6px; float:right;">
                    <input type="hidden" name="filter" value="{{request('filter')}}" />
                    <input type="text" class="form-control form-control-sm" name="search" value="{{request('search')}}" placeholder="Name, email or mobile" />
                    <button class="btn btn-sm btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <p style="color:#777;">
                        A student sees courses and certificates only after you <b>link</b> the account to their course record (ERP → Students).
                        Records with the same mobile number are suggested below. Check the person before linking.
                    </p>
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th style="width:220px;">Student</th>
                                    <th>Course Records</th>
                                    <th style="width:150px;">Registered</th>
                                    <th style="width:150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($accounts as $account)
                                <tr>
                                    <td>
                                        <b>{{$account->name}}</b> <small style="color:#999;">{{$account->code}}</small><br />
                                        <span style="color:#666;">{{$account->email}}</span><br />
                                        <span style="color:#666;">{{$account->phone}}</span><br />
                                        @if($account->status)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Blocked</span>@endif
                                    </td>
                                    <td>
                                        @foreach($account->enrollments()->with('course')->get() as $enrollment)
                                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px;">
                                            <span class="badge badge-info">{{$enrollment->code}}</span>
                                            <span>{{$enrollment->course?->course('title') ?: '—'}}</span>
                                            <form action="{{route('admin.studentAccountsAction',['unlink',$account->id])}}" method="post" style="margin:0;" onsubmit="return confirm('Unlink this course record?')">
                                                @csrf
                                                <input type="hidden" name="student" value="{{$enrollment->id}}" />
                                                <button type="submit" class="btn btn-sm btn-outline-danger" style="padding:1px 8px;">Unlink</button>
                                            </form>
                                        </div>
                                        @endforeach
                                        @foreach($account->suggestedEnrollments() as $suggestion)
                                        <div style="display:flex; align-items:center; gap:8px; margin-bottom:6px; color:#777;">
                                            <span class="badge badge-secondary">{{$suggestion->code}}</span>
                                            <span>{{$suggestion->name}} · {{$suggestion->phone}} · {{$suggestion->course?->course('title') ?: '—'}}</span>
                                            <form action="{{route('admin.studentAccountsAction',['link',$account->id])}}" method="post" style="margin:0;">
                                                @csrf
                                                <input type="hidden" name="student" value="{{$suggestion->id}}" />
                                                <button type="submit" class="btn btn-sm btn-success" style="padding:1px 8px;">Link</button>
                                            </form>
                                        </div>
                                        @endforeach
                                        @if(!$account->enrollments_count && !$account->suggestedEnrollments()->count())
                                        <span style="color:#999;">No course record with this mobile number yet.</span>
                                        @endif
                                    </td>
                                    <td>{{$account->created_at?->format('d M Y, h:i A')}}<br /><small style="color:#999;">Last login: {{$account->last_login_at?->diffForHumans() ?: '—'}}</small></td>
                                    <td style="padding:5px;">
                                        <form action="{{route('admin.studentAccountsAction',[$account->status ? 'block' : 'unblock',$account->id])}}" method="post" style="display:inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm {{$account->status ? 'btn-warning' : 'btn-success'}}">{{$account->status ? 'Block' : 'Unblock'}}</button>
                                        </form>
                                        <form action="{{route('admin.studentAccountsAction',['delete',$account->id])}}" method="post" style="display:inline;" onsubmit="return confirm('Are You Want To Delete?')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-danger">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align:center; color:#999;">No student has registered yet.</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <div style="margin-top:15px;">{{$accounts->links('pagination')}}</div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
