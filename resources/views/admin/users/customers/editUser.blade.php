@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('User Profile')}}</title>
@endsection

@push('css')
<style type="text/css">
    .showPassword {
    right: 0 !important;
    cursor: pointer;
    }
    .ProfileImage{
        max-width: 64px;
        max-height: 64px;
    }
</style>
@endpush
@section('contents')


<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
     <h3 class="content-header-title mb-0">User Profile</h3>
     <div class="row breadcrumbs-top">
       <div class="breadcrumb-wrapper col-12">
         <ol class="breadcrumb">
           <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a>
           </li>
           <li class="breadcrumb-item active">User Profile</li>
         </ol>
       </div>
     </div>
    </div>
   <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
     <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
       
       <a class="btn btn-outline-primary" href="{{route('admin.usersCustomer')}}">
       		Back
       	</a>
       	<a class="btn btn-outline-primary reloadPage" href="javascript:void(0)">
       		<i class="fa-solid fa-rotate"></i>
       	</a>
     </div>
   </div>
</div>
 
	

 <div class="content-body">
    <!-- Basic Elements start -->
    @include(adminTheme().'alerts')
    <div class="row">
        <div class="col-md-7">
            <div class="card">
                <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                    <h4 class="card-title">My Profile</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{route('admin.usersCustomerAction',['update',$user->id])}}" method="post" enctype="multipart/form-data">
                            @csrf
                            <div class="media">
                                <a href="javascript: void(0);">
                                    <img src="{{asset($user->image())}}" class="ProfileImage rounded mr-75" alt="profile image" />
                                </a>
                                <div class="media-body mt-75">
                                    <div class="col-12 px-0 d-flex flex-sm-row flex-column justify-content-start">
                                        <label class="btn btn-sm btn-primary ml-50 mb-50 mb-sm-0 cursor-pointer" for="account-upload">Upload new photo </label>
                                        <input type="file" name="image" id="account-upload" hidden="" />
                                        @isset(json_decode(Auth::user()->permission->permission, true)['users']['update']) @if($user->imageFile)
                                        <a href="{{route('admin.mediesDelete',$user->imageFile->id)}}" class="mediaDelete btn btn-sm btn-secondary ml-50">Reset </a>
                                        @endif @endisset
                                    </div>
                                    @if ($errors->has('image'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('image') }}</p>
                                    @endif
                                    <p class="text-muted ml-75 mt-50"><small>Allowed JPG, GIF or PNG. Max size of 2048kB</small></p>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label for="name">Name* </label>
                                            <input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Name" value="{{$user->name?:old('name')}}" required="" />
                                            @if ($errors->has('name'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('name') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label for="email">Email* </label>
                                            <input type="email" class="form-control {{$errors->has('email')?'error':''}}" name="email" placeholder="Enter Email" value="{{$user->email?:old('email')}}" required="" />
                                            @if ($errors->has('email'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('email') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label for="mobile">Mobile* </label>
                                            <input type="text" class="form-control {{$errors->has('mobile')?'error':''}}" name="mobile" placeholder="Enter Mobile" value="{{$user->mobile?:old('mobile')}}" />
                                            @if ($errors->has('mobile'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('mobile') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label for="gender">Gender </label>
                                        <select class="form-control {{$errors->has('gender')?'error':''}}" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="Male" {{$user->gender=='Male'?'selected':''}}>Male</option>
                                            <option value="Female" {{$user->gender=='Female'?'selected':''}}>Female</option>
                                        </select>
                                        @if ($errors->has('gender'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('gender') }}</p>
                                        @endif
                                    </div>
                                </div>
                                
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label for="division">Division </label>
                                        <select id="division" class="form-control {{$errors->has('division')?'error':''}}" name="division">
                                            <option value="">Select Division</option>

                                            @foreach(App\Models\Country::where('type',2)->where('parent_id',1)->get() as $data)
                                            <option value="{{$data->id}}" {{$data->id==$user->division?'selected':''}}>{{$data->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label for="district">District </label>
                                        <select id="district" class="form-control {{$errors->has('district')?'error':''}}" name="district">
                                            @if($user->division==null)
                                            <option value="">No District</option>
                                            @else
                                            <option value="">Select District</option>
                                            @foreach(App\Models\Country::where('type',3)->where('parent_id',$user->division)->get() as $data)
                                            <option value="{{$data->id}}" {{$data->id==$user->district?'selected':''}}>{{$data->name}}</option>
                                            @endforeach @endif
                                        </select>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label for="city">City </label>
                                        <select id="city" class="form-control {{$errors->has('city')?'error':''}}" name="city">
                                            @if($user->district==null)
                                            <option value="">No City</option>
                                            @else
                                            <option value="">Select City</option>
                                            @foreach(App\Models\Country::where('type',4)->where('parent_id',$user->district)->get() as $data)
                                            <option value="{{$data->id}}" {{$data->id==$user->city?'selected':''}}>{{$data->name}}</option>
                                            @endforeach @endif
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label for="postal_code">Postal Code</label>
                                            <input type="text" class="form-control {{$errors->has('postal_code')?'error':''}}" name="postal_code" placeholder="Enter Postal Code" value="{{$user->postal_code?:old('postal_code')}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <div class="controls">
                                            <label for="address">Address Line</label>
                                            <input type="text" class="form-control {{$errors->has('address')?'error':''}}" name="address" placeholder="Enter Address" value="{{$user->address_line1?:old('address')}}" />
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label for="status">User Status</label>
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" name="status" id="status" {{$user->status?'checked':''}}/>
                                            <label class="custom-control-label" for="status">User Active</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-xl-6 col-lg-6 col-md-12">
                                    <div class="form-group">
                                        <label for="created_at">Join Date</label>
                                        <input type="date" name="created_at" value="{{$user->created_at?$user->created_at->format('Y-m-d'):old('created_at')}}" class="form-control {{$errors->has('created_at')?'error':''}}">
                                    </div>
                                </div>
                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                    <button type="submit" class="btn btn-primary btn-md rounded-0 mr-sm-1 mb-1 mb-sm-0">Save changes</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card">
                <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                    <h4 class="card-title">Change Password</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <form action="{{route('admin.usersCustomerAction',['change-password',$user->id])}}" method="post">
                            @csrf
                            <div class="row">
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label for="old_password">Old password </label>
                                        <div class="input-group">
                                            <input type="password" class="form-control password" placeholder="Old Password" name="old_password" value="{{$user->password_show?:old('old_password')}}" required="" />
                                            <div class="input-group-append">
                                                <span class="input-group-text showPassword"><i class="fa fa-eye-slash"></i></span>
                                            </div>
                                        </div>
                                        @if ($errors->has('old_password'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('old_password') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label for="password">New Password </label>
                                        <input type="password" class="form-control password {{$errors->has('password')?'error':''}}" name="password" placeholder="New password" required="" />
                                        @if ($errors->has('password'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('password') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-xl-12 col-lg-12 col-md-12">
                                    <div class="form-group">
                                        <label for="password_confirmation">Confirmed Password </label>
                                        <input type="password" class="form-control password {{$errors->has('password_confirmation')?'error':''}}" name="password_confirmation" placeholder="Confirmed password" required="" />
                                        @if ($errors->has('password_confirmation'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('password_confirmation') }}</p>
                                        @endif
                                    </div>
                                </div>

                                <div class="col-12 d-flex flex-sm-row flex-column justify-content-end">
                                    <button type="submit" class="btn btn-danger btn-md rounded-0 mr-sm-1 mb-1 mb-sm-0">Change Password</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>



@endsection
@push('js')



@endpush