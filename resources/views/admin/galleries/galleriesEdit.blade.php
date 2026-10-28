@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Gallery Edit')}}</title>
@endsection @push('css')

<style type="text/css">
    .fileUpload-div {
        border: 2px dotted #e3e3e3;
        padding: 25px;
        text-align: center;
    }

    .fileUpload-div p {
        font-size: 20px;
        color: silver;
        text-transform: uppercase;
    }
    .fileUpload-div label {
        margin: 0;
        border: 1px solid #dc379b;
    }
    .fileUpload-div i {
        font-size: 60px;
        cursor: pointer;
        color: #c6c2c2;
    }
</style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Gallery Edit</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Gallery Edit</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-outline-primary" href="{{route('admin.galleries')}}">BACK</a>
            @isset(json_decode(Auth::user()->permission->permission, true)['galleries']['add'])
            <a class="btn btn-outline-primary" href="{{route('admin.galleriesAction',['create'])}}">Add Gallery</a>
          	@endisset
            <a class="btn btn-outline-primary reloadPage" href="javascript:void(0)">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Basic Elements start -->
    <section class="basic-elements">
        @include(adminTheme().'alerts')

        <div class="row">
            <div class="col-md-12">
                <form action="{{route('admin.galleriesAction',['update',$gallery->id])}}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Gallery Edit</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="name">Gallery Name(*) </label>
                                        <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Gallery Name" value="{{$gallery->name?:old('name')}}" required="" />
                                        @if ($errors->has('name'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="fetured">Gallery Location</label>
                                        <select class="form-control" name="location">
                                            <option value="">Select Location</option>
                                            <option value="Front Page Gallery" {{$gallery->location=='Front Page Gallery'?'selected':''}}>Front Page Gallery</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="image">Featured Image</label>
                                        <input type="file" name="image" class="form-control {{$errors->has('image')?'error':''}}" />
                                        @if ($errors->has('image'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('image') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-2">
                                        <img src="{{asset($gallery->image())}}" style="max-width: 100px;" />
                                        @isset(json_decode(Auth::user()->permission->permission, true)['galleries']['add'])
                                        @if($gallery->imageFile)
                                        <a href="{{route('admin.mediesDelete',$gallery->imageFile->id)}}" class="mediaDelete" style="color: red;"><i class="fa fa-trash"></i></a>
                                        @endif
                                        @endisset
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description </label>
                                    <textarea name="description" rows="5" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{!!$gallery->description!!}</textarea>
                                    @if ($errors->has('description'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                                    @endif
                                </div>
                                @isset(json_decode(Auth::user()->permission->permission, true)['galleries']['add'])
                                    <div class="fileUpload-div">
                                        <div>
                                            <p>Click To Upload Images (Multiple)</p>
                                        </div>
                                        <div>
                                            @if ($errors->has('images'))
                                            <p style="color: red; margin: 0; font-size: 10px;">The Tags Must Be (jpeg,png,jpg,gif,svg) max:2024 MB</p>
                                            @endif
                                            <small>(jpeg,png,jpg,gif,svg) max:25 MB</small>
                                        </div>
                                        <div>
                                            <label>
                                                <input type="file" name="images[]" multiple="" class="fileUpload" />
                                            </label>
                                            
                                        </div>
                                    </div>
                                @endisset
                                <hr>
                                <div class="sliderImagesList">
                                @include('admin.galleries.includes.galleriesImages')
                                </div>
                                <div class="row">
                                    <div class="form-group col-4">
                                        <label for="status">Gallery Status</label>
                                        <select class="form-control" name="status">
                                            <option value="active" {{$gallery->status=='active'?'checked':''}}>Active</option>
                                            <option value="inactive" {{$gallery->status=='inactive' || $gallery->status=='temp'?'checked':''}}>Inactive</option>
                                        </select>
                                    </div>
                                </div>
                                @isset(json_decode(Auth::user()->permission->permission, true)['galleries']['add'])
                                <button type="submit" class="btn btn-primary btn-md mr-sm-1 mb-1 mb-sm-0">Save changes</button>
                                @endisset
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div>

@endsection @push('js')
<script type="text/javascript">

</script>

@endpush
