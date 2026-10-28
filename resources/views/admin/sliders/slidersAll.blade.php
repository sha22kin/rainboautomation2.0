@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Sliders List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Sliders List</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Sliders List</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            @isset(json_decode(Auth::user()->permission->permission, true)['sliders']['add'])
            <a class="btn btn-outline-primary" href="{{route('admin.slidersAction',['create'])}}">Add Slider</a>
            @endisset
            <a class="btn btn-outline-primary" href="{{route('admin.sliders')}}">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Basic Elements start -->
    <section class="basic-elements">
        <div class="row">
            <div class="col-md-12">
                @include(adminTheme().'alerts')
                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Sliders List</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered table-hover">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px;">S:L</th>
                                            <th style="min-width: 300px;">Slider Name</th>
                                            <th style="max-width: 100px;">Image</th>
                                            <th style="min-width: 200px;width: 200px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($sliders as $i=>$slider)
                                        <tr>
                                            <td>
                                                {{$i+1}}
                                            </td>
                                            <td>
                                                <span>{{$slider->name}} 
                                                @if($slider->location)
                                                <span style="color:#ccc;">({{$slider->location}})</span>
                                                @endif
                                                </span><br />
                                                @if($slider->status=='active')
                                                <span><i class="fa fa-check" style="color: #1ab394;"></i></span>
                                                @else
                                                <span><i class="fa fa-times" style="color: #ed5565;"></i></span>
                                                @endif @if($slider->fetured==true)
                                                <span><i class="fa fa-star" style="color: #1ab394;"></i></span>
                                                @endif
                                                <span style="font-size: 10px;color:#ccc;">
                                                    <i class="fa fa-user" style="color: #1ab394;"></i>
                                                    {{$slider->user?$slider->user->name:'No Author'}}
                                                </span>
                                            </td>
                                            <td style="padding: 5px; text-align: center;">
                                                <img src="{{asset($slider->image())}}" style="max-width: 80px; max-height: 50px;" />
                                            </td>
                                            <td class="center">
                                                <a href="{{route('admin.slidersAction',['edit',$slider->id])}}" class="btn btn-md btn-info">Edit</a>

                                                @isset(json_decode(Auth::user()->permission->permission, true)['sliders']['delete'])
                                                <a href="{{route('admin.slidersAction',['delete',$slider->id])}}" class="btn btn-md btn-danger" onclick="return confirm('Are You Want To Delete?')" ><i class="fa fa-trash"></i></a>
                                                @endisset
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{$sliders->links('pagination')}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div>

@endsection @push('js') @endpush
