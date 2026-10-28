@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Brands List')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Brands List</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Brands List</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            @isset(json_decode(Auth::user()->permission->permission, true)['brands']['add'])
            <a class="btn btn-outline-primary" href="{{route('admin.brandsAction',['create'])}}">Add Brand</a>
            @endisset
            <a class="btn btn-outline-primary" href="{{route('admin.brands')}}">
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
                    <div class="card-content">
                        <div class="card-body">
                            <div id="accordion">
                                <div
                                    class="card-header collapsed"
                                    data-toggle="collapse"
                                    data-target="#collapseTwo"
                                    aria-expanded="false"
                                    aria-controls="collapseTwo"
                                    id="headingTwo"
                                    style="background: #f5f7fa; padding: 10px; cursor: pointer; border: 1px solid #00b5b8;"
                                >
                                    Search click Here..
                                </div>
                                <div id="collapseTwo" class="collapse" aria-labelledby="headingTwo" data-parent="#accordion" style="border: 1px solid #00b5b8; border-top: 0;">
                                    <div class="card-body">
                                        <form action="{{route('admin.brands')}}">
                                            <div class="row">
                                                <div class="col-md-12 mb-0">
                                                    <div class="input-group">
                                                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="Brand Name" class="form-control {{$errors->has('search')?'error':''}}" />
                                                        <button type="submit" class="btn btn-success btn-sm rounded-0">Search</button>
                                                    </div>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                        <h4 class="card-title">Brands List</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                            <form action="{{route('admin.brands')}}">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="input-group mb-1">
                                            <select class="form-control form-control-sm rounded-0" name="action" required="">
                                                <option value="">Select Action</option>
                                                <option value="1">Active</option>
                                                <option value="2">InActive</option>
                                                <option value="3">Feature</option>
                                                <option value="4">Un-Feature</option>
                                                <option value="5">Delete</option>
                                            </select>
                                            <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                                        </div>
                                    </div>
                                    <div class="col-md-4"></div>
                                    <div class="col-md-4">
                                        <ul class="statuslist">
                                            <li><a href="{{route('admin.brands')}}">All ({{$totals->total}})</a></li>
                                            <li><a href="{{route('admin.brands',['status'=>'active'])}}">Active ({{$totals->active}})</a></li>
                                            <li><a href="{{route('admin.brands',['status'=>'inactive'])}}">Inactive ({{$totals->inactive}})</a></li>
                                        </ul>
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover">
                                        <thead>
                                            <tr>
                                                <th style="min-width: 60px;">
                                                    <label style="cursor: pointer; margin-bottom: 0;"> <input class="checkbox" type="checkbox" class="form-control" id="checkall" /> All <span class="checkCounter"></span> </label>
                                                </th>
                                                <th style="min-width: 300px;">Brand Name</th>
                                                <th style="max-width: 100px;">Image</th>
                                                <th style="min-width: 160px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($brands as $i=>$brand)
                                            <tr>
                                                <td>
                                                    <input class="checkbox" type="checkbox" name="checkid[]" value="{{$brand->id}}" /><br />
                                                    {{$brands->currentpage()==1?$i+1:$i+($brands->perpage()*($brands->currentpage() - 1))+1}}
                                                </td>
                                                <td>
                                                    <span>{{$brand->name}}</span><br />

                                                    @if($brand->status=='active')
                                                    <span class="badge badge-success">Active </span>
                                                    @elseif($brand->status=='inactive')
                                                    <span class="badge badge-danger">Inactive </span>
                                                    @else
                                                    <span class="badge badge-danger">Draft </span>
                                                    @endif @if($brand->fetured==true)
                                                    <span><i class="fa fa-star" style="color: #1ab394;"></i></span>
                                                    @endif
                                                    <span style="font-size: 10px;">
                                                        <i class="fa fa-user" style="color: #1ab394;"></i>
                                                        {{$brand->user?$brand->user->name:'No Author'}}
                                                    </span>
                                                </td>
                                                <td style="padding: 5px; text-align: center;">
                                                    <img src="{{asset($brand->image())}}" style="max-width: 80px; max-height: 50px;" />
                                                </td>
                                                <td class="center">
                                                    <a href="{{route('admin.brandsAction',['edit',$brand->id])}}" class="btn btn-md btn-info">Edit</a>

                                                    @isset(json_decode(Auth::user()->permission->permission, true)['brands']['delete'])
                                                    <a href="{{route('admin.brandsAction',['delete',$brand->id])}}" class="btn btn-md btn-danger" onclick="return confirm('Are You Want To Delete?')"><i class="fa fa-trash"></i></a>
                                                    @endisset
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                    {{$brands->links('pagination')}}
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div>

@endsection @push('js') @endpush
