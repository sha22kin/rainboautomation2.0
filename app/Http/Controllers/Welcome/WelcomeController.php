<?php

namespace App\Http\Controllers\Welcome;


use Image;
use Auth;
use Hash;
use Str;
use Artisan;
use Session;
use Carbon\Carbon;
use App\Models\Country;
use App\Models\Post;
use App\Models\Media;
use App\Models\PostExtra;
use App\Models\User;
use App\Models\Attribute;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;


class WelcomeController extends Controller
{
  
    
	public function geo_filter($id){

      $datas=Country::where('parent_id',$id)->get();

      $geoData =View('geofilter',compact('datas'))->render();

       return Response()->json([
              'success' => true,
              'geoData' => $geoData,
            ]);
    }
    
    public function imageView(Request $r){
        if($r->imageUrl && is_numeric($r->weight) && is_numeric($r->height)){
          $image = Image::make($r->imageUrl)->fit($r->weight,$r->height)->response();
        }else{
          $image = Image::make(public_path('medies/noimage.jpg'))->fit(200,200)->response();
        }
        return $image;
    }
    
    public function imageView2(Request $r,$template=null,$image=null){
        $weight=null;
        $height=null;
        
        if(is_numeric($r->w)){
          $weight=$r->w;  
        }
        if(is_numeric($r->h)){
          $height=$r->h;  
        }
        
        $filePath='medies/noimage.jpg';

        if($image){
            $file =Media::where('file_rename',$image)->select(['file_url'])->first();
            if($file){
                $filePath = $file->file_url;
            }
        }
        
        
        // if($template=='s-profile'){
            
        //     if($image && $image!='profile.png' && $file){
        //         $filePath = $file->file_url;
        //     }else{
        //         $filePath ='medies/profile.png';
        //     }
        // }
        
        $mImage =Image::make(public_path($filePath));
        if($weight && $height){
            $mImage=$mImage->fit($weight,$height);
        }
        $mImage=$mImage->response();
        
        return $mImage;
    }
    public function siteMapXml(Request $r){
        
      $pages = Post::latest()->where('type',0)->where('status','active')->select(['slug','updated_at','status'])->limit(200)->get();
      $posts = Post::latest()->where('type',1)->where('status','active')->select(['slug','updated_at','status'])->limit(500)->get();
      $products = Post::latest()->where('type',2)->where('status','active')->select(['slug','updated_at','status'])->limit(300)->get();
      
      return response()->view('siteMap',compact('pages','posts','products'))->header('Content-Type', 'text/xml');
    }
    public function language($lang=null){
      if($lang){
          Session::put('lang',$lang);
      }else{
          Session::put('lang','en');
      }
      return redirect()->back();
    }

    public function index(Request $r){
        
    //   Artisan::call('cache:clear');
    //   Artisan::call('config:clear');
    //   Artisan::call('config:cache');
    //   Artisan::call('view:clear');
    //   Artisan::call('route:clear');
    //   Artisan::call('clear-compiled');

    	return $this->designPage('Front Page');
    }

    public function about(){
      return $this->designPage('About Us');
    }

    public function services(){
      return $this->designPage('Services');
    }

    public function solar(){
      return $this->designPage('Solar Solution');
    }

    public function training(){
      return $this->designPage('Training Academy');
    }

    public function industrial(){
      return $this->designPage('Industrial Solution');
    }

    public function contact(){
      return $this->designPage('Contact Us');
    }

    // Render a design template. The admin page using that template (Admin → Pages) supplies the
    // SEO title/description; when its slug differs from the fixed URL, visitors go to the admin slug.
    protected function designPage($template,$page=null){
      $config =designTemplates()[$template];
      if(!$page){
        $page =templatePage($template);
        if($page && $template!='Front Page' && route('pageView',$page->slug)!=url()->current()){
          return redirect()->route('pageView',$page->slug,301);
        }
      }
      return $this->themeView($config['view'],compact('page'));
    }

    // Views of the previous theme (blogs, services, old page templates) were removed with the redesign;
    // their routes now answer 404 instead of a missing-view error.
    protected function themeView($view,$data=[]){
      if(!view()->exists(welcomeTheme().$view)){
        abort(404);
      }
      return view(welcomeTheme().$view,$data);
    }

    public function serviceCategory($slug){
      $category =Attribute::latest()->where('type',0)->where('slug',$slug)->first();
      if(!$category){
        return abort('404');
      }

      $services = Post::whereHas('ctgServices',function($q) use($category){
        $q->where('reff_id',$category->id);
      })
      ->where(function($qq){
        $qq->where('status','active');
      })
      ->select(['id','name','slug','addedby_id','created_at','short_description'])
      ->whereDate('created_at','<=',date('Y-m-d'))
      ->paginate(12);

      return $this->themeView('services.categoryServices',compact('category','services'));
    }

