@extends(welcomeTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Confirm Password')}}</title>
@endsection 
@section('SEO')
<meta name="description" content="{!!general()->meta_description!!}" />
<meta name="keywords" content="{{general()->meta_keyword}}" />
<meta property="og:title" content="{{websiteTitle('Confirm Password')}}" />
<meta property="og:description" content="{!!general()->meta_description!!}" />
<meta property="og:image" content="{!!general()->meta_description!!}" />
<meta property="og:url" content="{{route('resetPassword',$token)}}" />
<link rel="canonical" href="{{route('resetPassword',$token)}}">
@endsection 
@push('css')
<style>
    .lostpassheader{
        text-align:center;   
    }
    .login-part {
        border: 1px solid #d5d5d5;
        padding: 25px;
        border-radius: 10px;
    }
</style>
@endpush 

@section('contents')
<br>
<div class="lostpass">
	<div class="lostpassheader">
		<h3>My Account</h3>
		<p>Confirmed Password</p>
	</div>
	<div class="container">
	    
	    <div class="row">
	        <div class="col-md-3"></div>
	        <div class="col-md-6">
	            <div class="login-part">
	    
	    
            		<p>
            			Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.
            		</p>
            
            		<form class="form-horizontal form-simple" action="{{route('resetPasswordCheck')}}" method="post">
                        @csrf
                        <input type="hidden" name="token" value="{{ $token }}">
                        <div>
                            @if($errors->has('name'))
                                <span style="color:red;display: block;">{{ $errors->first('name') }}</span>
                            @endif
                            @if($errors->has('password'))
                                <span style="color:red;display: block;">{{ $errors->first('password') }}</span>
                            @endif
                            @include(welcomeTheme().'alerts')
                        </div>
                        
            			<label for="verifycode"> Verify Code*</label>
            			<div class="form-group form-group-section">
            			    <input type="number" class="form-control form-control-lg" value="{{$r->code?:old('verifycode')}}" name="verifycode" placeholder="Enter Verifycode" autocomplete="off" required="" />
            			    @if($errors->has('verifycode'))
                                <span style="color:red;display: block;">{{ $errors->first('verifycode') }}</span>
                            @endif
            			</div>
            			<label for="password"> Password *</label>
            			<div class="form-group form-group-section">
            				<div class="input-group">
            					<input type="password" class="form-control control-section password" id="password" name="password" placeholder="Enter Password" required="" />
            					<div class="input-group-append">
            					<span class="input-group-text showPassword" style="cursor: pointer;"><i class="fa fa-eye-slash"></i></span>
            					</div>
            				</div>
            				@if($errors->has('password'))
                                <span style="color:red;display: block;">{{ $errors->first('password') }}</span>
                            @endif
            			</div>
            			<div>
            				<button type="submit" class="btn btn-success submitbutton">RESET PASSWORD</button>
            			</div>
            		</form>
            	</div>
        	</div>
    	</div>
	</div>
</div>

<br>

@endsection 
@push('js') 
@endpush