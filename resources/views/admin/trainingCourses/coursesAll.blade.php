@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Training Courses')}}</title>
@endsection @push('css')
<style type="text/css">
    .courseTable td { vertical-align: middle; }
    .courseTable .courseThumb { width: 70px; height: 46px; object-fit: cover; border-radius: 6px; background: #eef3f8; }
    .courseTable .courseCat { display: inline-block; color: #fff; font-size: 10px; font-weight: 700; letter-spacing: .4px; text-transform: uppercase; border-radius: 20px; padding: 3px 9px; }
    .courseTable .orderBtns a { display: inline-block; padding: 2px 7px; }
    .courseFilter { display: flex; gap: 6px; flex-wrap: wrap; margin: 0; padding: 0; list-style: none; }
    .courseFilter a { display: inline-block; font-size: 12px; padding: 4px 10px; border: 1px solid #e3ebf3; border-radius: 20px; color: #555; }
    .courseFilter a.active { background: #1e9ff2; border-color: #1e9ff2; color: #fff; }
</style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Training Courses</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Training Courses</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            @if($page)
            <a class="btn btn-outline-primary" href="{{route('admin.trainingCoursesAction','create')}}">Add Course</a>
            <a class="btn btn-outline-primary" href="{{route('pageView',$page->slug)}}" target="_blank"><i class="fa fa-eye"></i></a>
            @endif
            <a class="btn btn-outline-primary" href="{{route('admin.trainingCourses')}}"><i class="fa-solid fa-rotate"></i></a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        <div class="row">
            <div class="col-md-12">
                @include('admin.alerts')

                @if($page)
                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Training Courses <small style="color:#999;">(Training Academy page · {{$totals->active}} shown on website)</small></h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="row mb-1">
                                <div class="col-md-5 mb-1">
                                    <form action="{{route('admin.trainingCourses')}}">
                                        <input type="hidden" name="status" value="{{request()->status}}" />
                                        <input type="hidden" name="category" value="{{request()->category}}" />
                                        <div class="input-group">
                                            <input type="text" name="search" value="{{request()->search}}" placeholder="Search course name" class="form-control form-control-sm" />
                                            <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                        </div>
                                    </form>
                                </div>
                                <div class="col-md-7 mb-1">
                                    <ul class="statuslist" style="float:right;">
                                        <li><a href="{{route('admin.trainingCourses',['category'=>request()->category])}}">All ({{$totals->total}})</a></li>
                                        <li><a href="{{route('admin.trainingCourses',['status'=>'active','category'=>request()->category])}}">Active ({{$totals->active}})</a></li>
                                        <li><a href="{{route('admin.trainingCourses',['status'=>'inactive','category'=>request()->category])}}">Inactive ({{$totals->inactive}})</a></li>
                                    </ul>
                                </div>
                            </div>

                            <ul class="courseFilter mb-1">
                                <li><a href="{{route('admin.trainingCourses',['status'=>request()->status])}}" class="{{request()->category?'':'active'}}">All Categories</a></li>
                                @foreach(trainingCategories() as $key=>$category)
                                <li><a href="{{route('admin.trainingCourses',['status'=>request()->status,'category'=>$key])}}" class="{{request()->category==$key?'active':''}}">{{$category['filter']}}</a></li>
                                @endforeach
                            </ul>

                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover courseTable">
                                    <thead>
                                        <tr>
                                            <th style="width: 45px;">SL</th>
                                            <th style="min-width: 280px;">Course</th>
                                            <th style="width: 90px;">Image</th>
                                            <th style="min-width: 100px;">Duration</th>
                                            <th style="min-width: 100px;">Fee (BDT)</th>
                                            <th style="width: 80px;">Order</th>
                                            <th style="width: 130px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @forelse($courses as $i=>$course)
                                        <tr>
                                            <td>{{$i+1}}</td>
                                            <td>
                                                <a href="{{route('admin.trainingCoursesAction',['edit',$course->id])}}">{{$course->course('title')}}</a>
                                                <br />
                                                <span class="courseCat" style="background: {{$course->courseCategory()['color']}};">{{$course->courseCategory()['label']}}</span>
                                                @if($course->status=='active')
                                                <span class="badge badge-success">Active</span>
                                                @else
                                                <span class="badge badge-danger">Inactive</span>
                                                @endif
                                                <span style="color: #aaa; font-size: 12px;"><i class="fa fa-list" style="color: #1ab394;"></i> {{count($course->course('modules'))}} modules</span>
                                            </td>
                                            <td style="padding: 5px; text-align: center;">
                                                <img src="{{$course->courseImage()}}" class="courseThumb" loading="lazy" alt="" />
                                            </td>
                                            <td>{{$course->course('duration')}}</td>
                                            <td>{{$course->courseFee()}}</td>
                                            <td class="orderBtns" style="text-align: center;">
                                                @if(!request()->status && !request()->category && !request()->search)
                                                <a href="{{route('admin.trainingCoursesAction',['up',$course->id])}}" title="Move up"><i class="fa fa-arrow-up"></i></a>
                                                <a href="{{route('admin.trainingCoursesAction',['down',$course->id])}}" title="Move down"><i class="fa fa-arrow-down"></i></a>
                                                @else
                                                <span style="color:#ccc;" title="Clear filters to change order">—</span>
                                                @endif
                                            </td>
                                            <td style="padding: 5px;">
                                                <a href="{{route('admin.trainingCoursesAction',['edit',$course->id])}}" class="btn btn-sm btn-info">Edit</a>
                                                <a href="{{route('admin.trainingCoursesAction',['delete',$course->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Are You Want To Delete?')">Delete</a>
                                            </td>
                                        </tr>
                                        @empty
                                        <tr>
                                            <td colspan="7" style="text-align: center; color: #999;">No course found.</td>
                                        </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
                @else
                <div class="card">
                    <div class="card-body">
                        No active page uses the <b>Training Academy</b> template. Create or edit a page in
                        <a href="{{route('admin.pages')}}">Pages</a> and set its Page Template to "Training Academy".
                    </div>
                </div>
                @endif
            </div>
        </div>
    </section>
</div>

@endsection
