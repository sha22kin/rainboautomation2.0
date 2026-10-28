@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Course Inquiry')}}</title>
@endsection @push('css')
<style type="text/css">
    .inquiryDetails th { width: 160px; color: #666; font-weight: 600; background: #fafbfd; }
    .inquiryMessage { white-space: pre-wrap; background: #fafbfd; border: 1px solid #e3ebf3; border-radius: 6px; padding: 15px; min-height: 80px; margin: 0; }
</style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Course Inquiry</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.trainingInquiries')}}">Course Inquiries</a></li>
                    <li class="breadcrumb-item active">{{$inquiry->name}}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            <a class="btn btn-outline-primary" href="{{route('admin.trainingInquiries')}}">BACK</a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')
        <div class="row">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Inquiry Details</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <table class="table table-bordered inquiryDetails">
                                <tr>
                                    <th>Name</th>
                                    <td>{{$inquiry->name}}</td>
                                </tr>
                                <tr>
                                    <th>Phone</th>
                                    <td><a href="tel:{{preg_replace('/[^0-9+]/','',$inquiry->inquiry('phone'))}}">{{$inquiry->inquiry('phone')}}</a></td>
                                </tr>
                                <tr>
                                    <th>Email</th>
                                    <td>
                                        @if($inquiry->email)
                                        <a href="mailto:{{$inquiry->email}}">{{$inquiry->email}}</a>
                                        @else
                                        <span style="color:#aaa;">Not given</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Course</th>
                                    <td>
                                        {{$inquiry->inquiry('course_title')}}
                                        @if($course)
                                        <br />
                                        <span style="font-size: 12px; color: #888;">{{$course->course('duration')}} · {{$course->courseFee()}}</span>
                                        <a href="{{route('admin.trainingCoursesAction',['edit',$course->id])}}" style="font-size: 12px; margin-left: 6px;">Open course</a>
                                        @else
                                        <br /><span style="font-size: 12px; color: #aaa;">(this course has been deleted)</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr>
                                    <th>Received</th>
                                    <td>{{$inquiry->created_at->format('d M Y, h:i A')}} <span style="color:#aaa;">({{$inquiry->created_at->diffForHumans()}})</span></td>
                                </tr>
                            </table>

                            <label style="font-weight: 600; color: #666;">Message</label>
                            @if($inquiry->inquiry('message'))
                            <p class="inquiryMessage">{{$inquiry->inquiry('message')}}</p>
                            @else
                            <p class="inquiryMessage" style="color:#aaa;">No message.</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Inquiry Action</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <a href="tel:{{preg_replace('/[^0-9+]/','',$inquiry->inquiry('phone'))}}" class="btn btn-success btn-block"><i class="fa fa-phone"></i> Call {{$inquiry->inquiry('phone')}}</a>
                            @if($inquiry->email)
                            <a href="mailto:{{$inquiry->email}}?subject={{rawurlencode('Re: '.$inquiry->inquiry('course_title'))}}" class="btn btn-info btn-block"><i class="fa fa-envelope"></i> Reply by Email</a>
                            @endif
                            <a href="{{route('admin.trainingInquiriesAction',['unread',$inquiry->id])}}" class="btn btn-outline-secondary btn-block">Mark as Unread</a>
                            <a href="{{route('admin.trainingInquiriesAction',['delete',$inquiry->id])}}" class="btn btn-outline-danger btn-block" onclick="return confirm('Are You Want To Delete?')"><i class="fa fa-trash"></i> Delete Inquiry</a>
                            @if($inquiry->inquiry('ip'))
                            <p style="color:#aaa; font-size: 11px; margin: 12px 0 0;">Sent from IP {{$inquiry->inquiry('ip')}}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

@endsection