    public function serviceView($slug){
      $service =Post::latest()->where('type',3)->where('slug',$slug)->first();
      if(!$service){
        return abort('404');
      }

      return $this->themeView('services.serviceView',compact('service'));
    }

    public function blogCategory($slug){
      $category =Attribute::latest()->where('type',6)->where('slug',$slug)->first();
      if(!$category){
        return abort('404');
      }

      $posts = $category->activePosts()->latest()
      ->select(['id','name','slug','short_description','addedby_id','created_at'])
      ->paginate(10);

      return $this->themeView('blogs.categoryPosts',compact('category','posts'));
    }

    public function blogTag($slug){
      $tag =Attribute::latest()->where('type',7)->where('slug',$slug)->first();
      if(!$tag){
        return abort('404');
      }

      $posts = Post::whereHas('tagPosts',function($q) use($tag){
        $q->where('reff_id',$tag->id);
      })
      ->where(function($qq){
        $qq->where('status','active');
      })
      ->select(['id','name','slug','short_description','addedby_id','created_at'])
      ->whereDate('created_at','<=',date('Y-m-d'))
      ->paginate(10);

      return $this->themeView('blogs.tagPosts',compact('tag','posts'));
    }

    public function blogAuthor($id,$slug){
      $author =User::find($id);
      if(!$author){
        return abort('404');
      }
      $posts =$author->posts()->latest()->where('type',1)->where('status','active')
      ->select(['id','name','slug','short_description','addedby_id','created_at'])
      ->whereDate('created_at','<=',date('Y-m-d'))
      ->paginate(10);
      return $this->themeView('blogs.authorPosts',compact('author','posts'));

    }

    public function blogView($slug){
      $post =Post::where('type',1)->where('slug',$slug)->first();
      if(!$post){
        return abort('404');
      }
      $relatedPosts =$post->relatedPosts()->limit(3)->select(['id','name','slug','short_description','addedby_id','created_at'])->get();
      $comments =$post->postComments()->where('status','active')->select(['id','name','content','created_at'])->paginate(10);
      return $this->themeView('blogs.blogView',compact('post','relatedPosts','comments'));

    }

    public function blogSearch(Request $r){
      $check = $r->validate([
          'search' => 'required|max:100',
      ]);
      
      $posts =Post::latest()->where('type',1)->where('status','active')
      ->where(function($q) use ($r) {
        if($r->search){
          $q->where('name','LIKE','%'.$r->search.'%');
        }

      })
      ->select(['id','name','slug','short_description','addedby_id','created_at'])
      ->whereDate('created_at','<=',date('Y-m-d'))
      ->paginate(10)->appends([
        'search'=>$r->search,
      ]);

      return $this->themeView('blogs.blogSearch',compact('posts','r'));

    }

    public function blogComments(Request $r,$slug){
      $post =Post::where('type',1)->where('slug',$slug)->first();
      if(!$post){
        return abort('404');
      }

      $check = $r->validate([
          'name' => 'required|max:100',
          'email' => 'required|max:100',
          'website' => 'required|max:100',
          'comment' => 'required|max:1000',
      ]);

      $comments =new Review();

      if(Auth::check()){
      $comments->addedby_id=Auth::id();
      }
      $comments->src_id=$post->id;
      $comments->type=1;
      $comments->name=$r->name;
      $comments->email=$r->email;
      $comments->website=$r->website;
      $comments->content=$r->comment;
      $comments->save();

      Session()->flash('success','Your Comments successfully Submitted.');

      return back();


    }


    public function pageView($slug){
    
      // Only published pages / posts (inactive and draft ones are not public)
      $page =Post::latest()->whereIn('type',[0,1])->where('status','active')->where('slug',$slug)->first();

      if(!$page){
        return abort('404');
      }
      
      if($page->type==1){
          
          $post =$page;
          
          $relatedPosts =$post->relatedPosts()->limit(3)->select(['id','name','slug','short_description','addedby_id','created_at'])->get();
          $comments =$post->postComments()->where('status','active')->select(['id','name','content','created_at'])->paginate(10);
          
          return $this->themeView('blogs.blogView',compact('post','relatedPosts','comments'));
      }
      //If deferent Design or Condition Page Return by ID.

      //Font Home Page
      if($page->template=='Front Page'){
        return redirect()->route('index');
      }

      //Rainbow Automation design pages
      if(array_key_exists($page->template,designTemplates())){
        return $this->designPage($page->template,$page);
      }

      
      //Clients Page
      if($page->template=='All Clients'){
        return $this->themeView('pages.clients',compact('page'));
      }

      //Latest Blog Page
      if($page->template=='Latest Blog'){
        $posts = Post::latest()->where('type',1)->where('status','active')
        ->select(['id','name','slug','short_description','addedby_id','created_at'])
        ->whereDate('created_at','<=',date('Y-m-d'))
        ->paginate(10);
        return $this->themeView('blogs.latestBlogs',compact('posts','page'));
      }

      //Latest Services Page
      if($page->template=='Latest Services'){
        return redirect()->route('services');
      }

      return $this->themeView('pages.pageView',compact('page'));

    }

