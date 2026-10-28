@extends(welcomeTheme().'layouts.app') 
@section('title')
<title>{{websiteTitle('Register')}}</title>
@endsection 
@section('SEO')
<meta name="description" content="{!!general()->meta_description!!}" />
<meta name="keywords" content="{{general()->meta_keyword}}" />
<meta property="og:title" content="{{websiteTitle('Register')}}" />
<meta property="og:description" content="{!!general()->meta_description!!}" />
<meta property="og:image" content="{!!general()->meta_description!!}" />
<meta property="og:url" content="{{route('register')}}" />
<link rel="canonical" href="{{route('register')}}">
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
<div class="lostregis">
	<div class="lostpassheader">
		<h3>My Account</h3>
		<p>Sign-Up</p>
	</div>
	<div class="container">
	    <div class="row">
	        <div class="col-md-3"></div>
	        <div class="col-md-6">
	            <div class="login-part">
            		<h4>REGISTER VERIFY</h4>
            		@include(welcomeTheme().'alerts')
            		<form action="{{route('registerVerify')}}" method="post">
            		    @csrf
						@if(general()->mail_status && general()->sms_status && general()->register_verify_mail_user && general()->register_verify_sms_user)
            			<label for="userid">Verify Email/Mobile*</label>
            			<div class="form-group form-group-section">
            			    <input type="text" name="userid" value="{{old('userid')}}" class="form-control control-section" placeholder="Verify Email/Mobile" required="">
            			    @if($errors->has('userid'))
                                <span style="color:red;display: block;">Your Email/Mobile Are Invalid</span>
                            @endif
            			</div>
						<div>
            				<button type="submit" class="btn btn-success submitbutton">NEXT</button>
            			</div>
						@elseif(general()->sms_status && general()->register_verify_sms_user)
						<label for="mobile">Verify Mobile*</label>
            			<div class="form-group form-group-section">
            			    <input type="number" name="mobile" value="{{old('mobile')}}" class="form-control control-section" placeholder="Verify Mobile Number" required="">
            			    @if($errors->has('mobile'))
                                <span style="color:red;display: block;">{{ $errors->first('mobile') }}</span>
                            @endif
            			</div>
						<div>
            				<button type="submit" class="btn btn-success submitbutton">NEXT</button>
            			</div>
						@elseif(general()->mail_status && general()->register_verify_mail_user)
						<label for="email">Verify Email*</label>
            			<div class="form-group form-group-section">
            			    <input type="email" name="email" value="{{old('email')}}" class="form-control control-section" placeholder="Verify Email Address" required="">
            			    @if($errors->has('email'))
                                <span style="color:red;display: block;">{{ $errors->first('email') }}</span>
                            @endif
            			</div>
						<div>
            				<button type="submit" class="btn btn-success submitbutton">NEXT</button>
            			</div>
						@else
						<br>
						<h5 style="color: #ff5722;">Register Verification Are Not Allow.</h5>
						<br>
						@endif
            
            			
            		</form>
            		
                    <div class="row">
                        <div class="col-md-12">
                            <a href="{{route('login')}}">Alrady Have An Account? <span>Log-In</span></a>
                        </div>
                    </div>
            		
        		</div>
	        </div>
	        <div class="col-md-3"></div>
	    </div>

	</div>
</div>
<br>
@endsection 
@push('js') 
@endpush
