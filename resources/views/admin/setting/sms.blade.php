@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle(ucfirst($type).' Setting')}}</title>
@endsection @push('css')
<style type="text/css"></style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">{{ucfirst($type)}} Setting</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">{{ucfirst($type)}} Setting</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-outline-primary reloadPage" href="javascript:void(0)">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    @include(adminTheme().'alerts')
    <form action="{{route('admin.settingUpdate','sms')}}" method="post" enctype="multipart/form-data">
        @csrf
        <!-- Basic Elements start -->
        <section class="basic-elements">
            <div class="row">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">SMS Setting</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="sms_type">SMS Type</label>
                                            <select name="sms_type" class="form-control {{$errors->has('sms_type')?'error':''}}">
                                                <option value="smtp" {{$general->sms_type=='Non Masking'?'selected':''}}>Non-Masking</option>
                                                <option value="mailgun" {{$general->sms_type=='Masking'?'selected':''}}>Masking</option>
                                            </select>
                                            @if ($errors->has('sms_type'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_type') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="sms_senderid">SMS Sender ID</label>
                                            <input type="text" name="sms_senderid" value="{{ $general->sms_senderid }}" placeholder="SMS Sender ID" class="form-control {{$errors->has('sms_senderid')?'error':''}}" />
                                            @if ($errors->has('sms_senderid'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_senderid') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="sms_url_nonmasking">SMS Url Non-Masking</label>
                                            <input type="text" name="sms_url_nonmasking" value="{{ $general->sms_url_nonmasking }}" placeholder="SMS Url Non-Masking" class="form-control {{$errors->has('sms_url_nonmasking')?'error':''}}" />
                                            @if ($errors->has('sms_url_nonmasking'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_url_nonmasking') }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="sms_url_masking">SMS Url Masking</label>
                                            <input type="text" name="sms_url_masking" value="{{ $general->sms_url_masking }}" placeholder="SMS Url Masking" class="form-control {{$errors->has('sms_url_masking')?'error':''}}" />
                                            @if ($errors->has('sms_url_masking'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_url_masking') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="sms_username">SMS Username</label>
                                            <input type="text" name="sms_username" value="{{ $general->sms_username }}" placeholder="SMS Username  " class="form-control {{$errors->has('sms_username')?'error':''}}" />
                                            @if ($errors->has('sms_username'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_username') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="sms_password">SMS Password</label>
                                            <div class="input-group">
                                                <input type="password" name="sms_password" value="{{$general->sms_password}}" placeholder="SMS Password" class="form-control password {{$errors->has('sms_password')?'error':''}}" />
                                                <div class="input-group-append">
                                                    <span class="input-group-text showPassword"><i class="fa fa-eye-slash"></i></span>
                                                </div>
                                            </div>
                                            @if ($errors->has('sms_password'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('sms_password') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                        <div class="form-group">
                                            <label for="admin_numbers">Author Mobile <small>(use comma, for multiple number)</small></label>
                                            <input
                                                type="text"
                                                name="admin_numbers"
                                                value="{{ $general->admin_numbers }}"
                                                placeholder="Author Mobile  (use comma, for multiple)"
                                                class="form-control {{$errors->has('admin_numbers')?'error':''}}"
                                            />
                                            @if ($errors->has('admin_numbers'))
                                            <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('admin_numbers') }}</p>
                                            @endif
                                        </div>
                                    </div>

                                    <div class="col-xl-6 col-lg-6 col-md-12">
                                        <div class="form-group">
                                            <label for="sms_status">SMS Status</label>
                                            <div class="custom-control custom-checkbox">
                                                <input type="checkbox" class="custom-control-input" name="sms_status" id="sms_status" {{$general->sms_status?'checked':''}}/>
                                                <label class="custom-control-label" for="sms_status" style="cursor: pointer;">Active <small>(SMS System Active)</small></label>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xl-12 col-lg-12 col-md-12">
                                        <table class="table table-bordered table-striped">
                                        <tbody>
                                                <tr>
                                                    <th>Action For</th>
                                                    <th>Status <small>(Send SMS status)</small></th>
                                                </tr>
                                                <tr>
                                                    <th>Registration</th>
                                                    <td>
                                                        <label style="cursor: pointer;"> <input type="checkbox" name="register_sms_user" {{$general->register_sms_user?'checked':''}} /> User </label>
                                                        <label style="cursor: pointer;"> <input type="checkbox" name="register_sms_author" {{$general->register_sms_author?'checked':''}} /> Author </label>
                                                        <b> Account Create After Send SMS </b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Registration Verify</th>
                                                    <td>
                                                        <label style="cursor: pointer;"> <input type="checkbox" name="register_verify_sms_user" {{$general->register_verify_sms_user?'checked':''}} /> User </label>
                                                        <b> Registration Verify Request Send SMS </b>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th>Forget Password</th>
                                                    <td>
                                                        <label style="cursor: pointer;"> <input type="checkbox" name="forget_password_sms_user" {{$general->forget_password_sms_user?'checked':''}} /> User </label>
                                                        <b> Forget Password Request Send SMS </b>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                    <div class="col-xl-12 col-lg-12 col-md-12 mb-1">
                                        <button type="submit" class="btn btn-primary btn-md rounded-0 mr-sm-1 mb-1 mb-sm-0">Save changes</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!-- Basic Inputs end -->
    </form>
</div>

@endsection @push('js') @endpush
