<?php

namespace App\Http\Controllers\Auth;


use Auth;
use Str;
use Hash;
use Mail;
use File;
use url;
use Session;
use Cookie;
use Socialite;
use Redirect,Response;
use App\Models\User;
use App\Models\Seller;
use App\Models\General;
use App\Models\SocialIdentity;
use App\Http\Controllers\Controller;
use App\Mail\RegistrationMail;
use App\Mail\passwordResetVerify;
use App\Mail\VerifyCodeMail;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    
    public function __construct()
    {
    	$this->middleware('authCheck');
    }

    public function login(Request $r){

        //session(['url.intended' => url()->current()]); 

        // if(route('login')!=url()->previous()){
        //     session(['url.intended' => url()->previous()]); 
        // }

       // return Session::get('url.intended');

        //return $redirect;

        if ($r->isMethod('post'))
        {
            
            //Login Post Action

            $check = $r->validate([
            'username' => 'required|max:100',
            'password' => 'required|max:50'
            ]);

            if(!$check){
                Session::flash('error','Need To validation');
                return back();
            }

            $login = $r->username;

            $remember_me  = ( !empty( $r->remember ) )? TRUE : FALSE;
            
            if(is_numeric($login)){
                $field = 'mobile';
            } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $field = 'email';
            } else {
                $field = 'name';
            }

            $user =User::where($field,$login)->first();

            if($user){
                if(Hash::check($r->password, $user->password)){
                    Auth::login($user, $remember_me);

                    $redirect =Session::get('url.intended');
                    //Session::forget('url.intended');
                    if($redirect){
                        return Redirect::to($redirect);
                    }

                    return Redirect()->route('admin.dashboard');
                    
                }else{
                    Session::flash('loginfailP','Your Acounts Password Are Incorrect');
                    return back();
                }
            }else{
                Session::flash('loginfail','Your No Accounts Have With Us');
                return back();
            }

            //Login Post Action End

        }
        // if(auth::check()){
        //     Redirect()->route('admin.dashboard');
        // }
        return view('auth.login'); 
    	
    }


    public function register(Request $r){

        if ($r->isMethod('post'))
        {
        
        $check = $r->validate([
            'name' => 'required|max:100',
            'email' => 'required|max:100|unique:users,email',
            'mobile' => 'required|max:100|unique:users,mobile',
            'password' => 'required|min:5'
        ]);

        if(!$check){
            return back();
        }
        Session::flash('success','Registration Are Not Allow');
        return back();
        $general = General::first();
        
        //User Create
        $user =new User();
        $user->name=$r->name;
        $user->mobile=$r->mobile;
        $user->email=$r->email;
        $user->password=Hash::make($r->password);
        $user->password_show=$r->password;
        $user->country=1;
        $user->save();

        Auth::login($user);
        
        //Mail Send/SMS Send
        
        //**********Send Mail***************//

        if(general()->mail_status && $user->email){
            //Mail Data
            $datas =array('user'=>$user);
            $template ='mails.registrationMail';
            $toEmail =$user->email;
            $toName =$user->name;
            $subject ='Registration Successfully Completed in '.general()->title;
        
            sendMail($toEmail,$toName,$subject,$datas,$template);
        }
        //**********Send Mail***************//
        
        
         //**********Send SMS ***************//
            if($general->sms_status){
        
                //Send SMS User
                if($general->cus_reg_sms_customer && $user->mobile){
                    
                    $m =$user->mobile;
                    
                    $to =bdMobile($m);
                    
                    if(strlen($to) != 13)
                    {
                        return true;
                    }
                    $msg = urlencode("Congratulations!! {$user->name}.You are Successfully Registration in {$general->title}"); //150 characters allowed here
        
                    $url = smsUrl($to,$msg);
                
                    $client = new Client();
                    
                    try {
                            $r = $client->request('GET', $url);
                        } catch (\GuzzleHttp\Exception\ConnectException $e) {
                        } catch (\GuzzleHttp\Exception\ClientException $e) {
                        }
                    
                    
                }
                
                //Send SMS Admin
                if($general->cus_reg_sms_admin && $general->admin_numbers){
                    $m =$general->admin_numbers;
                    $to =bdMobile($m);
                    if(strlen($to) != 13)
                    {
                        return true;
                    }
                    $msg = urlencode("New Registration in {$general->title}. Name: {$user->name}, Mobile: {$user->mobile}."); //150 characters allowed here
        
                    $url = smsUrl($to,$msg);
                
                    $client = new Client();
                    
                    try {
                            $r = $client->request('GET', $url);
                        } catch (\GuzzleHttp\Exception\ConnectException $e) {
                        } catch (\GuzzleHttp\Exception\ClientException $e) {
                        }
                }
                
                
                
            }
            
        //**********Send SMS ***************//
        
        
        

        if(Auth::check()){
            return Redirect()->route('customer.dashboard');
        }else{
            Session::flash('success','Your Registration Successfully Done!');
            return Redirect()->route('login');
        }

    }else{
        //Session::put('verifycode','123456');
        return view('auth.register',compact('r'));
    } 


    }


    public function forgotPassword(Request $r){
        
        if ($r->isMethod('post'))
        {

        $check = $r->validate([
            '_token' => 'required',
            'emailormobile' => 'required|max:100',
        ]);

        if(!$check){
            Session::flash('error','Need To validation');
            return back();
        }
        Session::flash('success','Recover Password Are Not Allow');
        return back();
        $general = General::first();
        
        if(is_numeric($r->emailormobile)){
        
        $user =User::where('mobile',$r->emailormobile)->first();

        if(!$user){
            Session::flash('error','There is no account with the Mobile number you provided.');
            return back(); 
        }
        $verifycode = mt_rand(100000,999999);
        $token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);
        $user->remember_token=$token;
        $user->verify_code=$verifycode;
        $user->save();
        
       //**********Send SMS ***************//
        
        if($general->sms_status && $user->mobile){

            //Send SMS User
                
                $m =$user->mobile;
                
                $to =bdMobile($m);
                
                if(strlen($to) != 13)
                {
                    return true;
                }
                
                $msg = urlencode("You Forget Password OTP code is {$verifycode} in {$general->title}"); //150 characters allowed here
    
                $url = smsUrl($to,$msg);
            
                $client = new Client();
                
                try {
                        $r = $client->request('GET', $url);
                    } catch (\GuzzleHttp\Exception\ConnectException $e) {
                    } catch (\GuzzleHttp\Exception\ClientException $e) {
                    }
        
        }
        
        //**********Send SMS ***************//
        
        Session::flash('success','We send SMS Reset 6 digit Code Your Mobile Number!');
        return Redirect()->route('resetPassword',$token);

        }else if(filter_var($r->emailormobile, FILTER_VALIDATE_EMAIL)) {

            $user =User::where('email',$r->emailormobile)->first();

            if(!$user){
                Session::flash('error','There is no account with the Email address you provided.');
                return back(); 
            }

            $token = app(\Illuminate\Auth\Passwords\PasswordBroker::class)->createToken($user);
            $user->remember_token=$token;
            $user->save();
            
            
            //**********Send Mail***************//

             if(general()->mail_status && $user->email){
                //Mail Data
                $datas =array('user'=>$user);
                $template ='mails.passwordResetVerify';
                $toEmail =$user->email;
                $toName =$user->name;
                $subject ='Reset Password Code Form '.general()->title;
            
                sendMail($toEmail,$toName,$subject,$datas,$template);
            }
            //**********Send Mail***************//

            Session::flash('success','We send Reset Link with 6 digit Code Your Email address!');
            return Redirect()->route('resetPassword',$token);

        }else{
          Session::flash('error','Please Provide your email address/mobile number.');
          return back();  
        }

    }else{
        return view('auth.forget-password');
    }


        
    }

    public function resetPassword(Request $r,$token){

        $user =User::where('remember_token',$token)->first();
        if($user){
            return view('auth.confirm-password',compact('token'));
        }else{
            Session::flash('faillink','Your reset link are exprired');
            return Redirect()->route('forgotPassword');
        }

    }

    public function resetPasswordCheck(Request $r){

         $check = $r->validate([
            'token' => 'required',
            'verifycode' => 'required|numeric|digits:6',
            'password' => 'required|min:6',
        ]);


        if(!$check){
            Session::flash('error','Need To validation');
            return back();
        }

        $user =$user =User::where('remember_token',$r->token)->first();

        
        if($user){
           
            if($user->verify_code ==$r->verifycode){
                
                $user->remember_token=null;
                $user->verify_code=null;
                $user->password=Hash::make($r->password);
                $user->password_show=$r->password;
                $user->save();

                Auth::loginUsingId($user->id);
                if(Auth::check()){
                    return Redirect()->route('customer.dashboard');
                }else{
                    Session::flash('success','Your Reset Password Successfully Done!');
                    return Redirect()->route('login');
                }

            }else{
                Session::flash('error','Your Verify Code Are Incorrect!!');
                return Redirect()->back();
            }

        }else{
        Session::flash('error','Your reset link are exprired');
        return Redirect()->route('forgotPassword');
        }

        return $r;
    }

    public function logout(){
    	Auth::logout();
        session()->flush();
    	return Redirect()->route('index');
    }


    
         //Verify Code SEnd Function

      public function sendVerifyCode(Request $r,$data){

        if($r->ajax())
        {
          // 
            $data =$data;

            if(is_numeric($data)){
                $user =User::where('mobile',$data)->first();
                Session::put('mobile', $data);

            } elseif (filter_var($data, FILTER_VALIDATE_EMAIL)) {
                $user =User::where('email',$data)->first();
                Session::put('email', $data);
            }

            if($user){
              $status=false;
              return Response()->json([
                      'success' => $status,
                    ]);
            }else{

            $status=true;

            $verifycode = mt_rand(100000,999999);
             Session::put('verifycode', $verifycode);
            $verifycode=Session::get('verifycode');
            $general = General::first();
            if(is_numeric($data) && $general->sms_status){
                //Send SMS User
                        $m =$data;
                        
                        $to =bdMobile($m);
                        
                        if(strlen($to) != 13)
                        {
                            //return true;
                        }else{
                        $msg = urlencode("Your Verify OPT Code Is {$verifycode} form  {$general->title}"); //150 characters allowed here
            
                        $url = smsUrl($to,$msg);
                    
                        $client = new Client();
                        
                        try {
                                $r = $client->request('GET', $url);
                            } catch (\GuzzleHttp\Exception\ConnectException $e) {
                            } catch (\GuzzleHttp\Exception\ClientException $e) {
                            }
                        }
                }elseif (filter_var($data, FILTER_VALIDATE_EMAIL) && $general->mail_status) {

                    //********** Send Mail ***************//
                       if(general()->mail_status && $data){
                            //Mail Data
                            $datas =array('verifycode'=>$verifycode);
                            $template ='mails.VerifyCodeMail';
                            $toEmail =$data;
                            $toName =null;
                            $subject ='Your Verify OPT Code Form '.general()->title;
                        
                            sendMail($toEmail,$toName,$subject,$datas,$template);
                        }
                        
                    //********** Send Mail ***************//

                }

            return Response()->json([
                      'success' => $status,
                    ]);
            }

        }

      }





    public function SellerSendVerifyCode(Request $r,$data){

        if($r->ajax())
        {
          // 
            $data =$data;
            $mobile=false;
            if(is_numeric($data)){
                $sellerUser =User::where('mobile',$data)->where('business',true)->first();
                Session::put('mobile', $data);
                $mobile=true;
            }

            if($sellerUser){
              $status=false;
              return Response()->json([
                      'success' => $status,
                    ]);
            }else{
                $user =User::where('mobile',$data)->first();

                $status=true;
                if($user){
                $verify=true;  
                }else{
                 $verify=false;   
                }
                

                $verifycode = mt_rand(100000,999999);
                Session::put('verifycode', $verifycode);
                $verifycode=Session::get('verifycode');
                
                $general = General::first();
                if(is_numeric($data) && $general->sms_status){
                    //Send SMS User
                            $m =$data;
                            
                            $to =bdMobile($m);
                            
                            if(strlen($to) != 13)
                            {
                                //return true;
                            }else{
                            $msg = urlencode("Your Verify OPT Code Is {$verifycode} form  {$general->title}"); //150 characters allowed here
                
                            $url = smsUrl($to,$msg);
                        
                            $client = new Client();
                            
                            try {
                                    $r = $client->request('GET', $url);
                                } catch (\GuzzleHttp\Exception\ConnectException $e) {
                                } catch (\GuzzleHttp\Exception\ClientException $e) {
                                }
                            }
                    }

                $page =View('auth.includes.sellerFormData',compact('verify','user'))->render();

                return Response()->json([
                  'success' => $status,
                  'page' => $page,
                  'verify' => $verify,
                ]);


            }


        }

      }


      
       //Social login/Registration Function
       
      public function redirectToProvider($provider)
       {
           
           return Socialite::driver($provider)->redirect();
       }
       
       
       
       public function handleProviderCallback($provider, Request $request)
       {
           
           
           try {
    
               $user = Socialite::driver($provider)->stateless()->user();
    
           } catch (Exception $e) {
               return redirect('/login');
           }
    
           $authUser = $this->findOrCreateUser($user, $provider);
           
           $request->session()->regenerate();
           Auth::login($authUser, true);
           return Redirect()->route('customer.dashboard');

       }
       
       
        public function findOrCreateUser($providerUser, $provider)
       {
           
           $account = SocialIdentity::whereProviderName($provider)
                      ->whereProviderId($providerUser->getId())
                      ->first();
    
           if ($account) {
               return $account->user;
           } else {
               $user = User::whereEmail($providerUser->getEmail())->where('email', '<>', null)->first();
    
               if (! $user) {
                   
                   $rand =rand(100000,999999);
                   
                   $user = User::create([
                       'email' => $providerUser->getEmail(),
                       'name'  => $providerUser->getName(),
                       'email_verified_at' => Carbon::now(),
                       'password'=> Hash::make($rand),
                       'password_show'=> $rand,
                   ]);
  
                    $user->profile_photo_path = $providerUser->getAvatar();
                    $user->save();
          
               }
               
               $user->identities()->create([
                   'provider_id'   => $providerUser->getId(),
                   'provider_name' => $provider,
                   'provider_token'=> $providerUser->token,
                   'provider_img_url'=> $providerUser->getAvatar(),
               ]);
    
               return $user;
           }
       }















}
