<?php

namespace App\Http\Controllers\Api;

use Mail;
use Auth;
use Hash;
use Session;
use Response;
use Cookie;
use Str;
use File;
use Validator;
use Carbon\Carbon;
use App\Models\Country;
use App\Models\Attribute;
use App\Models\Post;
use App\Models\PostExtra;
use App\Models\User;
use App\Models\Media;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ApiWelcomeController extends Controller
{

    public function generalInfo(Request $request){
        $general =general();
        $datas =array(
            'title'=>$general->title,
            'subtitle'=>$general->subtitle,
            'website_name'=>websiteTitle(),
            'website'=>$general->website,
            'logo'=>asset($general->logo),
            'favicon'=>asset($general->favicon),
            'mobile'=>$general->mobile,
            'email'=>$general->email,
            'footer_text'=>$general->copyright_text,
            'address_one'=>$general->address_one,
            'address_two'=>$general->address_two,
            'facebook_link'=>$general->facebook_link,
            'twitter_link'=>$general->twitter_link,
            'instagram_link'=>$general->instagram_link,
            'linkedin_link'=>$general->linkedin_link,
            'pinterest_link'=>$general->pinterest_link,
            'youtube_link'=>$general->youtube_link,
        );
        return Response()->json($datas);
    }
    
    public function geo_filter($id){
        $datas=Country::where('parent_id',$id)->select(['id','name'])->orderBy('name')->get();
        return Response()->json($datas);
    }
    
    public function slider(){

        $sliders =slider('Front Page Slider');
        if($sliders){
            if($sliders->subSliders->count()>0){
                $sliders = $sliders->subSliders()->select(['id','name','description'])->get();
                collect($sliders)->map(function($item){
                    $item->image_url = asset($item->image());
                    unset($item->imageFile);
                    return $item;
                });
            }
        }
        return Response()->json($sliders);
    }
    
    
    

    public function contactMail(Request $r){
 
        $rules = [
            'name' => 'required|max:100',
            'email' => 'required|email|max:100',
            //'subject' => 'required|max:100',
            'message' => 'required|max:500',
        ];
        
        $validator = Validator::make($r->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json(['message'=>'The given data was invalid.','errors'=>$validator->messages()], 422);
        }

      if(general()->mail_status && general()->mail_from_address){
            //Mail Data
            $datas =array('r'=>$r);
            $template ='mails.ContactMail';
            $toEmail =general()->mail_from_address;
            $toName =general()->mail_from_name;
            $subject ='Contact Mail Form '.general()->title;
        
           // sendMail($toEmail,$toName,$subject,$datas,$template);
        }

      return response()->json(['message'=>'Your form send successfully done. We are response as soon as possible.','status'=>true]);
    }
    
    public function subscribe(Request $r){
        
        $rules = [
            'email' => 'required|email|max:100',
        ];
        
        $validator = Validator::make($r->all(), $rules);
        
        if ($validator->fails()) {
            return response()->json(['message'=>'The given data was invalid.','errors'=>$validator->messages()], 422);
        }
        
        $subscribe =PostExtra::latest()->where('type',1)->where('name',$r->email)->first();

        if(!$subscribe){
            $subscribe =new PostExtra();
            $subscribe->type=1;
            $subscribe->name=$r->email;
            $subscribe->save();
          $status=true;
          $message ='You Are Successfully Subsribe';
        }else{
          $status=true;
          $message ='You Are Already Subscibe.Thank You';
        }
      
      return Response()->json([
        'success' => $status,
        'message' => $message,
      ]);
        
    }
    public function homeContent(){

        $fetured[]=array(
            'image_url'=>asset('welcome/images/growth.svg'),
            'title'=>'Business Strategy',
            'text'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.',
        );
        $fetured[]=array(
            'image_url'=>asset('welcome/images/international.svg'),
            'title'=>'Financial Planning',
            'text'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.',
        );
        $fetured[]=array(
            'image_url'=>asset('welcome/images/planning-1.svg'),
            'title'=>'International Business',
            'text'=>'Lorem ipsum dolor sit amet, consectetur adipiscing elit sed do eiusmod tempor.',
        );

        $services =Post::where('type',3)
        ->where('status','active')
        ->whereDate('created_at','<=',Carbon::now())
        ->limit(3)
        ->get(['id','name','slug','short_description']);

        collect($services)->map(function($item){
            $item->image_url =asset($item->image());
            unset($item->imageFile);
            return $item;
        });

        $link=null;
        if($page =pageTemplate('Latest Services')){
            $link =route('pageView',$page->slug?:'');
        }

        $servicesContent =array(
            'head'=>'KNOW OUR SERVICES',
            'title'=>'Our Business Consulting Case Services',
            'services'=>$services,
            'link'=>$link,
        );

        $blogs =Post::where('type',1)
        ->where('status','active')
        ->whereDate('created_at','<=',Carbon::now())
        ->limit(3)
        ->get(['id','name','slug','addedby_id','created_at']);

        collect($blogs)->map(function($item){
            $item->image_url =asset($item->image());
            $item->post_by =$item->user?$item->user->name:'';
            $item->formated_created_at =$item->created_at->format('d M');
            unset($item->user);
            unset($item->imageFile);
            return $item;
        });

        $link=null;
        if($page =pageTemplate('Latest Blog')){
            $link =route('pageView',$page->slug?:'');
        }

        $blogsContent =array(
            'head'=>'BLOG & NEWS',
            'title'=>'Our Business Latest Blog & News',
            'blogs'=>$blogs,
            'link'=>$link,
        );

        return Response()->json(['featuredContent'=>$fetured,'servicesContent'=>$servicesContent,'blogsContent'=>$blogsContent]);
    }

    public function menu($location=null){
        $menu =null;
        if($location){
            $hasMenu=null;
            if($location=='header_menus'){
                $hasMenu =menu('Header Menus');
            }elseif($location=='footer_two'){
                $hasMenu =menu('Footer Two');
            }elseif($location=='footer_three'){
                $hasMenu =menu('Footer Three');
            }

            if($hasMenu){
                
                $menuItems = array();

                foreach ($hasMenu->subMenus as $menu) {
                    $subMenuItems = array();

                    foreach ($menu->subMenus as $subMenu) {
                        $subSubMenuItems = array();

                        foreach ($subMenu->subMenus as $subSubMenu) {
                            // You can add more levels as needed
                            $subSubMenuItems[] = array(
                                'title' => $subSubMenu->menuName(),
                                'icon'  => '',
                                'slug'  => $subSubMenu->menuSlug(),
                                'type'  => $subSubMenu->menu_type,
                                'items'=>''
                            );
                        }

                        $subMenuItems[] = array(
                            'title' => $subMenu->menuName(),
                            'icon'  => '',
                            'slug'  => $subMenu->menuSlug(),
                            'type'  => $subMenu->menu_type,
                            'items' => $subSubMenuItems,
                        );
                    }

                    $menuItems[] = array(
                        'title' => $menu->menuName(),
                        'icon'  => '',
                        'slug'  => $menu->menuSlug(),
                        'type'  => $menu->menu_type,
                        'items' => $subMenuItems,
                    );
                }

                $menu = array(
                    'title' => $hasMenu->name,
                    'items' => $menuItems,
                );
            }
        }
        

        return Response()->json($menu);
    }

    public function pageView($slug){
        $hasPage =Post::latest()->where('type',0)->where('slug',$slug)->first();
        if(!$hasPage){
            return response()->json(['message'=>'This Page Are Not Found'], 404);
        }

        $page =array(
            'name'=>$hasPage->name,
            'slug'=>$hasPage->slug,
            'image_url'=>$hasPage->bannerFile?asset($hasPage->banner()):'',
            'template'=>$hasPage->template,
            'short_description'=>$hasPage->short_description,
            'description'=>$hasPage->description,
        );

        return  Response()->json($page);
    }

    public function serviceCategory($slug){
        $hasCategory =Attribute::latest()->where('type',0)->where('slug',$slug)->first();
        if(!$hasCategory){
            return response()->json(['message'=>'This Category Are Not Found'], 404);
        }
  
        $services = Post::whereHas('ctgServices',function($q) use($hasCategory){
          $q->where('reff_id',$hasCategory->id);
        })
        ->where(function($qq){
          $qq->where('status','active');
        })
        ->select(['id','name','slug','addedby_id','created_at'])
        ->whereDate('created_at','<=',date('Y-m-d'))
        ->paginate(12);
        
        collect($services->items())->map(function($item){
            $item->image_url =asset($item->image());
            unset($item->imageFile);
            return $item;
        });

        $category =array(
            'name'=>$hasCategory->name,
            'image_url'=>$hasCategory->bannerFile?asset($hasCategory->banner()):'',
        );

        return  Response()->json([$category,$services]);
      }
    
  
      public function serviceView($slug){
        $service =Post::latest()->where('type',3)->where('slug',$slug)->first();
        if(!$service){
          return response()->json(['message'=>'This Service Are Not Found'], 404);
        }
        
        $serviceArray=[
                'title'=>$service->name,
                'slug'=>$service->slug,
                'short_description'=>$service->short_description,
                'description'=>$service->description,
                'view'=>$service->view,
                'image_url'=>asset($service->image()),
                'created_at'=>$service->created_at,
            ];
  
        return  Response()->json($serviceArray);
      }
      
      public function blogCategory($slug){
        $hasCategory =Attribute::latest()->where('type',6)->where('slug',$slug)->first();
        if(!$hasCategory){
            return response()->json(['message'=>'This Category Are Not Found'], 404);
        }
        
        $posts = $hasCategory->activePosts()->latest()
          ->select(['id','name','slug','short_description','addedby_id','created_at'])
          ->paginate(10);
          
        collect($posts->items())->map(function($item){
            $item->image_url =asset($item->image());
            $item->post_by =$item->user?$item->user->name:'';
            $item->formated_created_at =$item->created_at->format('d M');
            unset($item->user);
            unset($item->imageFile);
            return $item;
        });

        $category =array(
            'name'=>$hasCategory->name,
            'image_url'=>$hasCategory->image(),
        );

        return  Response()->json([$category,$posts]);
      }
      
      public function blogView($slug){
        $blog =Post::latest()->where('type',1)->where('slug',$slug)->first();
        if(!$blog){
          return response()->json(['message'=>'This Blog Are Not Found'], 404);
        }
        
        $blogArray=[
                'title'=>$blog->name,
                'slug'=>$blog->slug,
                'short_description'=>$blog->short_description,
                'description'=>$blog->description,
                'view'=>$blog->view,
                'image_url'=>asset($blog->image()),
                'post_By'=>$blog->user?$blog->user->name:'',
                'comments_view'=>asset($blog->image()),
                'created_at'=>$blog->created_at,
                'formated_created_at'=>$blog->created_at->format('d M'),
            ];
        
        $realatedBlog =$blog->relatedPosts()->limit(3)->select(['id','name','slug','short_description','addedby_id','created_at'])->get();
        $comments =$blog->postComments()->where('status','active')->select(['id','name','content','created_at'])->paginate(10);
  
        return  Response()->json(['blog'=>$blogArray,'comments'=>$comments,'realatedBlog'=>$realatedBlog]);
      }





}