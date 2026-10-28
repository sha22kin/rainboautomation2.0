<?php

namespace App\Http\Controllers\Api;


use Auth;
use Str;
use Hash;
use url;
use File;
use Session;
use Cookie;
use Carbon\Carbon;
use Redirect,Response;
use Validator;
use App\Models\User;
use App\Models\Cart;
use App\Models\Post;
use App\Models\Media;
use App\Models\WishList;
use App\Models\PostExtra;
use App\Models\Country;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\OrderReturnItem;
use App\Models\Review;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiUserController extends Controller
{
    
    public function __construct(Request $request)
    {
    	//$token = $request->header('authorization');
    	$token = $request->bearerToken();
    	
        if($token){
            $user = User::where('api_token','<>',null)->where('api_token',$token)->first();
            if($user) {
                $this->myUser =$user;
            }else{
                $this->myUser ='unAuthorize';
            }
            
        }else{
            $this->myUser ='tokenExprice';
        }
        
    }
    
    public function profile(Request $request){
        $user = $this->myUser;
        
        if($request->isMethod('post')){
            
            $rules = [
                'name' => 'required|max:50|unique:users,name,'.$user->id,
                'email' => 'required|max:100|unique:users,email,'.$user->id,
                'mobile' => 'nullable|max:20|unique:users,mobile,'.$user->id,
                'city' => 'nullable|numeric',
                'district' => 'nullable|numeric',
                'division' => 'nullable|numeric',
                'postal_code' => 'nullable|max:10',
                'address' => 'nullable|max:200',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ];
            
            $validator = Validator::make($request->all(), $rules);
            
            if ($validator->fails()) {
                return response()->json(['message'=>'The given data was invalid.','errors'=>$validator->messages()], 422);
            }
            
            $user->name =$request->name;
            $user->mobile =$request->mobile;
            $user->email =$request->email;
            $user->city =$request->city;
            $user->district =$request->district;
            $user->division =$request->division;
            $user->address_line1 =$request->address;
            $user->postal_code =$request->postal_code;
            
            ///////Image Uploard Start////////////
            if($request->hasFile('image')){
             $file=$request->image;
             
             $media =Media::latest()->where('src_type',6)->where('use_Of_file',1)->where('src_id',$user->id)->first();
              if(!$media){
              $media =new Media();
              }else{
    
                if(File::exists($media->file_url)){
                      File::delete($media->file_url);
                  }
              }
              $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
              $fullname = basename($file->getClientOriginalName());
              $ext =$file->getClientOriginalExtension();
              $size =$file->getSize();
    
              $year =carbon::now()->format('Y');
              $month =carbon::now()->format('M');
              $folder = $month.'_'.$year;
    
              $img =safeUploadName($file);
              $path ="medies/".$folder;
              $fullpath ="medies/".$folder.'/'.$img;
              $media->src_type=6;
              $media->use_Of_file=1;
              $media->src_id=$user->id;
              $media->file_name=Str::limit($fullname,250);
              $media->alt_text=Str::limit($name,250);
              $media->file_size=$size;
              $media->file_type=1;
              $file->move(public_path($path), $img);
              $media->file_url =$fullpath;
              $media->addedby_id=auth::id();
              $media->save();
    
          }
          
          ///////Image Uploard End////////////
          $user->save();

          return response()->json(['message'=>'Your Profile Are Successfully Updated','status'=>true]);

        }
        
        
        $myUser=array(
                    'id'=>$user->id,
                    'name'=>$user->name,
                    'id'=>$user->id,
                    'email'=>$user->email,
                    'mobile'=>$user->mobile,
                    'image'=>$user->image(),
                    'prefecture'=>$user->districtN?$user->districtN->name:null,
                    'city_name'=>$user->city_name,
                    'company_name'=>$user->company_name,
                    'address'=>$user->address_line1,
                    'postal_code'=>$user->postal_code,
                    'fulladdress'=>$user->fullAddress(),
                );
       return response()->json($myUser);
    }
    
    public function changePassword(Request $request){
     
        $user =$this->myUser;
        
        $rules = [
          'current_password' => 'required|string|min:5',
          'password' => 'required|string|min:8|different:current_password',
        ];
        
        $userPassw =User::find($user['id']);
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
         return response()->json(['message'=>'The given data was invalid.','errors'=>$validator->messages()], 422);
        } else {
            
          if(Hash::check($request->current_password, $userPassw->password)){
              $userPassw->password=Hash::make($request->password);
              $userPassw->password_show=$request->password;
              $userPassw->save();
              return response()->json(['message'=>'Password Update Successfully Done!']);
            }else{
            return response()->json(['message'=>'Carrent Password Are Not Match'], 422);
            }  
           
        }
        
    }
    
    
    
    
    
    
    
    public function logOut(Request $request){
        
        if ($request->isMethod('post'))
        {
            $user = $this->myUser;
            
            if($user=='tokenExprice'){
                return response()->json(['message'=>'Your Login Token Are Expire/Not Found','status'=>false],401);
            }elseif($user=='unAuthorize'){
                return response()->json(['message'=>'Your Login Access Are Not Allow','status'=>false],401);
            }else{
            $user->api_token=null;
            $user->save();
            return response()->json(['message'=>'Your Are Log-out Successfully Done','status'=>true]);
            }
        }
        
        return response()->json(['message'=>'This Method Request Are Not Allow','status'=>false],405);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}











