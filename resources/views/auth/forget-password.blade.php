@extends(welcomeTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Forget Password')}}</title>
@endsection 
@section('SEO')
<meta name="description" content="{!!general()->meta_description!!}" />
<meta name="keywords" content="{{general()->meta_keyword}}" />
<meta property="og:title" content="{{websiteTitle('Forget Password')}}" />
<meta property="og:description" content="{!!general()->meta_description!!}" />
<meta property="og:image" content="{!!general()->meta_description!!}" />
<meta property="og:url" content="{{route('forgotPassword')}}" />
<link rel="canonical" href="{{route('forgotPassword')}}">
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
		<p>Forget Password</p>
	</div>
	<div class="container">
	    <div class="row">
	        <div class="col-md-3"></div>
	        <div class="col-md-6">
	            <div class="login-part">
            		<p>
            			Lost your password? Please enter your username or email address. You will receive a link to create a new password via email.
            		</p>
            
            		<form  method="POST" action="{{route('forgotPassword')}}">
                        @csrf
                        <h4>FORGET PASSWORD</h4>
            			@include(welcomeTheme().'alerts')
            			@if(general()->mail_status && general()->sms_status && general()->forget_password_mail_user && general()->forget_password_sms_user)
            			<label for="userid">Email/Mobile*</label>
            			<div class="form-group form-group-section">
            				<input type="text" name="userid" value="{{old('userid')}}" class="form-control control-section" placeholder="Enter Your Email/Mobile" required="">
            				@if($errors->has('userid'))
            					<span style="color:red;display: block;">You Mobile/Email Address Are Invalid</span>
            				@endif
            			</div>
            			<div>
            				<button type="submit" class="btn btn-success submitbutton">RESET PASSWORD</button>
            			</div>
            			<br>
            			@elseif(general()->sms_status && general()->forget_password_sms_user)
            			<label for="mobile">Mobile*</label>
            			<div class="form-group form-group-section">
            				<input type="text" name="mobile" value="{{old('mobile')}}" class="form-control control-section" placeholder="Enter Your Mobile" required="">
            				@if($errors->has('mobile'))
            					<span style="color:red;display: block;">{{ $errors->first('mobile') }}</span>
            				@endif
            			</div>
            			<div>
            				<button type="submit" class="btn btn-success submitbutton">RESET PASSWORD</button>
            			</div>
            			<br>
            			@elseif(general()->mail_status && general()->forget_password_mail_user)
            			<label for="email">Email*</label>
            			<div class="form-group form-group-section">
            				<input type="email" name="email" value="{{old('email')}}" class="form-control control-section" placeholder="Enter Your Email" required="">
            				@if($errors->has('email'))
            					<span style="color:red;display: block;">{{ $errors->first('email') }}</span>
            				@endif
            			</div>
            
            			<div>
            				<button type="submit" class="btn btn-success submitbutton">RESET PASSWORD</button>
            			</div>
            			<br>
            			@else
            			<br>
            			<h5 style="color: #ff5722;">Reset Password Are Not Allow.</h5>
            			<br>
            			@endif
            			<p>You have Account. <a href="{{route('login')}}">Login</a></p>
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