    // Training Academy "Course Inquiry" form (saved for Admin → Training Courses → Course Inquiries)
    public function courseInquiry(Request $r){
      $r->validate([
          'course' => 'required|integer',
          'name' => 'required|max:100',
          'phone' => 'required|max:30',
          'email' => 'nullable|email|max:100',
          'message' => 'nullable|max:1000',
      ]);

      $course =trainingCourses()->firstWhere('id',(int)$r->course);
      if(!$course){
        return response()->json(['success'=>false,'message'=>'This course is no longer available.'],422);
      }

      $inquiry =new PostExtra();
      $inquiry->type=5;
      $inquiry->src_id=$course->id;
      $inquiry->parent_id=$course->src_id;
      $inquiry->name=$r->name;
      $inquiry->email=$r->email;
      $inquiry->content=json_encode([
        'phone'=>$r->phone,
        'message'=>$r->message,
        'course_title'=>$course->course('title'),
        'ip'=>$r->ip(),
      ],JSON_UNESCAPED_UNICODE);
      $inquiry->status='unread';
      $inquiry->save();

      return response()->json([
        'success'=>true,
        'message'=>'Your inquiry has been received. Our training team will contact you soon.',
      ]);
    }

    // Contact Us form → sent with Admin → Setting → Mail (SMTP) to the Author Mail / Mail From Address inbox
    public function contactMail(Request $r){
      $r->validate([
          'name' => 'required|max:100',
          'phone' => 'required|max:30',
          'email' => 'nullable|email|max:100',
          'type' => 'nullable|max:100',
          'message' => 'required|max:2000',
      ]);

      $general =general();
      if(!$general->mail_status || !$general->mail_from_address){
        return response()->json(['success'=>false,'message'=>'Sorry, our email service is not available right now. Please call us or try again later.'],503);
      }

      $data =array(
        'name'=>$r->name,
        'phone'=>$r->phone,
        'email'=>$r->email,
        'type'=>$r->type ?: 'General Inquiry',
        'message'=>$r->message,
        'page'=>url()->previous(),
        'ip'=>$r->ip(),
      );
      $datas =array('r'=>$data);
      $template ='mails.ContactMail';
      // Admin → Mail Setting → Author Mail (comma separated); falls back to the Mail From Address
      $adminMails =array_values(array_filter(array_map('trim',explode(',',(string)$general->admin_mails)),function($mail){
        return filter_var($mail,FILTER_VALIDATE_EMAIL);
      }));
      $toEmail =$adminMails ?: $general->mail_from_address;
      $toName =$adminMails ? null : $general->mail_from_name;
      $subject =$general->title.' website inquiry: '.$data['type'];
      $replyTo =$r->email ? ['email'=>$r->email,'name'=>$r->name] : null;

      if(!sendMail($toEmail,$toName,$subject,$datas,$template,null,$replyTo)){
        return response()->json(['success'=>false,'message'=>'Sorry, your message could not be sent. Please try again later.'],500);
      }

      return response()->json([
        'success'=>true,
        'message'=>'Thank you! Your message has been sent. Our team will contact you soon.',
      ]);
    }

    public function search(Request $r){
      
      if($r->search){

        $posts =Post::where('status','active')
        ->where(function($q) use($r){
          $q->where('search_key','LIKE','%'.$r->search.'%');
        })
        ->paginate(24);

      }else{
        $posts = array();
      }

      return $this->themeView('search');

    }

    public function subscribe(Request $r){

      if(filter_var($r->email, FILTER_VALIDATE_EMAIL)){
        $subscribe =PostExtra::latest()->where('type',1)->where('name',$r->email)->first();

        if(!$subscribe){
          
            $subscribe =new PostExtra();
            $subscribe->type=1;
            $subscribe->name=$r->email;
            $subscribe->save();
          $status=true;
          $message ='<p style="color: #009688;"><span>Success:</span> You Are Successfully Subsribe.</p>';
        }else{
          $status=true;
          $message ='<p style="color: #ffc107;"><span>Note:</span> You Are Already Subsribe.Thank You.</p>';
        }


      }else{
        $status=false;
        $message ='<p style="color: #ff5722;"><span>Error:</span> Email Are Not validated</p>';
      }

      if(request()->ajax()){
        
        return Response()->json([
                'success' => $status,
                'message' => $message,
              ]);
      }

      Session()->flash($status?'success':'error',$message);
      return redirect()->back();

    }





}
