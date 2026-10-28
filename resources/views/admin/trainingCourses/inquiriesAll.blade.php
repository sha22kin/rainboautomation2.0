@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Course Inquiries')}}</title>
@endsection @push('css')
<style type="text/css">
    .inquiryTable td { vertical-align: middle; }
    .inquiryTable tr.unread td { background: #f2f8ff; }
    .inquiryTable tr.unread .inquiryName { font-weight: 700; }
    .inquiryTable .inquiryMsg { color: #888; font-size: 12px; }
    .inquiryTable a.rowLink { color: inherit; }
</style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Course Inquiries</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.trainingCourses')}}">Training Courses</a></li>
                    <li class="breadcrumb-item active">Course Inquiries</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            <a class="btn btn-outline-primary" href="{{route('admin.trainingCourses')}}">All Courses</a>
            <a class="btn btn-outline-primary" href="{{route('admin.trainingInquiries')}}"><i class="fa-solid fa-rotate"></i></a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        <div class="row">
            <div class="col-md-12">
                @include('admin.alerts')
                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Course Inquiries <small style="color:#999;">(from the Training Academy "Inquiry" form)</small></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{route('admin.trainingInquiries')}}">
                                <input type="hidden" name="status" value="{{request()->status}}" />
                                <div class="row">
                                    <div class="col-md-4 mb-1">
                                        <select name="course" class="form-control form-control-sm" onchange="this.form.submit()">
                                            <option value="">All Courses</option>
                                            @foreach($courses as $course)
                                            <option value="{{$course->id}}" {{request()->course==$course->id?'selected':''}}>{{$course->course('title')}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <div class="input-group">
                                            <input type="text" name="search" value="{{request()->search}}" placeholder="Name, phone, email or message" class="form-control form-control-sm" />
                                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                        </div>
                                    </div>
                                    <div class="col-md-4 mb-1">
                                        <ul class="statuslist" style="float:right;">
                                            <li><a href="{{route('admin.trainingInquiries',['course'=>request()->course])}}">All ({{(int)$totals->total}})</a></li>
                                            <li><a href="{{route('admin.trainingInquiries',['status'=>'unread','course'=>request()->course])}}">Unread ({{(int)$totals->unread}})</a></li>
                                            <li><a href="{{route('admin.trainingInquiries',['status'=>'read','course'=>request()->course])}}">Read ({{(int)$totals->read}})</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </form>

                            <form action="{{route('admin.trainingInquiries')}}" method="post">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group mb-1">
                                            <select class="form-control form-control-sm rounded-0" name="action" required="">
                                                <option value="">Select Action</option>
                                                <option value="read">Mark as Read</option>
                                                <option value="unread">Mark as Unread</option>
                                                <option value="delete">Delete</option>
                                            </select>
                                            <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                                        </div>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-bordered table-hover inquiryTable">
                                        <thead>
                                            <tr>
                                                <th style="width: 70px;">
                                                    <label style="cursor: pointer; margin-bottom: 0;"><input class="checkbox" type="checkbox" id="checkall" /> All <span class="checkCounter"></span></label>
                                                </th>
                                                <th style="min-width: 200px;">Name / Contact</th>
                                                <th style="min-width: 260px;">Course / Message</th>
                                                <th style="min-width: 120px;">Date</th>
                                                <th style="width: 130px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($inquiries as $i=>$inquiry)
                                            <tr class="{{$inquiry->status=='unread'?'unread':''}}">
                                                <td>
                                                    <input class="checkbox" type="checkbox" name="checkid[]" value="{{$inquiry->id}}" /><br />
                                                    {{$inquiries->firstItem()+$i}}
                                                </td>
                                                <td>
                                                    <a href="{{route('admin.trainingInquiriesAction',['view',$inquiry->id])}}" class="rowLink inquiryName">{{$inquiry->name}}</a>
                                                    @if($inquiry->status=='unread')
                                                    <span class="badge badge-danger">New</span>
                                                    @endif
                                                    <br />
                                                    <span style="font-size: 12px;"><i class="fa fa-phone" style="color: #1ab394;"></i> {{$inquiry->inquiry('phone')}}</span>
                                                    @if($inquiry->email)
                                                    <br /><span style="font-size: 12px;"><i class="fa fa-envelope" style="color: #1ab394;"></i> {{$inquiry->email}}</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    {{$inquiry->inquiry('course_title')}}
                                                    @if($inquiry->inquiry('message'))
                                                    <div class="inquiryMsg">{{Str::limit($inquiry->inquiry('message'), 90)}}</div>
                                                    @endif
                                                </td>
                                                <td style="font-size: 12px;">
                                                    {{$inquiry->created_at->format('d M Y')}}<br />
                                                    <span style="color: #999;">{{$inquiry->created_at->format('h:i A')}}</span>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <a href="{{route('admin.trainingInquiriesAction',['view',$inquiry->id])}}" class="btn btn-sm btn-info">View</a>
                                                    <a href="{{route('admin.trainingInquiriesAction',['delete',$inquiry->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Are You Want To Delete?')">Delete</a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" style="text-align: center; color: #999;">No inquiry found.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                    {{$inquiries->links('pagination')}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
