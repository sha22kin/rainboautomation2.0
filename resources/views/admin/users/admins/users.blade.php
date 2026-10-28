@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Admin Users')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Admin Users</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Admin Users</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            @isset(json_decode(Auth::user()->permission->permission, true)['adminUsers']['add'])
            <button class="btn btn-outline-primary" type="button" data-toggle="modal" data-target="#AddUser">
                Add User
            </button>
            @endisset
            <a class="btn btn-outline-primary" href="{{route('admin.usersAdmin')}}">
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
                                        <form action="{{route('admin.usersAdmin')}}">
                                            <div class="row">
                                                <div class="col-md-4 mb-1">
                                                <select name="role" class="form-control {{$errors->has('role')?'error':''}}">
                                                    <option value="">Select Role</option>
                                                    @foreach($roles as $role)
                                                    <option value="{{$role->id}}" {{request()->role==$role->id?'selected':''}}>{{$role->name}}</option>
                                                    @endforeach
                                                </select>
                                                </div>
                                                <div class="col-md-8 mb-1">
                                                    <div class="input-group">
                                                        <input type="text" name="search" value="{{request()->search?request()->search:''}}" placeholder="User Name, Email, Mobile" class="form-control {{$errors->has('search')?'error':''}}" />
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
                        <h4 class="card-title">Admin users List</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                        <form action="{{route('admin.usersAdmin')}}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group mb-1">
                                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                                            <option value="">Select Action</option>
                                            <option value="1">Active</option>
                                            <option value="2">Inactive</option>
                                            <option value="5">Remove</option>
                                        </select>
                                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    
                                </div>
                                <div class="col-md-4">
                                    <ul class="statuslist">
                                        <li><a href="{{route('admin.usersAdmin')}}">All ({{$totals->total}})</a></li>
                                        <li><a href="{{route('admin.usersAdmin',['status'=>'active'])}}">Active ({{$totals->active}})</a></li>
                                        <li><a href="{{route('admin.usersAdmin',['status'=>'inactive'])}}">Inactive ({{$totals->inactive}})</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 100px; width: 100px;">
                                            <label style="cursor: pointer; margin-bottom: 0;"> <input class="checkbox" type="checkbox" class="form-control" id="checkall" /> All <span class="checkCounter"></span> </label>
                                            </th>
                                            <th style="min-width: 80px;">Image</th>
                                            <th style="min-width: 250px; width: 250px;">Name</th>
                                            <th style="min-width: 150px;">Email</th>
                                            <th style="min-width: 100px;">Role</th>
                                            <th style="min-width: 80px;">Status</th>
                                            <th style="min-width: 80px; width: 80px;">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($users as $i=>$user)
                                        <tr>
                                            <td>
                                                @if($user->id==Auth::id()) @else
                                                <input class="checkbox" type="checkbox" name="checkid[]" value="{{$user->id}}" />
                                                @endif
                                                {{$users->currentpage()==1?$i+1:$i+($users->perpage()*($users->currentpage() - 1))+1}}
                                            </td>
                                            <td style="padding: 0 3px; text-align: center;">
                                                <span>
                                                    <img src="{{asset($user->image())}}" style="max-width: 60px; max-height: 50px;" />
                                                </span>
                                            </td>
                                            <td>
                                                <a href="{{route('admin.usersAdminAction',['edit',$user->id])}}" class="invoice-action-view mr-1">{{$user->name}} </a>
                                            </td>
                                            <td>{{$user->email}}</td>
                                            <td> 
                                                @if($user->permission)
                                                <span class="badge badge-info">{{$user->permission->name}}</span>
                                                @else
                                                <span class="badge badge-danger">Un-athorize</span>
                                                @endif
                                            </td>

                                            <td>
                                                @if($user->status)
                                                <span class="badge badge-success">Active </span>
                                                @else
                                                <span class="badge badge-danger">Inactive </span>
                                                @endif
                                            </td>
                                            <td style="padding: 5px 0; text-align: center;">
                                                <a href="{{route('admin.usersAdminAction',['edit',$user->id])}}" class="invoice-action-view mr-1">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                @isset(json_decode(Auth::user()->permission->permission, true)['adminUsers']['delete']) @if($user->id==Auth::id()) @else
                                                <a href="{{route('admin.usersAdminAction',['delete',$user->id])}}" onclick="return confirm('Are You Want To Delete')" class="invoice-action-edit cursor-pointer danger">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                                @endif @endisset
                                                <br />
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            {{$users->links('pagination')}}
                        </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div>

@isset(json_decode(Auth::user()->permission->permission, true)['adminUsers']['add'])
<!-- Modal -->
<div class="modal fade text-left" id="AddUser" tabindex="-1" >
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="{{route('admin.usersAdminAction','create')}}" method="post">
                @csrf
                <div class="modal-header">
                    <h4 class="modal-title" id="myModalLabel1">Add Admin User</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times; </span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <div class="controls">
                            <input type="text" class="form-control {{$errors->has('username')?'error':''}}" name="username" placeholder="Enter Email/Mobile" value="" required="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endisset @endsection @push('js') @endpush
