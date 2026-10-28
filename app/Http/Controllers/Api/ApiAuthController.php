<?php

namespace App\Http\Controllers\Api;


use Auth;
use Str;
use Hash;
use Mail;
use url;
use Session;
use Cookie;
use Redirect,Response;
use Validator;
use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiAuthController extends Controller
{
    
    public function __construct(Request $request)
    {
    	$this->apiToken = uniqid(base64_encode(Str::random(60)));
        
    }
    
    public function login(Request $request){
        
       // return $request->email;
        
        if ($request->isMethod('post'))
        {
            $rules = [
              'email' => 'required|email|max:100',
              'password' => 'required|min:5'
            ];
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                $erros =null;
                // $errors = $validator->messages();
                // if($errors->has('email') && $errors->has('password')){
                //     $erros= 'Your Invalid Email And Password';
                // }elseif($errors->has('email')){
                //     $erros= $errors->first('email');
                // }elseif($errors->has('password')){
                //     $erros= $errors->first('password');
                // }

             return response()->json(['message'=>$erros,'status'=>false], 422);
             //return response()->json(['message'=>'The given data was invalid.','errors'=>$validator->messages()], 422);
            }
            
            $login = $request->email;
            $password = $request->password;

            $field =null;
            if(is_numeric($login)){
                $field = 'mobile';
            } elseif (filter_var($login, FILTER_VALIDATE_EMAIL)) {
                $field = 'email';
            }else{
                $field ='name';
            }
            
            if($field){
                
                $user =User::where($field,$login)->first();
                if($user){
                        
                        if(Hash::check($password, $user->password)){
                            $user->api_token=$this->apiToken;
                            $user->save();
                              if($login) {
                                return response()->json(['user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'mobile'=>$user->mobile,'image'=>$user->image(),'access_token'=>$this->apiToken],'status'=>true]);
                                 //return response()->json(['userId'=>$user->id,'name'=>$user->name,'email'=>$user->email,'mobile'=>$user->mobile,'img_name'=>$user->img_name,'access_token'=>$this->apiToken]); 
                              }
                		
                    	}else{
                    	 return response()->json(['message'=>'Your Acount Password Are Incorrect','status'=>false],422); 
                    	}
                    	
                }else{
                    return response()->json(['message'=>'You Are Not authenticated User','status'=>false],401); 
                }
                
            }else{
               return response()->json(['message'=>'User Email/Phone Numer Are Invalied','status'=>false],401);
            }
            
            
        }
     
        return Response()->json(['message'=>'This Method Request Are Not Allow','status'=>false],405); 
        
    }
    
    public function registration(Request $request){
        
        if ($request->isMethod('post'))
        {
            $rules = [
              //'username' => 'required|max:100|unique:users,name',
              'name' => 'required|max:100',
              //'email' => 'required|max:100|unique:users,email',
              'email' => 'required|max:100',
              'password' => 'required|min:5'
            ];
            
            $validator = Validator::make($request->all(), $rules);
        
            if ($validator->fails()) {
                
                $erros =null;
                $errors = $validator->messages();
                if($errors->has('name')){
                    $erros= $errors->first('name');
                }elseif($errors->has('email')){
                    $erros= $errors->first('email');
                }elseif($errors->has('password')){
                    $erros= $errors->first('password');
                }
                
              return response()->json(['message'=>$erros,'status'=>false], 422);
             //   return response()->json(['message'=>'The given data was invalid.','errors'=>$validator->messages()], 422);
            }
            
            $email =User::where('email',$request->email)->first();
            if($email){
                $erros ='Your Email Are Already Register';
                return response()->json(['message'=>$erros,'status'=>false], 422);
            }
            
            $user =new User();
            $user->name=$request->name;
            $user->email=$request->email;
            $user->password=Hash::make($request->password);
            $user->password_show=$request->password;
            $user->api_token=$this->apiToken;
            $user->country=1;
            $user->save();
          
            if($user){
                
                $general =general();
                
                //**********Send Mail***************//
        
                // if($general->mail_status && $user->email){
        
                //     Mail::send('mails.registrationMail', ['user' => $user,'general'=>$general], function ($message) use ($user,$general) {
        
                //         $message->from($general->mail_from_address,$general->mail_from_name);
        
                //         $message->to($user->email,$user->name)
                //         ->subject('Registration Successfully Completed in '.$general->mail_from_name.".");
                //     });
                    
                // }
                //**********Send Mail***************//
                
                return response()->json(['user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'mobile'=>$user->mobile,'image'=>$user->image(),'access_token'=>$this->apiToken],'status'=>true]);
            } else {
                return response()->json(['message'=>'Registration failed, please try again.','status'=>false],500);
            }
            
            
        }
        return Response()->json(['message'=>'This Method Request Are Not Allow','status'=>false],405); 
        
    }
    
    public function forgotPassword(Request $request){
        if ($request->isMethod('post'))
        {
            $rules = [
              'email' => 'required|email|max:100',
            ];
            
            $validator = Validator::make($request->all(), $rules);
        
            if ($validator->fails()) {
                
                $erros =null;
                $errors = $validator->messages();
                if($errors->has('email')){
                    $erros= $errors->first('email');
                }

             return response()->json(['message'=>$erros,'status'=>false], 422);
                
                
             //return response()->json(['message'=>'The given data was invalid.','errors'=>$validator->messages()], 422);
            }
            
            $user =User::where('email','<>',null)->where('email',$request->email)->first();

            if(!$user){
                return Response()->json(['message'=>'There is no account with the Email Address you provided.','status'=>false],401); 
            }
            
            $verifycode = mt_rand(100000,999999);
            $token = Str::random(60).$user->id;
            $user->verify_code=$verifycode;
            $user->reset_remember=$token;
            $user->save();
            
            $general =general();
            
            //**********Send Mail***************//
            
            // if($general->mail_status && $user->email){
    
            //     Mail::send('mails.passwordResetVerify', ['user' => $user,'general'=>$general], function ($message) use ($user,$general) {

            //         $message->from($general->mail_from_address,$general->mail_from_name);
    
            //         $message->to($user->email,$user->name)
            //         ->subject('Reset password verify code from '.$general->mail_from_name.".");
            //     });
                
            // }
            //**********Send Mail***************//
            
            return response()->json(['message'=>'We are send reset code your email. Please Check.','status'=>true]);

        }
        
        return Response()->json(['message'=>'This Method Request Are Not Allow','status'=>false],405); 
    }
    
    public function resetPassword(Request $request){
        if ($request->isMethod('post'))
        {
             $rules = [
              'email' => 'required|email|max:100',
              'verifycode' => 'required|numeric|digits:6',
              'password' => 'required|min:6',
            ];
            
            $validator = Validator::make($request->all(), $rules);
        
            if ($validator->fails()) {
                $erros =null;
                $errors = $validator->messages();
                if($errors->has('email')){
                    $erros= $errors->first('email');
                }elseif($errors->has('verifycode')){
                    $erros= $errors->first('verifycode');
                }elseif($errors->has('password')){
                    $erros= $errors->first('password');
                }
                return response()->json(['message'=>$erros,'status'=>false], 422);
                
                
             //return response()->json(['message'=>'The given data was invalid.','errors'=>$validator->messages()], 422);
            }
            
            $user =$user =User::where('email','<>',null)->where('email',$request->email)->first();

        
            if($user){
                
                if($user->verify_code==$request->verifycode){
                        $user->password=Hash::make($request->password);
                        $user->password_show=$request->password;
                        $user->api_token=$this->apiToken;
                        $user->verify_code=null;
                        $user->save();
                        
                        return response()->json(['user'=>['id'=>$user->id,'name'=>$user->name,'email'=>$user->email,'mobile'=>$user->mobile,'image'=>$user->image(),'access_token'=>$this->apiToken],'status'=>true]);
                }else{
                    return response()->json(['message'=>'Your Verify Code Are Invalid','status'=>false],422);
                }
                
                
            }else{
                return response()->json(['message'=>'You Are Not authenticated User','status'=>false],401); 
            }
            
            
        }
        
        return Response()->json(['message'=>'This Method Request Are Not Allow','status'=>false],405); 
    }
    
    
    
    
    
    
    
    
}