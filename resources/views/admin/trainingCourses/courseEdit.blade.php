@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Course Edit')}}</title>
@endsection @push('css')
<style type="text/css">
    .coursePreview { width: 100%; max-height: 170px; object-fit: cover; border-radius: 8px; background: #eef3f8; }
</style>
@endpush @section('contents')

@php
    $data = $course->course();
    $isUploaded = $data['image'] && !preg_match('#^https?://#i', $data['image']);
@endphp

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">{{$course->status=='temp' ? 'Add Course' : 'Course Edit'}}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.trainingCourses')}}">Training Courses</a></li>
                    <li class="breadcrumb-item active">{{$course->status=='temp' ? 'Add Course' : 'Course Edit'}}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            <a class="btn btn-outline-primary" href="{{route('admin.trainingCourses')}}">BACK</a>
            <a class="btn btn-outline-primary" href="{{route('admin.trainingCoursesAction','create')}}">Add Course</a>
            <a class="btn btn-outline-primary" href="{{route('admin.trainingCoursesAction',['edit',$course->id])}}"><i class="fa-solid fa-rotate"></i></a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.trainingCoursesAction',['update',$course->id])}}" method="post" enctype="multipart/form-data">
            @csrf
            <div class="row">
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Course Details</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">Course Title *</label>
                                    <input type="text" class="form-control {{$errors->has('title')?'error':''}}" id="title" name="title" placeholder="Enter Course Title" value="{{old('title', $data['title'])}}" required />
                                    @if ($errors->has('title'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-6">
                                        <label for="category">Category *</label>
                                        <select class="form-control {{$errors->has('category')?'error':''}}" id="category" name="category">
                                            @foreach($categories as $key=>$category)
                                            <option value="{{$key}}" {{old('category', $data['category'])==$key?'selected':''}}>{{$category['label']}} ({{$category['filter']}})</option>
                                            @endforeach
                                        </select>
                                        @if ($errors->has('category'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('category') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-3 col-6">
                                        <label for="duration">Duration</label>
                                        <input type="text" class="form-control {{$errors->has('duration')?'error':''}}" id="duration" name="duration" placeholder="30 Hours" value="{{old('duration', $data['duration'])}}" />
                                        @if ($errors->has('duration'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('duration') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-3 col-6">
                                        <label for="fee">Course Fee (BDT)</label>
                                        <input type="number" min="0" class="form-control {{$errors->has('fee')?'error':''}}" id="fee" name="fee" placeholder="12000" value="{{old('fee', $data['fee'])}}" />
                                        @if ($errors->has('fee'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('fee') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Short Description</label>
                                    <textarea class="form-control {{$errors->has('description')?'error':''}}" id="description" name="description" rows="3" placeholder="Shown on the course card and in Details">{{old('description', $data['description'])}}</textarea>
                                    @if ($errors->has('description'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                                    @endif
                                </div>
                                <div class="form-group mb-0">
                                    <label for="modules">Course Modules <small style="color:#999;">(one per line – shown in Details)</small></label>
                                    <textarea class="form-control {{$errors->has('modules')?'error':''}}" id="modules" name="modules" rows="6" placeholder="PLC hardware, architecture and electrical safety&#10;Digital I/O wiring, sensors and actuators">{{old('modules', implode("\n", $data['modules']))}}</textarea>
                                    @if ($errors->has('modules'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('modules') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Course Image</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <img src="{{$course->courseImage()}}" class="coursePreview" id="coursePreview" alt="" />
                                </div>
                                <div class="form-group">
                                    <label for="image">Upload Image</label>
                                    <input type="file" id="image" name="image" accept="image/*" class="form-control {{$errors->has('image')?'error':''}}" />
                                    @if ($errors->has('image'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('image') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="image_url">or Image Link</label>
                                    <input type="url" id="image_url" name="image_url" class="form-control {{$errors->has('image_url')?'error':''}}" placeholder="https://..." value="{{old('image_url', $isUploaded ? '' : $data['image'])}}" />
                                    @if ($errors->has('image_url'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('image_url') }}</p>
                                    @endif
                                </div>
                                <div class="form-group mb-0">
                                    <label for="alt">Image Alt Text</label>
                                    <input type="text" id="alt" name="alt" class="form-control {{$errors->has('alt')?'error':''}}" placeholder="Short description of the image" value="{{old('alt', $data['alt'])}}" />
                                    @if ($errors->has('alt'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('alt') }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Course Action</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="status">Course Status</label>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" id="status" name="status" {{$course->status!='inactive'?'checked':''}}/>
                                        <label class="custom-control-label" for="status">Active (show on website)</label>
                                    </div>
                                </div>
                                <button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save changes</button>
                                @if($course->status!='temp')
                                <a href="{{route('admin.trainingCoursesAction',['delete',$course->id])}}" class="btn btn-outline-danger mb-1 mb-sm-0" onclick="return confirm('Are You Want To Delete?')">Delete</a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

@endsection @push('js')
<script>
    // Preview the selected image file / image link
    (function () {
        var preview = document.getElementById('coursePreview');
        document.getElementById('image').addEventListener('change', function () {
            if (this.files && this.files[0]) preview.src = URL.createObjectURL(this.files[0]);
        });
        document.getElementById('image_url').addEventListener('change', function () {
            if (this.value) preview.src = this.value;
        });
    })();
</script>
@endpush
