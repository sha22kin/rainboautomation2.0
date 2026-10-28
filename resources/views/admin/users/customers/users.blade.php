@extends(adminTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Customer Users')}}</title>
@endsection 
@push('css')
<style type="text/css"></style>
@endpush 
@section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Customer Users</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Customer Users</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            @isset(json_decode(Auth::user()->permission->permission, true)['users']['add'])
            <button type="button" class="btn btn-outline-primary" data-toggle="modal" data-target="#AddUser">Add User</button>
            @endisset
            <a class="btn btn-outline-primary" href="{{route('admin.usersCustomer')}}">
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
                                        <form action="{{route('admin.usersCustomer')}}">
                                            <div class="row">
                                                <div class="col-md-6 mb-1">
                                                    <div class="input-group">
                                                        <input type="date" name="startDate" value="{{request()->startDate?:''}}" class="form-control {{$errors->has('startDate')?'error':''}}" />
                                                        <input type="date" value="{{request()->endDate?:''}}" name="endDate" class="form-control {{$errors->has('endDate')?'error':''}}" />
                                                    </div>
                                                </div>
                                                <div class="col-md-6 mb-1">
                                                    <div class="input-group">
                                                        <input type="text" name="search" value="{{request()->search?:''}}" placeholder="User Name, Email, Mobile" class="form-control {{$errors->has('search')?'error':''}}" />
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
                        <h4 class="card-title">Customer users List</h4>
                    </div>
                    <div class="card-content">
                        <div class="card-body">
                        <form action="{{route('admin.usersCustomer')}}">
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="input-group mb-1">
                                        <select class="form-control form-control-sm rounded-0" name="action" required="">
                                            <option value="">Select Action</option>
                                            <option value="1">Active</option>
                                            <option value="2">Inactive</option>
                                            <option value="5">Delete</option>
                                        </select>
                                        <button class="btn btn-sm btn-primary rounded-0" onclick="return confirm('Are You Want To Action?')">Action</button>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    
                                </div>
                                <div class="col-md-4">
                                    <ul class="statuslist">
                                        <li><a href="{{route('admin.usersCustomer')}}">All ({{$totals->total}})</a></li>
                                        <li><a href="{{route('admin.usersCustomer',['status'=>'active'])}}">Active ({{$totals->active}})</a></li>
                                        <li><a href="{{route('admin.usersCustomer',['status'=>'inactive'])}}">Inactive ({{$totals->inactive}})</a></li>
                                    </ul>
                                </div>
                            </div>

                            <div class="table-responsive">
                                <table class="table table-bordered table-striped">
                                    <thead>
                                        <tr>
                                            <th style="min-width: 60px; width: 60px;">
                                                <label style="cursor: pointer; margin-bottom: 0;"> <input class="checkbox" type="checkbox" class="form-control" id="checkall" /> All <span class="checkCounter"></span> </label>
                                            </th>
                                            <th style="min-width: 100px; width: 100px;">Image</th>
                                            <th style="min-width: 200px; width: 200px;">Name</th>
                                            <th style="min-width: 150px;">Email</th>
                                            <th style="min-width: 80px;">Status</th>
                                            <th style="min-width: 100px;">Join Date</th>
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
                                            <td><a href="{{route('admin.usersCustomerAction',['edit',$user->id])}}" class="invoice-action-view mr-1">{{$user->name}}</a></td>
                                            <td>{{$user->email}}</td>
                                            <td>
                                                @if($user->status)
                                                <span class="badge badge-success">Active </span>
                                                @else
                                                <span class="badge badge-danger">Inactive </span>
                                                @endif
                                            </td>
                                            <td>{{$user->created_at->format('d M Y h:i A')}}</td>
                                            <td style="padding: 8px 5px; text-align: center;">
                                                <a href="{{route('admin.usersCustomerAction',['edit',$user->id])}}" class="invoice-action-view mr-1">
                                                    <i class="fa fa-eye"></i>
                                                </a>

                                                @isset(json_decode(Auth::user()->permission->permission, true)['users']['delete']) @if($user->id==Auth::id()) @else
                                                <a href="{{route('admin.usersCustomerAction',['delete',$user->id])}}" onclick="return confirm('Are You Want To Delete')" class="invoice-action-edit cursor-pointer danger">
                                                    <i class="fa fa-trash"></i>
                                                </a>
                                                @endif @endisset
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

@isset(json_decode(Auth::user()->permission->permission, true)['users']['add'])
 <!-- Modal -->
 <div class="modal fade text-left" id="AddUser" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
	 <div class="modal-content">
	 	<form action="{{route('admin.usersCustomerAction','create')}}" method="post">
	   		@csrf
	   <div class="modal-header">
		 <h4 class="modal-title">Add User</h4>
		 <button type="button" class="close" data-dismiss="modal" aria-label="Close">
		   <span aria-hidden="true">&times; </span>
		 </button>
	   </div>
	   <div class="modal-body">
	   		<div class="form-group">
			 <label for="name">Name* </label>
             <div class="controls">
                 <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" required="">
				@if ($errors->has('name'))
				<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
				@endif
				</div>
         	</div>
			 <div class="form-group">
				<label for="name">Email* </label>
				<div class="controls">
					<input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email" required="">
					@if ($errors->has('email'))
					<p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
					@endif
				</div>
         	</div>
	   </div>
	   <div class="modal-footer">
		 <button type="button" class="btn grey btn-outline-secondary" data-dismiss="modal">Close </button>
		 <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Add User</button>
	   </div>
	   </form>
	 </div>
   </div>
 </div>
@endisset



@endsection 
@push('js') 
@endpush
