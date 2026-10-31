<?php

namespace App\Http\Controllers\Admin;

use Auth;
use Str;
use Hash;
use File;
use DB;
use Image;
use Artisan;
use Session;
use Validator;
use Redirect,Response;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Post;
use App\Models\Transaction;
use App\Models\PostExtra;
use App\Models\Review;
use App\Models\General;
use App\Models\Country;
use App\Models\Media;
use App\Models\Attribute;
use App\Models\Permission;
use App\Models\PostAttribute;
use App\Models\PostContentElement;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    
    public function resizeImage($loadImage,$type,$w,$h){
        
        $img =	$type.$loadImage->file_rename;
        $fullpath =$loadImage->file_path.'/'.$img;
        $path = public_path($loadImage->file_path.'/');

        $image = Image::make(public_path($loadImage->file_url));
        $image->fit($w,$h);
        $image->save($path.$img);
        
        if($type=='sm'){
           $loadImage->file_url_sm=$fullpath;
           $loadImage->save(); 
        }elseif($type=='md'){
           $loadImage->file_url_md=$fullpath;
           $loadImage->save(); 
        }elseif($type=='lg'){
           $loadImage->file_url_lg=$fullpath;
           $loadImage->save(); 
        }
        
        return true;
    }
    
    public function dashboard(){
        // $datas = [];
        // return PostExtra::count();
        // $apiUrl = 'https://randomuser.me/api/1.4/?results=250';
        // $datasApi =null;
        //         try {
        //             $ch = curl_init();
        
        //             // Set cURL options
        //             curl_setopt($ch, CURLOPT_URL, $apiUrl);
        //             curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        //             // Additional options if needed...
        
        //             // Execute cURL session and capture the output
        //             $apiResponse = curl_exec($ch);
        
        //             // Check for cURL errors
        //             if (curl_errno($ch)) {
        //                 throw new \Exception('Curl error: ' . curl_error($ch));
        //             }
        
        //             // Close cURL session
        //             curl_close($ch);
        
        //             // Process and use the $apiResponse as needed
        //             $datasApi = json_decode($apiResponse, true);
        //         } catch (\Exception $e) {
        //             // Handle any exceptions that occur during the cURL request
        //             //return response()->json(['error' => $e->getMessage()], 500);
        //         }
                
                // if($datasApi){

                //     foreach($datasApi['results'] as $result){
                //         $name =null;
                //         if(isset($result['name']['title'])){
                //             $name .=$result['name']['title'];
                //         }
                //         if(isset($result['name']['first'])){
                //             $name .=' '.$result['name']['first'];
                //         }
                //         if(isset($result['name']['last'])){
                //             $name .=' '.$result['name']['last'];
                //         }
                //         $email =null;
                //         if(isset($result['email'])){
                //             $email .=$result['email'];
                //         }
                        
                //         $datas[] = [
                //         'name' => $name,
                //         'email' => $email,
                //         'status' => 'active',
                //         'type' => 0,
                //         'addedby_id' => Auth::id(),
                //         ];

                //     }
                // }
        // Insert data into the database
        //PostExtra::insert($datas);
               
     
        //         return 'stopo';
        // for ($i = 1; $i <= 10000; $i++) {
        //     $datas[] = [
        //         'name' => 'Name ' . $i+40000,
        //         'content' => 'Content ' . $i+40000,
        //         'drag' => $i+40000,
        //         'status' => 'active',
        //         'type' => 0,
        //         'addedby_id' => Auth::id(),
        //         // Add other fields as needed
        //     ];
        // }

        // // Insert data into the database
        // PostExtra::insert($datas);
        
        // return PostExtra::where('type',0)->select(['name as namefile','status as statusName',DB::raw('COUNT(*) as rowcount')])->limit(100)->get();
        ///Reports  Summery Dashboard
        $services30Days=Post::where('type',3)
        ->where('status','<>','temp')
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->count();

        $posts30Days=Post::where('type',1)
        ->where('status','<>','temp')
        ->whereMonth('created_at', Carbon::now()->month)
        ->whereYear('created_at', Carbon::now()->year)
        ->count();

        $pagesTotal=Post::where('type',0)
        ->where('status','<>','temp')
        ->count();

        $userTotal=User::where('customer',true)
        ->where('status',1)
        ->count();

        $reports=array(
                    "services"=>$services30Days,
                    "posts"=>$posts30Days,
                    "pages"=>$pagesTotal,
                    "users"=>$userTotal,
                );
        ///Reports  Summery Dashboard

        $posts =Post::latest()->where('type',1)->where('status','<>','temp')->paginate(10);

        $recentPages =Post::latest('updated_at')->where('type',0)->where('status','<>','temp')->limit(6)->get();

        return view(adminTheme().'dashboard',compact('posts','reports','recentPages'));
      
    }



    public function myProfile(Request $r){

      $user =Auth::user();
      if($r->isMethod('post')){
        if($r->actionType=='profile'){
          $check = $r->validate([
            'name' => 'required|max:100|unique:users,name,'.$user->id,
            'email' => 'required|max:100|unique:users,email,'.$user->id,
            'mobile' => 'nullable|max:20|unique:users,mobile,'.$user->id,
            'gender' => 'nullable|max:10',
            'address' => 'nullable|max:191',
            'division' => 'nullable|numeric',
            'district' => 'nullable|numeric',
            'city' => 'nullable|numeric',
            'postal_code' => 'nullable|max:20',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);
  
          $user->name =$r->name;
          $user->mobile =$r->mobile;
          $user->email =$r->email;
          $user->gender =$r->gender;
          $user->address_line1 =$r->address;
          $user->division =$r->division;
          $user->district =$r->district;
          $user->city =$r->city;
          $user->postal_code =$r->postal_code;
          ///////Image UploadStart////////////
          if($r->hasFile('image')){
            $file =$r->image;
            $src  =$user->id;
            $srcType  =6;
            $fileUse  =1;
            $author=Auth::id();
            
            $loadImage =uploadFile($file,$src,$srcType,$fileUse,$author);
            // //Resize Image 
            // $w=250;
            // $h=250;
            // $type='md';
            // $this->resizeImage($loadImage,$type,$w,$h);
            
            // //Resize Image 
            // $w=50;
            // $h=50;
            // $type='sm';
            // $this->resizeImage($loadImage,$type,$w,$h);
            
            // //Resize Image 
            // $w=400;
            // $h=400;
            // $type='lg';
            // $this->resizeImage($loadImage,$type,$w,$h);
            
          }
          ///////Image Upload End////////////
          $user->save();
  
          Session()->flash('success','Your Updated Are Successfully Done!');
  
        }
        if($r->actionType=='change-password'){
  
          $check = $r->validate([
              'old_password' => 'required|string|min:8',
              'password' => 'required|string|min:8|confirmed|different:old_password',
          ]);
  
          if(Hash::check($r->old_password, $user->password)){
            $user->password_show=$r->password;
            $user->password=Hash::make($r->password);
            $user->update();
            Session()->flash('success','Your Are Successfully Done');
          }else{
            Session()->flash('error','Current Password Are Not Match');
          }
        }
        return back();
      }
        
      return view(adminTheme().'users.myProfile',compact('user'));
      
    }


    //Medias Library Route
  public function medies(Request $r){

    //Check Authorized User
    $allPer = empty(json_decode(Auth::user()->permission->permission, true)['medies']['all']);

    //Media Delete All Selected Images Start
    if($r->actionType=='allDelete'){

      $check = $r->validate([
          'mediaid.*' => 'required|numeric',
      ]);

      for ($i=0; $i < count($r->mediaid); $i++) { 
        $media =Media::find($r->mediaid[$i]);
        if($media){

          if($allPer && $media->addedby_id!=Auth::id()){
            //You are unauthorized Try!!;
          }else{

            if(File::exists($media->file_url)){
                File::delete($media->file_url);
            }
            $media->delete();

          }

        }
      }

      Session()->flash('success','Your Are Successfully Deleted');
      return redirect()->back();
    }

    //Media Delete All Selected Images End


    $medies =Media::latest()->where('src_type',0)
    ->where(function($q) use ($r,$allPer) {

      // Check Permission
      if($allPer){
        $q->where('addedby_id',auth::id()); 
      }

    })
    ->select(['id','file_url','file_size','file_type','file_name','alt_text','caption','description','addedby_id'])
    ->paginate(50);

    if($r->ajax())
      {

          return Response()->json([
              'success' => true,
              'view' => View(adminTheme().'medies.includes.mediesAll',[
                  'medies'=>$medies
              ])->render()
          ]);
      }

    return view(adminTheme().'medies.medies',compact('medies'));
  }

  public function mediesCreate(Request $r){

      $check = $r->validate([
          'images.*' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf,docx,zip,rar,mp4,webm,mov,wmv,mp3|max:25600',
      ]);

      if(!$check){
          Session::flash('error','Need To validation');
          return back();
      }   

     $files=$r->file('images');
      if($files){
          foreach($files as $file){

              $file =$file;
              $src  =null;
              $srcType  =0;
              $fileUse  =0;
              $fileStatus=false;
              $author=Auth::id();
              uploadFile($file,$src,$srcType,$fileUse,$author,$fileStatus);
              
          }
      }

    Session()->flash('success','Your Are Successfully Done');
     return redirect()->back();
     
  }
    
    // Media Library single-file (progress bar) upload: same allowed types as mediesCreate, stored by uploadFile() in medies/
    public function mediesFileUpload(Request $request){
        $request->validate([
            'file' => 'required|file|mimes:jpeg,png,jpg,gif,svg,webp,pdf,docx,zip,rar,mp4,webm,mov,wmv,mp3|max:25600',
        ]);
        uploadFile($request->file('file'),null,0,0,Auth::id(),false);

        
        
        if($request->ajax()){
            return Response()->json([
                  'success' => true,
              ]);
        }
    }
   
    
  public function mediesEdit(Request $r, $id){
    $media =Media::find($id);
    if(!$media){
      Session()->flash('error','This File Are Not Found');
      return redirect()->back();
    }

    if($media->src_type==0){
      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['medies']['all']);
      if($allPer && $media->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.medies');
      }
    }

    if($r->isMethod('post')){
         $media->alt_text=$r->alt_text;
         $media->caption=$r->caption;
         $media->description=$r->description;
         $media->editedby_id=auth::id();
         $media->save();
         Session()->flash('success','Your Are Successfully Done');
         return redirect()->back();
    }

    return view(adminTheme().'medies.mediaImageEdit',compact('media'));
  }


  public function mediesDelete(Request $request,$id){

     if($request->ajax())
    {
   
    $media =Media::find($id);
    if(!$media){
      Session()->flash('error','This File Are Not Found');
     return Response()->json([
              'success' => false
          ]);
     }
     
    if(File::exists($media->file_url)){
          File::delete($media->file_url);
    }
    if(File::exists($media->file_url_sm)){
        File::delete($media->file_url_sm);
    }
    if(File::exists($media->file_url_md)){
        File::delete($media->file_url_md);
    }
    if(File::exists($media->file_url_lg)){
        File::delete($media->file_url_lg);
    }
    $media->delete();
      return Response()->json([
              'success' => true
          ]);
    }      

  }

  //Medias Library Route End

  // Page Management Function Start
    
  // Training Academy page courses (Admin → Training Courses)
  public function trainingCourses(Request $r){
    $page =templatePage('Training Academy');
    $courses =collect();
    $totals =(object)['total'=>0,'active'=>0,'inactive'=>0];

    if($page){
      $all =$page->trainingCourses()->where('status','<>','temp')->get();
      $totals =(object)[
        'total'=>$all->count(),
        'active'=>$all->where('status','active')->count(),
        'inactive'=>$all->where('status','inactive')->count(),
      ];
      $courses =$all->filter(function($course) use($r){
        return (!$r->status || $course->status==$r->status)
          && (!$r->category || $course->course('category')==$r->category)
          && (!$r->search || stripos($course->course('title'),$r->search)!==false);
      })->values();
    }

    return view(adminTheme().'trainingCourses.coursesAll',compact('page','courses','totals'));
  }

  public function trainingCoursesAction(Request $r,$action,$id=null){
    $page =templatePage('Training Academy');
    if(!$page){
      Session()->flash('error','No active page uses the Training Academy template.');
      return redirect()->route('admin.trainingCourses');
    }

    // Add Course: reuse this user's unfinished draft, or start a new one at the end of the list
    if($action=='create'){
      $course =$page->trainingCourses()->where('status','temp')->where('addedby_id',Auth::id())->first();
      if(!$course){
        $course =new PostExtra();
        $course->src_id=$page->id;
        $course->type=4;
        $course->status='temp';
        $course->drag=$page->trainingCourses()->max('drag')+1;
        $course->addedby_id=Auth::id();
        $course->save();
      }
      return redirect()->route('admin.trainingCoursesAction',['edit',$course->id]);
    }

    $course =$page->trainingCourses()->find($id);
    if(!$course){
      Session()->flash('error','This Course Is Not Found');
      return redirect()->route('admin.trainingCourses');
    }

    if($action=='update' && $r->isMethod('post')){
      $r->validate([
        'title' => 'required|max:191',
        'category' => 'required|in:'.implode(',',array_keys(trainingCategories())),
        'description' => 'nullable|max:1000',
        'duration' => 'nullable|max:50',
        'fee' => 'nullable|integer|min:0|max:100000000',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'image_url' => 'nullable|url|max:500',
        'alt' => 'nullable|max:191',
        'modules' => 'nullable|max:3000',
      ]);

      $data =$course->course();
      $image =$data['image'];
      if($r->hasFile('image')){
        $file =$r->file('image');
        $name =safeUploadName($file);
        $this->deleteCourseImage($image);
        $file->move(public_path('medies/courses'),$name);
        $image ='medies/courses/'.$name;
      }elseif($r->image_url && $r->image_url!=$image){
        $this->deleteCourseImage($image);
        $image =$r->image_url;
      }

      $course->name=$r->title;
      $course->content=json_encode([
        'category'=>$r->category,
        'title'=>$r->title,
        'description'=>$r->description,
        'duration'=>$r->duration,
        'fee'=>$r->fee!==null && $r->fee!=='' ? (int)$r->fee : null,
        'image'=>$image,
        'alt'=>$r->alt,
        'modules'=>array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',$r->modules ?? '')))),
      ],JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES);
      $course->status=$r->status?'active':'inactive';
      $course->editedby_id=Auth::id();
      $course->save();

      Session()->flash('success','Course Updated Successfully Done!');
      return redirect()->route('admin.trainingCoursesAction',['edit',$course->id]);
    }

    if($action=='delete'){
      $this->deleteCourseImage($course->course('image'));
      $course->delete();
      Session()->flash('success','Course Deleted Successfully Done!');
      return redirect()->route('admin.trainingCourses');
    }

    // Move a course one place up / down in the website order
    if($action=='up' || $action=='down'){
      $list =$page->trainingCourses()->where('status','<>','temp')->get()->values();
      $index =$list->search(function($item) use($course){ return $item->id==$course->id; });
      $swap =$action=='up' ? $index-1 : $index+1;
      if($index!==false && isset($list[$swap])){
        $items =$list->all();
        [$items[$index],$items[$swap]] =[$items[$swap],$items[$index]];
        foreach($items as $i=>$item){
          if($item->drag!=$i){
            $item->drag=$i;
            $item->save();
          }
        }
      }
      return redirect()->back();
    }

    $categories =trainingCategories();
    return view(adminTheme().'trainingCourses.courseEdit',compact('page','course','categories'));
  }

  // Course Inquiry form submissions (Admin → Training Courses → Course Inquiries)
  public function trainingInquiries(Request $r){

    // Bulk action on the selected inquiries
    if($r->isMethod('post')){
      $ids =array_filter((array)$r->checkid,'is_numeric');
      if($ids){
        $inquiries =PostExtra::where('type',5)->whereIn('id',$ids);
        if($r->action=='read'){
          $inquiries->update(['status'=>'read']);
          Session()->flash('success','Selected Inquiries Marked As Read!');
        }elseif($r->action=='unread'){
          $inquiries->update(['status'=>'unread']);
          Session()->flash('success','Selected Inquiries Marked As Unread!');
        }elseif($r->action=='delete'){
          $inquiries->delete();
          Session()->flash('success','Selected Inquiries Deleted Successfully Done!');
        }
      }else{
        Session()->flash('error','Please select at least one inquiry.');
      }
      return redirect()->back();
    }

    $inquiries =PostExtra::latest()->where('type',5)
      ->where(function($q) use($r){
        if($r->status=='unread' || $r->status=='read'){
          $q->where('status',$r->status);
        }
        if($r->course){
          $q->where('src_id',$r->course);
        }
        if($r->search){
          $q->where(function($qq) use($r){
            $qq->where('name','LIKE','%'.$r->search.'%')
              ->orWhere('email','LIKE','%'.$r->search.'%')
              ->orWhere('content','LIKE','%'.$r->search.'%');
          });
        }
      })
      ->paginate(25)->appends($r->only(['status','course','search']));

    $totals =PostExtra::where('type',5)->selectRaw("count(*) as total, sum(status='unread') as unread, sum(status='read') as `read`")->first();

    $page =templatePage('Training Academy');
    $courses =$page ? $page->trainingCourses()->where('status','<>','temp')->get() : collect();

    return view(adminTheme().'trainingCourses.inquiriesAll',compact('inquiries','totals','courses'));
  }

  public function trainingInquiriesAction(Request $r,$action,$id){
    $inquiry =PostExtra::where('type',5)->find($id);
    if(!$inquiry){
      Session()->flash('error','This Inquiry Is Not Found');
      return redirect()->route('admin.trainingInquiries');
    }

    if($action=='delete'){
      $inquiry->delete();
      Session()->flash('success','Inquiry Deleted Successfully Done!');
      return redirect()->route('admin.trainingInquiries');
    }

    if($action=='unread'){
      $inquiry->status='unread';
      $inquiry->save();
      Session()->flash('success','Inquiry Marked As Unread!');
      return redirect()->route('admin.trainingInquiries');
    }

    // View: opening an inquiry marks it as read
    if($inquiry->status!='read'){
      $inquiry->status='read';
      $inquiry->save();
    }
    $course =PostExtra::where('type',4)->find($inquiry->src_id);

    return view(adminTheme().'trainingCourses.inquiryView',compact('inquiry','course'));
  }

  // Remove an uploaded course image (external image links are left alone)
  private function deleteCourseImage($image){
    if($image && str_starts_with($image,'medies/courses/') && File::exists(public_path($image))){
      File::delete(public_path($image));
    }
  }

  // Industrial Solution page: texts + Industrial Automation Capabilities cards (Admin → Industrial Solution)
  public function industrialCapabilities(Request $r){
    return $this->solutionPageEditor($r,'Industrial Solution','admin.industrialCapabilities');
  }

  // Solar Solution page: texts + Solar Power Solutions cards (Admin → Solar Solution)
  public function solarSolution(Request $r){
    return $this->solutionPageEditor($r,'Solar Solution','admin.solarSolution');
  }

  // Services page: texts + service / support cards + workflow (Admin → Services Page)
  public function servicesPage(Request $r){
    return $this->solutionPageEditor($r,'Services','admin.servicesPage');
  }

  // Page key used in the card / chip / step URLs => template, editor route and card types
  private function solutionSections(){
    return [
      'industrial'=>['template'=>'Industrial Solution','route'=>'admin.industrialCapabilities','cards'=>3],
      'solar'=>['template'=>'Solar Solution','route'=>'admin.solarSolution','cards'=>3],
      'services'=>['template'=>'Services','route'=>'admin.servicesPage','cards'=>3],
      'services-support'=>['template'=>'Services','route'=>'admin.servicesPage','cards'=>12],
    ];
  }

  // Shared editor of the Industrial Solution / Solar Solution / Services design pages
  private function solutionPageEditor(Request $r,$template,$routeName){
    $page =templatePage($template);

    if($page && $r->isMethod('post')){
      $r->validate([
        'content.hero.*' => 'nullable|max:500',
        'content.capabilities.*' => 'nullable|max:500',
        'content.cta.*' => 'nullable|max:500',
        'content.support.overline' => 'nullable|max:100',
        'content.support.title' => 'nullable|max:191',
        'content.support.strip' => 'nullable|max:1000',
        'content.workflow.overline' => 'nullable|max:100',
        'content.workflow.title' => 'nullable|max:191',
      ]);

      // Section texts (hero, section headings, call to action). Hero chips and workflow steps have their own add / edit pages.
      $defaults =solutionDefaults($template);
      $saved =$page->sectionContent ? (json_decode($page->sectionContent->content ?: '[]',true) ?: []) : [];
      $input =$r->input('content',[]);
      $content =[];
      foreach(solutionTextGroups($template) as $group){
        foreach(array_keys($defaults[$group]) as $key){
          $content[$group][$key]=trim((string)($input[$group][$key] ?? ''));
        }
      }
      $content['workflow']=[
        'overline'=>trim((string)($input['workflow']['overline'] ?? '')),
        'title'=>trim((string)($input['workflow']['title'] ?? '')),
      ];
      if(isset($saved['workflow']['steps'])){
        $content['workflow']['steps']=$saved['workflow']['steps'];
      }
      if(isset($saved['features'])){
        $content['features']=$saved['features'];
      }
      $this->saveSectionContent($page,$template,$content);

      Session()->flash('success',$template.' Page Updated Successfully Done!');
      return redirect()->route($routeName);
    }

    $section =collect($this->solutionSections())->search(function($row) use($routeName){ return $row['route']==$routeName; });
    return view(adminTheme().'industrialCapabilities.industrialCapabilities',compact('page','template','routeName','section'));
  }

  // Store the JSON texts of an Industrial Solution / Solar Solution page
  private function saveSectionContent($page,$template,$content){
    $extra =$page->sectionContent ?: new PostExtra();
    $extra->src_id=$page->id;
    $extra->type=6;
    $extra->name=$template.' Content';
    $extra->content=json_encode($content,JSON_UNESCAPED_UNICODE);
    $extra->status='active';
    $extra->addedby_id=Auth::id();
    $extra->save();
    $page->unsetRelation('sectionContent');
  }

  // Hero feature chips ($list = features) / workflow steps ($list = steps) of the Industrial Solution / Solar Solution page:
  // list is on the page editor, add / edit on its own page. Items are addressed by their position in the list.
  public function solutionItemsAction(Request $r,$section,$list,$action,$index=null){
    $sections =$this->solutionSections();
    unset($sections['services-support']);
    if(!isset($sections[$section]) || !in_array($list,['features','steps']) || ($section=='services' && $list=='features')){
      abort(404);
    }
    $template =$sections[$section]['template'];
    $listRoute =$sections[$section]['route'];
    $page =templatePage($template);
    if(!$page){
      Session()->flash('error','No active page uses the '.$template.' template.');
      return redirect()->route($listRoute);
    }

    $saved =$page->sectionContent ? (json_decode($page->sectionContent->content ?: '[]',true) ?: []) : [];
    $current =solutionContent($page,$template);
    $items =array_values($list=='features' ? $current['features'] : $current['workflow']['steps']);
    $label =$list=='features' ? 'Chip' : 'Step';

    $store =function($items) use($page,$template,$saved,$list){
      if($list=='features'){
        $saved['features']=array_values($items);
      }else{
        $saved['workflow']=($saved['workflow'] ?? []);
        $saved['workflow']['steps']=array_values($items);
      }
      $this->saveSectionContent($page,$template,$saved);
    };

    $item =null;
    if($action!='create'){
      if(!is_numeric($index) || !isset($items[(int)$index])){
        Session()->flash('error',$label.' Are Not Found');
        return redirect()->route($listRoute);
      }
      $index =(int)$index;
      $item =$items[$index];
    }

    if($action=='delete'){
      array_splice($items,$index,1);
      $store($items);
      Session()->flash('success',$label.' Are Successfully Deleted!');
      return redirect()->route($listRoute);
    }

    if($action=='up' || $action=='down'){
      $swap =$action=='up' ? $index-1 : $index+1;
      if(isset($items[$swap])){
        [$items[$index],$items[$swap]] =[$items[$swap],$items[$index]];
        $store($items);
      }
      return redirect()->route($listRoute);
    }

    if(in_array($action,['create','edit']) && $r->isMethod('post')){
      $rules =['title' => 'required|max:100','text' => 'nullable|max:'.($list=='features' ? 191 : 500)];
      if($list=='features'){
        $rules['color']=['required','regex:/^#[0-9a-fA-F]{6}$/'];
      }
      $r->validate($rules);

      $row =['title'=>$r->title,'text'=>(string)$r->text];
      if($list=='features'){
        $row['color']=$r->color;
      }
      if($item){
        $items[$index]=$row;
      }else{
        $items[]=$row;
      }
      $store($items);

      Session()->flash('success',$label.' Are Successfully '.($item ? 'Updated' : 'Added').'!');
      return $r->save_new ? redirect()->route('admin.solutionItemsAction',[$section,$list,'create']) : redirect()->route($listRoute);
    }

    if(!in_array($action,['create','edit'])){
      abort(404);
    }
    $data =$item ?: ['title'=>null,'text'=>null,'color'=>'#287dcc'];
    $number =$item ? $index+1 : count($items)+1;
    return view(adminTheme().'industrialCapabilities.itemEdit',compact('page','template','section','list','listRoute','item','index','data','label','number'));
  }

  // Industrial Automation Capabilities / Solar Power Solutions cards: list is on the page editor, add / edit on its own page
  public function solutionCardsAction(Request $r,$section,$action,$id=null){
    $sections =$this->solutionSections();
    if(!isset($sections[$section])){
      abort(404);
    }
    $template =$sections[$section]['template'];
    $listRoute =$sections[$section]['route'];
    $cardType =$sections[$section]['cards'];
    $page =templatePage($template);
    if(!$page){
      Session()->flash('error','No active page uses the '.$template.' template.');
      return redirect()->route($listRoute);
    }

    $card =null;
    if($action!='create'){
      $card =PostExtra::where('type',$cardType)->where('src_id',$page->id)->find($id);
      if(!$card){
        Session()->flash('error','Card Are Not Found');
        return redirect()->route($listRoute);
      }
    }

    if($action=='delete'){
      $card->delete();
      Session()->flash('success','Card Are Successfully Deleted!');
      return redirect()->route($listRoute);
    }

    if($action=='up' || $action=='down'){
      $list =PostExtra::where('type',$cardType)->where('src_id',$page->id)->orderBy('drag')->get()->values();
      $index =$list->search(function($item) use($card){ return $item->id==$card->id; });
      $swap =$action=='up' ? $index-1 : $index+1;
      if($index!==false && isset($list[$swap])){
        $items =$list->all();
        [$items[$index],$items[$swap]] =[$items[$swap],$items[$index]];
        foreach($items as $i=>$item){
          if($item->drag!=$i){
            $item->drag=$i;
            $item->save();
          }
        }
      }
      return redirect()->route($listRoute);
    }

    if(in_array($action,['create','edit']) && $r->isMethod('post')){
      $withHome =$section=='services';
      $r->validate([
        'icon' => 'nullable|max:20',
        'title' => 'required|max:100',
        'description' => 'nullable|max:500',
        'color' => ['required','regex:/^#[0-9a-fA-F]{6}$/'],
      ] + ($withHome ? [
        'badge' => 'nullable|max:100',
        'home_description' => 'nullable|max:1000',
        'points' => 'nullable|max:1000',
        'link_page' => 'nullable|in:custom,'.implode(',',array_column(designTemplates(),'route')),
        'link_url' => 'nullable|max:500|required_if:link_page,custom',
        'link_text' => 'nullable|max:100',
        'home_color' => ['nullable','regex:/^#[0-9a-fA-F]{6}$/'],
        'home_order' => 'nullable|integer|min:0|max:999',
      ] : []));

      if(!$card){
        $card =new PostExtra();
        $card->src_id=$page->id;
        $card->type=$cardType;
        $card->drag=PostExtra::where('type',$cardType)->where('src_id',$page->id)->max('drag')+1;
        $card->status='active';
        $card->addedby_id=Auth::id();
      }
      $card->name=$r->title;
      $data =[
        'icon'=>$r->icon,
        'title'=>$r->title,
        'description'=>$r->description,
        'color'=>$r->color,
      ];
      if($withHome){
        $data +=[
          'show_services'=>$r->show_services ? true : false,
          'show_home'=>$r->show_home ? true : false,
          'badge'=>$r->badge,
          'home_description'=>$r->home_description,
          'points'=>array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n/',(string)$r->points)))),
          'link_page'=>$r->link_page ?: null,
          'link_url'=>$r->link_page=='custom' ? $r->link_url : null,
          'link_text'=>$r->link_text,
          'home_color'=>$r->home_color,
          'home_order'=>(int)$r->home_order,
        ];
      }
      $card->content=json_encode($data,JSON_UNESCAPED_UNICODE);
      $card->save();

      Session()->flash('success','Card Are Successfully '.($action=='create' ? 'Added' : 'Updated').'!');
      return $r->save_new ? redirect()->route('admin.solutionCardsAction',[$section,'create']) : redirect()->route($listRoute);
    }

    if(!in_array($action,['create','edit'])){
      abort(404);
    }
    $data =$card ? $card->card() : ['icon'=>null,'title'=>null,'description'=>null,'color'=>'#287dcc'];
    $pageContent =solutionContent($page,$template);
    $sectionTitle =$cardType==12 ? $pageContent['support']['title'] : $pageContent['capabilities']['title'];
    return view(adminTheme().'industrialCapabilities.cardEdit',compact('page','template','section','listRoute','card','data','sectionTitle'));
  }

  // Home page "Why Choose Us" section: heading texts + item list (Admin → Home: Why Choose Us)
  public function homeWhy(Request $r){
    if($r->isMethod('post')){
      $r->validate([
        'eyebrow' => 'nullable|max:100',
        'title' => 'nullable|max:191',
        'subtitle' => 'nullable|max:500',
      ]);
      $content =homeWhyContent();
      $content['eyebrow']=trim((string)$r->eyebrow);
      $content['title']=trim((string)$r->title);
      $content['subtitle']=trim((string)$r->subtitle);
      $this->saveHomeWhy($content);

      Session()->flash('success','Why Choose Us Section Updated Successfully Done!');
      return redirect()->route('admin.homeWhy');
    }

    $content =homeWhyContent();
    return view(adminTheme().'homeWhy.homeWhy',compact('content'));
  }

  // Add / edit / delete / move one "Why Choose Us" item (addressed by its position in the list)
  public function homeWhyAction(Request $r,$action,$index=null){
    $content =homeWhyContent();
    $items =$content['items'];

    $item =null;
    if($action!='create'){
      if(!is_numeric($index) || !isset($items[(int)$index])){
        Session()->flash('error','Item Are Not Found');
        return redirect()->route('admin.homeWhy');
      }
      $index =(int)$index;
      $item =$items[$index];
    }

    if($action=='delete'){
      array_splice($items,$index,1);
      $content['items']=$items;
      $this->saveHomeWhy($content);
      Session()->flash('success','Item Are Successfully Deleted!');
      return redirect()->route('admin.homeWhy');
    }

    if($action=='up' || $action=='down'){
      $swap =$action=='up' ? $index-1 : $index+1;
      if(isset($items[$swap])){
        [$items[$index],$items[$swap]] =[$items[$swap],$items[$index]];
        $content['items']=$items;
        $this->saveHomeWhy($content);
      }
      return redirect()->route('admin.homeWhy');
    }

    if(!in_array($action,['create','edit'])){
      abort(404);
    }

    if($r->isMethod('post')){
      $r->validate([
        'title' => 'required|max:100',
        'text' => 'nullable|max:500',
        'color' => ['required','regex:/^#[0-9a-fA-F]{6}$/'],
        'color2' => ['required','regex:/^#[0-9a-fA-F]{6}$/'],
      ]);
      $row =['title'=>$r->title,'text'=>(string)$r->text,'color'=>$r->color,'color2'=>$r->color2];
      if($item){
        $items[$index]=$row;
      }else{
        $items[]=$row;
      }
      $content['items']=$items;
      $this->saveHomeWhy($content);

      Session()->flash('success','Item Are Successfully '.($item ? 'Updated' : 'Added').'!');
      return $r->save_new ? redirect()->route('admin.homeWhyAction','create') : redirect()->route('admin.homeWhy');
    }

    $data =$item ?: ['title'=>null,'text'=>null,'color'=>'#2684d9','color2'=>'#8b28a4'];
    $number =$item ? $index+1 : count($items)+1;
    return view(adminTheme().'homeWhy.homeWhyEdit',compact('item','index','data','number'));
  }

  // Website Translations: Bangla for texts added from the CMS (the website language switch uses them)
  public function translations(Request $r){
    if($r->isMethod('post')){
      $r->validate(['bn' => 'nullable|array', 'bn.*' => 'nullable|max:2000']);
      foreach((array)$r->bn as $id=>$bn){
        if($row =PostExtra::where('type',14)->find($id)){
          $data =json_decode($row->content ?: '[]',true) ?: [];
          $data['bn']=trim((string)$bn);
          $row->content=json_encode($data,JSON_UNESCAPED_UNICODE);
          $row->status=$data['bn']!=='' ? 'active' : 'pending';
          $row->editedby_id=Auth::id();
          $row->save();
        }
      }
      Session()->flash('success','Translations Saved Successfully!');
      return redirect()->back();
    }

    $filter =in_array($r->filter,['pending','done','all']) ? $r->filter : 'pending';
    $rows =PostExtra::where('type',14)
      ->when($filter=='pending',fn($q)=>$q->where('status','pending'))
      ->when($filter=='done',fn($q)=>$q->where('status','active'))
      ->when($r->search,fn($q)=>$q->where('content','LIKE','%'.$r->search.'%'))
      ->latest('id')->paginate(50)->appends($r->only(['filter','search']));
    $counts =PostExtra::where('type',14)->selectRaw("count(*) as total, sum(status='pending') as pending")->first();

    $scanPages =collect(['index','about','services','solar','training','industrial','contact'])
      ->map(fn($name)=>pageLink($name))->unique()->values();

    return view(adminTheme().'translations.translations',compact('rows','filter','counts','scanPages'));
  }

  // Add one text by hand, delete a text, or store the texts found by "Scan Website"
  public function translationsAction(Request $r,$action,$id=null){
    if($action=='add' && $r->isMethod('post')){
      $r->validate(['en' => 'required|max:1000', 'bn' => 'nullable|max:2000']);
      $row =$this->translationRow($r->en);
      $data =json_decode($row->content ?: '[]',true) ?: [];
      if(trim((string)$r->bn)!==''){
        $data['bn']=trim($r->bn);
      }
      $row->content=json_encode($data,JSON_UNESCAPED_UNICODE);
      $row->status=($data['bn'] ?? '')!=='' ? 'active' : 'pending';
      $row->save();
      Session()->flash('success','Text Added Successfully!');
      return redirect()->route('admin.translations',['filter'=>'all']);
    }

    if($action=='delete' && $row =PostExtra::where('type',14)->find($id)){
      $row->delete();
      Session()->flash('success','Text Deleted Successfully!');
      return redirect()->back();
    }

    // Texts found on the website pages (sent by the scan) + training course details shown in the Details popup
    if($action=='scan' && $r->isMethod('post')){
      $r->validate(['texts' => 'nullable|array|max:3000', 'texts.*' => 'nullable|string|max:1000']);
      $texts =(array)$r->texts;
      foreach(trainingCourses() as $course){
        $texts[]=$course->course('title');
        $texts[]=$course->course('description');
        $texts[]=$course->course('alt');
        foreach((array)$course->course('modules') as $module){
          $texts[]=$module;
        }
      }
      // Already known: built-in dictionary of the language switch or a stored text
      $known =array_flip(array_map(fn($k)=>trim(preg_replace('/\s+/u',' ',$k)),array_keys(builtinTranslations())));
      foreach(PostExtra::where('type',14)->get(['content']) as $row){
        $known[json_decode($row->content ?: '[]',true)['en'] ?? '']=true;
      }
      $added =0;
      foreach($texts as $text){
        $text =trim(preg_replace('/\s+/u',' ',(string)$text));
        if($text==='' || mb_strlen($text)>1000 || !preg_match('/[A-Za-z]{2,}/',$text) || isset($known[$text])){
          continue;
        }
        $this->translationRow($text)->save();
        $known[$text]=true;
        $added++;
      }
      return response()->json(['success'=>true,'added'=>$added]);
    }

    abort(404);
  }

  // The translation row of an English text (new, unsaved row when it is not stored yet)
  private function translationRow($en){
    $en =trim(preg_replace('/\s+/u',' ',(string)$en));
    $row =PostExtra::where('type',14)->get()->first(function($row) use($en){
      return (json_decode($row->content ?: '[]',true)['en'] ?? null)===$en;
    });
    if(!$row){
      $row =new PostExtra();
      $row->type=14;
      $row->src_id=0;
      $row->name=Str::limit($en,180);
      $row->content=json_encode(['en'=>$en,'bn'=>''],JSON_UNESCAPED_UNICODE);
      $row->status='pending';
      $row->addedby_id=Auth::id();
    }
    return $row;
  }

  private function saveHomeWhy($content){
    $extra =homeWhyRow() ?: new PostExtra();
    $extra->src_id=0;
    $extra->type=13;
    $extra->name='Home Why Choose Us';
    $extra->content=json_encode($content,JSON_UNESCAPED_UNICODE);
    $extra->status='active';
    $extra->addedby_id=Auth::id();
    $extra->save();
  }

  // Training Academy page texts: hero, course chips, catalog heading (Admin → Training Courses → Page Content)
  public function trainingPage(Request $r){
    $page =templatePage('Training Academy');

    if($page && $r->isMethod('post')){
      $r->validate([
        'content.hero.*' => 'nullable|max:1000',
        'content.catalog.*' => 'nullable|max:500',
        'chips.*.title' => 'nullable|max:100',
        'chips.*.text' => 'nullable|max:191',
      ]);

      $defaults =trainingDefaults();
      $input =$r->input('content',[]);
      $content =[];
      foreach(['hero','catalog'] as $group){
        foreach(array_keys($defaults[$group]) as $key){
          $content[$group][$key]=trim((string)($input[$group][$key] ?? ''));
        }
      }
      $content['chips']=[];
      foreach(array_values($r->chips ?: []) as $row){
        if(!empty($row['title'])){
          $content['chips'][]=['title'=>$row['title'],'text'=>$row['text'] ?? ''];
        }
      }

      $extra =$page->trainingContent ?: new PostExtra();
      $extra->src_id=$page->id;
      $extra->type=7;
      $extra->name='Training Academy Content';
      $extra->content=json_encode($content,JSON_UNESCAPED_UNICODE);
      $extra->status='active';
      $extra->addedby_id=Auth::id();
      $extra->save();

      Session()->flash('success','Training Page Content Updated Successfully Done!');
      return redirect()->route('admin.trainingPage');
    }

    return view(adminTheme().'trainingCourses.trainingPage',compact('page'));
  }

  // Contact Us page address cards (Admin → Contact Locations)
  public function contactLocations(Request $r){
    $page =templatePage('Contact Us');

    if($page && $r->isMethod('post')){
      $r->validate([
        'locations.*.label' => 'nullable|max:100',
        'locations.*.title' => 'nullable|max:191',
        'locations.*.address' => 'nullable|max:500',
        'locations.*.email' => 'nullable|email|max:191',
        'locations.*.email_note' => 'nullable|max:100',
        'locations.*.map' => 'nullable|max:500',
      ]);

      $page->contactLocations()->delete();
      foreach(array_values($r->locations ?: []) as $i=>$row){
        if(empty($row['title']) && empty($row['address'])){
          continue;
        }
        $phones =array_values(array_filter(array_map('trim',preg_split('/\r\n|\r|\n|,/',$row['phones'] ?? ''))));

        $location =new PostExtra();
        $location->src_id=$page->id;
        $location->type=2;
        $location->name=$row['label'] ?? null;
        $location->content=json_encode([
          'title'=>$row['title'] ?? null,
          'address'=>$row['address'] ?? null,
          'phones'=>$phones,
          'email'=>$row['email'] ?? null,
          'email_note'=>$row['email_note'] ?? null,
          'map'=>$row['map'] ?? null,
        ],JSON_UNESCAPED_UNICODE);
        $location->drag=$i;
        $location->status='active';
        $location->addedby_id=Auth::id();
        $location->save();
      }

      Session()->flash('success','Contact Locations Updated Successfully Done!');
      return redirect()->route('admin.contactLocations');
    }

    return view(adminTheme().'contactLocations.contactLocations',compact('page'));
  }

  public function pages(Request $r){

    $allPer = empty(json_decode(Auth::user()->permission->permission, true)['pages']['all']);
      // Filter Action Start

    if($r->action){
      if($r->checkid){

      $datas=Post::latest()->where('type',0)->whereIn('id',$r->checkid)->get();

      foreach($datas as $data){
        if($allPer && $data->addedby_id!=Auth::id()){
          // You are unauthorized Try!!
        }else{

          if($r->action==1){
            $data->status='active';
            $data->save();
          }elseif($r->action==2){
            $data->status='inactive';
            $data->save();
          }elseif($r->action==3){
            $data->fetured=true;
            $data->save();
          }elseif($r->action==4){
            $data->fetured=false;
            $data->save();
          }elseif($r->action==5){
            //Page Extra Data Delete
            PostExtra::where('type',0)->where('src_id',$data->id)->delete();
            
            //Page Media File Delete
            $medias =Media::latest()->where('src_type',1)->where('src_id',$data->id)->get();
            foreach($medias as $media){
              if(File::exists($media->file_url)){
                File::delete($media->file_url);
              }
              $media->delete();
            }

            $data->delete();

          }

        }


      }

      Session()->flash('success','Action Successfully Completed!');

      }else{
        Session()->flash('info','Please Need To Select Minimum One Post');
      }

      return redirect()->back();
    }

    //Filter Action End

    $pages=Post::latest()->where('type',0)->where('status','<>','temp')
    ->where(function($q) use ($r,$allPer) {

        if($r->search){
            $q->where('name','LIKE','%'.$r->search.'%');
        }
        
        if($r->startDate || $r->endDate)
        {
            if($r->startDate){
                $from =$r->startDate;
            }else{
                $from=Carbon::now()->format('Y-m-d');
            }

            if($r->endDate){
                $to =$r->endDate;
            }else{
                $to=Carbon::now()->format('Y-m-d');
            }

            $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);

        }

        if($r->status){
           $q->where('status',$r->status); 
        }

      // Check Permission
      if($allPer){
       $q->where('addedby_id',auth::id()); 
      }

    })
    ->select(['id','name','slug','view','type','template','created_at','addedby_id','status','fetured'])
    ->paginate(25)->appends([
      'search'=>$r->search,
      'status'=>$r->status,
      'startDate'=>$r->startDate,
      'endDate'=>$r->endDate,
    ]);

    //Total Count Results
    $totals = DB::table('posts')->where('status','<>','temp')
    ->where('type',0)
    ->selectRaw('count(*) as total')
    ->selectRaw("count(case when status = 'active' then 1 end) as active")
    ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
    ->first();

    return view(adminTheme().'pages.pagesAll',compact('pages','totals'));

  }

  public function pagesAction(Request $r,$action,$id=null){

    if($action=='create'){
        $page =Post::where('type',0)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$page){
          $page =new Post();
          $page->type =0;
          $page->status ='temp';
          $page->addedby_id =Auth::id();
        }
        $page->created_at =Carbon::now();
        $page->save();
  
        return redirect()->route('admin.pagesAction',['edit',$page->id]);
      }
      $page =Post::find($id);
      if(!$page){
        Session()->flash('error','This Page Are Not Found');
        return redirect()->route('admin.pages');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['pages']['all']);
      if($allPer && $page->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.pages');
      }

      if($action=='update' && $r->isMethod('post')){

        $check = $r->validate([
            'name' => 'required|max:191',
            'template' => 'nullable|max:100',
            'seo_title' => 'nullable|max:120',
            'seo_description' => 'nullable|max:200',
            'seo_keyword' => 'nullable|max:300',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
        ]);

        $page->name=$r->name;
        $page->short_description=$r->short_description;
        $page->description=$r->description;
        $page->seo_title=$r->seo_title;
        $page->seo_description=$r->seo_description;
        $page->seo_keyword=$r->seo_keyword;
        $page->template=$r->template?:null;
        ///////Image Upload Start////////////
        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$page->id;
          $srcType  =1;
          $fileUse  =1;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        ///////Image Upload End////////////

        ///////Image Upload Start////////////
        if($r->hasFile('banner')){
          $file =$r->banner;
          $src  =$page->id;
          $srcType  =1;
          $fileUse  =2;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        ///////Image Upload End////////////

        $slug =Str::slug($r->name);
        if($slug==null){
          $page->slug=$page->id;
        }else{
          if(Post::where('type',0)->where('slug',$slug)->whereNotIn('id',[$page->id])->count() >0){
          $page->slug=$slug.'-'.$page->id;
          }else{
          $page->slug=$slug;
          }
        }

        if($r->created_at){
          $page->created_at =$r->created_at;
        }
        $page->status =$r->status?'active':'inactive';
        $page->fetured =$r->fetured?1:0;
        $page->editedby_id =Auth::id();
        $page->save();
        
        //Gallery posts
        if($r->galleries){

          $page->postTags()->whereNotIn('reff_id',$r->galleries)->delete();

          for ($i=0; $i < count($r->galleries); $i++) {
            $tag = $page->postTags()->where('reff_id',$r->galleries[$i])->first();

              if($tag){}else{
              $tag =new PostAttribute();
              $tag->type=2;
              $tag->src_id=$page->id;
              $tag->reff_id=$r->galleries[$i];
              }
              $tag->save();
         }
       }else{
        $page->postTags()->delete();
       }
        
        
        Session()->flash('success','Your Are Successfully Done');
        return redirect()->back();

      }

      if($action=='delete'){
        
        //Page Extra Data Delete
        PostExtra::where('type',0)->where('src_id',$page->id)->delete();

        //Page Media File Delete
        $medies =Media::where('src_type',1)->where('src_id',$page->id)->get();
        foreach ($medies as  $media) {
            if(File::exists($media->file_url)){
                File::delete($media->file_url);
            }
            $media->delete();
        }

        //Page Delete
        $page->delete();
        Session()->flash('success','Your Are Successfully Done');
        return redirect()->back();

      }

      $extraDatas=PostExtra::where('src_id',$id)->get();
      
      $galleries=Attribute::latest()->where('type',4)->where('status','<>','temp')->where('parent_id',null)
      ->select(['id','name'])
      ->get();

      return view(adminTheme().'pages.pageEdit',compact('page','extraDatas','galleries'));
  }


// Page Management Function End

// Services Management Function

    public function services(Request $r){

      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['services']['all']);
      // Filter Action Start

      if($r->action){
        if($r->checkid){

        $datas=Post::latest()->where('type',3)->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){

          if($allPer && $data->addedby_id!=Auth::id()){
            // You are unauthorized Try!!
          }else{

            if($r->action==1){
              $data->status='active';
              $data->save();
            }elseif($r->action==2){
              $data->status='inactive';
              $data->save();
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){
              
              $medias =Media::latest()->where('src_type',1)->where('src_id',$data->id)->get();
              
              foreach($medias as $media){
                if(File::exists($media->file_url)){
                  File::delete($media->file_url);
                }
                $media->delete();
              }

              $data->serviceCtgs()->delete();
              $data->postTags()->delete();
              $data->delete();
            }

          }

        }

        Session()->flash('success','Action Successfully Completed!');

        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }

        return redirect()->back();
      }

      //Filter Action End

      $services =Post::latest()->where('type',3)->where('status','<>','temp')
        ->where(function($q) use ($r,$allPer) {

            if($r->search){
                $q->where('search_key','LIKE','%'.$r->search.'%');
            }
            
            if($r->startDate || $r->endDate)
            {
                if($r->startDate){
                    $from =$r->startDate;
                }else{
                    $from=Carbon::now()->format('Y-m-d');
                }

                if($r->endDate){
                    $to =$r->endDate;
                }else{
                    $to=Carbon::now()->format('Y-m-d');
                }

                $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
            }

            if($r->status){
             $q->where('status',$r->status); 
            }

            // Check Permission
            if($allPer){
             $q->where('addedby_id',auth::id()); 
            }


        })
        ->select(['id','name','slug','view','type','created_at','addedby_id','status','fetured'])
        ->paginate(25)->appends([
          'search'=>$r->search,
          'status'=>$r->status,
          'startDate'=>$r->startDate,
          'endDate'=>$r->endDate,
        ]);

        //Total Count Results
        $totals = DB::table('posts')
        ->where('type',3)->where('status','<>','temp')
        ->selectRaw('count(*) as total')
        ->selectRaw("count(case when status = 'active' then 1 end) as active")
        ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
        ->first();

        return view(adminTheme().'services.servicesAll',compact('services','totals'));
    }
    
    public function servicesAction(Request $r,$action,$id=null){
        
        //Add Service  Start
        if($action=='create'){
    
          $service =Post::where('type',3)->where('status','temp')->where('addedby_id',Auth::id())->first();
          if(!$service){
            $service =new Post();
            $service->type =3;
            $service->status ='temp';
            $service->addedby_id =Auth::id();
          }
          $service->created_at =Carbon::now();
          $service->save();
  
          return redirect()->route('admin.servicesAction',['edit',$service->id]);
        }
        //Add Service  End
        
        $service =Post::where('type',3)->find($id);
        if(!$service){
        Session()->flash('error','This Service Are Not Found');
        return redirect()->route('admin.services');
        }
        
        //Check Authorized User
        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['services']['all']);
        if($allPer && $service->addedby_id!=Auth::id()){
          Session()->flash('error','You are unauthorized Try!!');
          return redirect()->route('admin.services');
        }
        
        //Update Service  Start
        if($action=='update'){
            
            $check = $r->validate([
                'name' => 'required|max:191',
                'seo_title' => 'nullable|max:200',
                'seo_description' => 'nullable|max:250',
                'catagoryid.*' => 'nullable|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'gallery_image.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            if(!$check){
                Session::flash('error','Need To validation');
                return back();
            }
            
            $service->name=$r->name;
            $service->short_description=$r->short_description;
            $service->description=$r->description;
            $service->seo_title=$r->seo_title;
            $service->seo_description=$r->seo_description;
            $service->seo_keyword=$r->seo_keyword;
            
            ///////Image Upload End////////////
            if($r->hasFile('image')){
              $file =$r->image;
              $src  =$service->id;
              $srcType  =1;
              $fileUse  =1;
              uploadFile($file,$src,$srcType,$fileUse);
            }
            ///////Image Upload End////////////
    
            ///////Gallery Upload End////////////
    
            $files=$r->file('gallery_image');
            if($files){
    
                foreach($files as $file)
                {
                    
                    $file =$file;
                    $src  =$service->id;
                    $srcType  =1;
                    $fileUse  =3;
                    $author=Auth::id();
                    $fileStatus=false;
                    uploadFile($file,$src,$srcType,$fileUse,$author,$fileStatus);
                }
            }
    
            ///////Gallery Upload End////////////
    
            $slug =Str::slug($r->name);
            if($slug==null){
            $service->slug=$service->id;
            }else{
            if(Post::where('type',3)->where('slug',$slug)->whereNotIn('id',[$service->id])->count() >0){
            $service->slug=$slug.'-'.$service->id;
            }else{
            $service->slug=$slug;
            }
            }
            if($r->created_at){
              $service->created_at =$r->created_at;
            }
            $service->status =$r->status?'active':'inactive';
            $service->fetured =$r->fetured?1:0;
            $service->editedby_id =Auth::id();
            $service->save();
            
            //Category posts
            if($r->categoryid){
            
            $service->serviceCtgs()->whereNotIn('reff_id',$r->categoryid)->delete();
            
            for ($i=0; $i < count($r->categoryid); $i++) {
            
            $ctg = $service->serviceCtgs()->where('reff_id',$r->categoryid[$i])->first();
            
            if($ctg){}else{
            $ctg =new PostAttribute();
            $ctg->src_id=$service->id;
            $ctg->reff_id=$r->categoryid[$i];
            $ctg->type=0;
            }
            $ctg->drag=$i;
            $ctg->save();
            }
            
            }else{
            $service->serviceCtgs()->delete();
            }
           
           ///////Search Key Start////////
            $key='';
            $key.=$service->name;
            //Post Category Name
            foreach($service->serviceCtgs as $postctg){
                if($ctg=Attribute::where('type',0)->find($postctg->catagory_id)){
                    $key.=' '.$ctg->name;
                }
            }
            $service->search_key=Str::limit($key,450);
            
            $service->save();
            ///////Search Key End////////
    
            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();
              
        }
        //Update Service  End
        
        
        //Delete Service  Start
        if($action=='delete'){
            
            $medias =Media::latest()->where('src_type',1)->where('src_id',$service->id)->get();
            foreach($medias as $media){
              if(File::exists($media->file_url)){
                File::delete($media->file_url);
              }
              $media->delete();
            }
            
            $service->serviceCtgs()->delete();
            $service->postTags()->delete();
            $service->delete();
            
            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();
            
        }
        //Delete Service  End
        
        $categories =Attribute::where('type',0)->where('status','<>','temp')->where('parent_id',null)->get();
        $tags =Attribute::where('type',9)->where('status','<>','temp')->where('parent_id',null)->get();
        $brands =Attribute::where('type',2)->where('status','<>','temp')->where('parent_id',null)->get();
      
        return view(adminTheme().'services.servicesEdit',compact('service','categories','tags','brands'));
        
        
    }

  // Services Management Function End


  //Service Category Function
  public function servicesCategories(Request $r){

    $allPer = empty(json_decode(Auth::user()->permission->permission, true)['servicesCtg']['all']);
    // Filter Action Start

      if($r->action){
        if($r->checkid){

        $datas=Attribute::where('type',0)->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){

          if($allPer && $data->addedby_id!=Auth::id()){
            // You are unauthorized Try!!
          }else{

            if($r->action==1){
              $data->status='active';
              $data->save();
            }elseif($r->action==2){
              $data->status='inactive';
              $data->save();
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){
              
              $medias =Media::latest()->where('src_type',3)->where('src_id',$data->id)->get();
              foreach($medias as $media){
                if(File::exists($media->file_url)){
                  File::delete($media->file_url);
                }
                $media->delete();
              }

              //Post Category sub Category replace
              foreach($data->subctgs as $subctg){
                $subctg->parent_id=$data->parent_id;
                $subctg->save();
              }
              
              $data->delete();
            }

          }


        }

        Session()->flash('success','Action Successfully Completed!');

        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }

        return redirect()->back();
      }

    //Filter Action End

    $categories =Attribute::latest()->where('type',0)->where('status','<>','temp')
    ->where(function($q) use ($r,$allPer) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }

          if($r->status){
             $q->where('status',$r->status); 
          }

         // Check Permission
          if($allPer){
           $q->where('addedby_id',auth::id()); 
          }

    })
    ->select(['id','name','slug','parent_id','view','type','created_at','addedby_id','status','fetured'])
        ->paginate(25)->appends([
          'search'=>$r->search,
          'status'=>$r->status,
        ]);

    //Total Count Results
    $totals = DB::table('attributes')
    ->where('type',0)->where('status','<>','temp')
    ->selectRaw('count(*) as total')
    ->selectRaw("count(case when status = 'active' then 1 end) as active")
    ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
    ->first();

      
    return view(adminTheme().'services.category.servicesCategories',compact('categories','totals'));

    }
    
    public function servicesCategoriesAction(Request $r,$action,$id=null){
        
        //Add Service Category  Start
        if($action=='create'){
            
            $category =new Attribute();
            $category->type =0;
            $category->status ='temp';
            $category->addedby_id =Auth::id();
            $category->save();

            return redirect()->route('admin.servicesCategoriesAction',['edit',$category->id]);
            
        }
        //Add Service Category  End
        
        $category =Attribute::where('type',0)->find($id);
        if(!$category){
        Session()->flash('error','This Category Are Not Found');
        return redirect()->route('admin.servicesCategories');
        }
        
        //Check Authorized User
        $allPer = empty(json_decode(Auth::user()->permission->permission, true)['servicesCtg']['all']);
        if($allPer && $category->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.servicesCategories');
        }
        
        //Update Service Category  Start
        if($action=='update'){
            
            $check = $r->validate([
                'name' => 'required|max:191',
                'seo_title' => 'nullable|max:200',
                'seo_desc' => 'nullable|max:200',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
                'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
            if(!$check){
                Session::flash('error','Need To validation');
                return back();
            }
    
            $category->name=$r->name;
            $category->description=$r->description;
            $category->seo_title=$r->seo_title;
            $category->seo_description=$r->seo_description;
            $category->seo_keyword=$r->seo_keyword;
            if($r->parent_id==$category->parent_id){}else{
              $category->parent_id=$r->parent_id;
             }
    
            ///////Image UploadStart////////////
            if($r->hasFile('image')){
                $file =$r->image;
                $src  =$category->id;
                $srcType  =3;
                $fileUse  =1;
                uploadFile($file,$src,$srcType,$fileUse);
            }
          
            ///////Image Upload End////////////
    
            ///////Banner Upload End////////////
    
            if($r->hasFile('banner')){
                $file =$r->banner;
                $src  =$category->id;
                $srcType  =3;
                $fileUse  =2;
                uploadFile($file,$src,$srcType,$fileUse);
            }
    
            ///////Banner Upload End////////////
    
    
           $slug =Str::slug($r->name);
           if($slug==null){
            $category->slug=$category->id;
           }else{
            if(Attribute::where('type',0)->where('slug',$slug)->whereNotIn('id',[$category->id])->count() >0){
            $category->slug=$slug.'-'.$category->id;
            }else{
            $category->slug=$slug;
            }
           }
          $category->status =$r->status?'active':'inactive';
          $category->fetured =$r->fetured?1:0;
          $category->editedby_id =Auth::id();
          $category->save();
          
          Session()->flash('success','Your Are Successfully Done');
          return redirect()->back();

        }
        //Update Service Category  End
        
        //Delete Service Category  Start
        if($action=='delete'){
            //Category Media File Delete
            $medias =Media::latest()->where('src_type',3)->where('src_id',$category->id)->get();
            foreach($medias as $media){
              if(File::exists($media->file_url)){
                File::delete($media->file_url);
              }
              $media->delete();
            }
    
            //Service Category sub Category replace
            foreach($category->subctgs as $subctg){
              $subctg->parent_id=$category->parent_id;
              $subctg->save();
            }
            
            $category->delete();
    
           Session()->flash('success','Your Are Successfully Done');
           return redirect()->back();
        }
        //Delete Service Category  End
        
        $parents =Attribute::where('type',0)->where('status','<>','temp')->where('parent_id',null)->get();
        return view(adminTheme().'services.category.servicesCategoryEdit',compact('category','parents'));
        
    }

    
    //Service Category Function End



    //Clients Function

    public function clients(Request $r){
      
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['clients']['all']);
      
      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=Attribute::latest()->where('type',3)->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){
          if($allPer && $data->addedby_id!=Auth::id()){
            // You are unauthorized Try!!
          }else{

            if($r->action==1){
              $data->status='active';
              $data->save();
            }elseif($r->action==2){
              $data->status='inactive';
              $data->save();
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){
              
              $medias =Media::latest()->where('src_type',3)->where('src_id',$data->id)->get();
              foreach($medias as $media){
                if(File::exists($media->file_url)){
                  File::delete($media->file_url);
                }
                $media->delete();
              }

              $data->delete();
            }

          }


        }

        Session()->flash('success','Action Successfully Completed!');

        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }

        return redirect()->back();
      }

      //Filter Action End

      $clients=Attribute::latest()->where('type',3)->where('status','<>','temp')
        ->where(function($q) use ($r,$allPer) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }

          if($r->status){
             $q->where('status',$r->status); 
          }

          // Check Permission
          if($allPer){
           $q->where('addedby_id',auth::id()); 
          }

      })
      ->select(['id','name','slug','type','created_at','addedby_id','status','fetured'])
      ->paginate(25)->appends([
        'search'=>$r->search,
        'status'=>$r->status,
      ]);

      //Total Count Results
      $totals = DB::table('attributes')
      ->where('type',3)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'clients.clientsAll',compact('clients','totals'));
    }

    public function clientsAction(Request $r,$action,$id=null){
      // Add Client Action Start
      if($action=='create'){

        $client =Attribute::where('type',3)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$client){
          $client =new Attribute();
        }
        $client->type =3;
        $client->status ='temp';
        $client->addedby_id =Auth::id();
        $client->save();

        return redirect()->route('admin.clientsAction',['edit',$client->id]);

      } 

      // Add Client Action End
      
      
      $client =Attribute::where('type',3)->find($id);
      if(!$client){
        Session()->flash('error','This Client Are Not Found');
        return redirect()->route('admin.clients');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['clients']['all']);
      if($allPer && $client->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.clients');
      }

      // Update Client Action Start
      if($action=='update'){
          $check = $r->validate([
            'name' => 'required|max:191',
            'seo_title' => 'nullable|max:200',
            'seo_desc' => 'nullable|max:250',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $client->name=$r->name;
        $client->short_description=$r->short_description;
        $client->description=$r->description;
        $client->seo_title=$r->seo_title;
        $client->short_description=$r->short_description;
        $client->seo_keyword=$r->seo_keyword;

        ///////Image UploadStart////////////
      
        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$client->id;
          $srcType  =3;
          $fileUse  =1;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        
        ///////Image Upload End////////////

        ///////Banner Upload End////////////

        if($r->hasFile('banner')){

          $file =$r->banner;
          $src  =$client->id;
          $srcType  =3;
          $fileUse  =2;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);

        }

        ///////Banner Upload End////////////

        $slug =Str::slug($r->name);
         if($slug==null){
          $client->slug=$client->id;
         }else{
          if(Attribute::where('type',3)->where('slug',$slug)->whereNotIn('id',[$client->id])->count() >0){
          $client->slug=$slug.'-'.$client->id;
          }else{
          $client->slug=$slug;
          }
        }

        $client->status =$r->status?'active':'inactive';
        $client->fetured =$r->fetured?1:0;
        $client->editedby_id =Auth::id();
        $client->save();

        Session()->flash('success','Your Are Successfully Updated');
        return redirect()->back();

      }

      // Update Client Action End


      // Delete Client Action Start
      if($action=='delete'){
        $medias =Media::latest()->where('src_type',3)->where('src_id',$client->id)->get();
        foreach($medias as $media){
          if(File::exists($media->file_url)){
            File::delete($media->file_url);
          }
          $media->delete();
        }

        $client->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.clients');

      }
      // Delete Client Action End

      return view(adminTheme().'clients.clientsEdit',compact('client'));
    }


    //Clients Function End

    //Brands Function

    public function brands(Request $r){

      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);

      // Filter Action Start
      if($r->action){
        if($r->checkid){

        $datas=Attribute::latest()->where('type',2)->whereIn('id',$r->checkid)->get();

        foreach($datas as $data){
          if($allPer && $data->addedby_id!=Auth::id()){
            // You are unauthorized Try!!
          }else{

            if($r->action==1){
              $data->status='active';
              $data->save();
            }elseif($r->action==2){
              $data->status='inactive';
              $data->save();
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){
              
              $medias =Media::latest()->where('src_type',3)->where('src_id',$data->id)->get();
              foreach($medias as $media){
                if(File::exists($media->file_url)){
                  File::delete($media->file_url);
                }
                $media->delete();
              }

              $data->delete();
            }

          }

        }

        Session()->flash('success','Action Successfully Completed!');

        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }

        return redirect()->back();
      }

      //Filter Action End

      $brands=Attribute::latest()->where('type',2)->where('status','<>','temp')
        ->where(function($q) use ($r,$allPer) {

          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
          }


          if($r->status){
             $q->where('status',$r->status); 
          }

          // Check Permission
          if($allPer){
           $q->where('addedby_id',auth::id()); 
          }

      })
      ->select(['id','name','slug','type','created_at','addedby_id','status','fetured'])
      ->paginate(25)->appends([
        'search'=>$r->search,
        'status'=>$r->status,
      ]);

      //Total Count Results
      $totals = DB::table('attributes')
      ->where('type',2)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 'active' then 1 end) as active")
      ->selectRaw("count(case when status = 'inactive' then 1 end) as inactive")
      ->first();

      return view(adminTheme().'brands.brandsAll',compact('brands','totals'));

    }

    public function brandsAction(Request $r,$action,$id=null){
      // Add Brand Action Start
      if($action=='create'){

        $brand =Attribute::where('type',2)->where('status','temp')->where('addedby_id',Auth::id())->first();
        if(!$brand){
          $brand =new Attribute();
        }
        $brand->type =2;
        $brand->status ='temp';
        $brand->addedby_id =Auth::id();
        $brand->save();

        return redirect()->route('admin.brandsAction',['edit',$brand->id]);
      } 
      // Add Brand Action End
      
      $brand =Attribute::where('type',2)->find($id);
      if(!$brand){
        Session()->flash('error','This Brand Are Not Found');
        return redirect()->route('admin.brands');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['brands']['all']);
      if($allPer && $brand->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.brands');
      }

      // Update Brand Action Start
      if($action=='update'){

          $check = $r->validate([
              'name' => 'required|max:191',
              'seo_title' => 'nullable|max:200',
              'seo_desc' => 'nullable|max:250',
              'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
              'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);

          $brand->name=$r->name;
          $brand->short_description=$r->short_description;
          $brand->description=$r->description;
          $brand->seo_title=$r->seo_title;
          $brand->short_description=$r->short_description;
          $brand->seo_keyword=$r->seo_keyword;

           ///////Image UploadStart////////////

            if($r->hasFile('image')){
              $file =$r->image;
              $src  =$brand->id;
              $srcType  =3;
              $fileUse  =1;
              $author=Auth::id();
              uploadFile($file,$src,$srcType,$fileUse,$author);
            }
            
            ///////Image Upload End////////////

            ///////Banner Upload End////////////

            if($r->hasFile('banner')){

              $file =$r->banner;
              $src  =$brand->id;
              $srcType  =3;
              $fileUse  =2;
              $author=Auth::id();
              uploadFile($file,$src,$srcType,$fileUse,$author);

            }

            ///////Banner Upload End////////////

            $slug =Str::slug($r->name);
            if($slug==null){
              $brand->slug=$brand->id;
            }else{
              if(Attribute::where('type',2)->where('slug',$slug)->whereNotIn('id',[$brand->id])->count() >0){
              $brand->slug=$slug.'-'.$brand->id;
              }else{
              $brand->slug=$slug;
              }
            }
            $brand->status =$r->status?'active':'inactive';
            $brand->fetured =$r->fetured?1:0;
            $brand->editedby_id =Auth::id();
            $brand->save();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();

      }
      // Update Brand Action Start

      // Delete Brand Action Start
      if($action=='delete'){
          $medias =Media::latest()->where('src_type',3)->where('src_id',$brand->id)->get();
            foreach($medias as $media){
              if(File::exists($media->file_url)){
                File::delete($media->file_url);
              }
              $media->delete();
            }

            $brand->delete();

            Session()->flash('success','Your Are Successfully Done');
            return redirect()->route('admin.brands');
      }
      // Delete Brand Action End

      return view(adminTheme().'brands.brandsEdit',compact('brand'));
    }

    //Brands Function End


    //Sliders Function
    public function sliders(Request $r){

      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['sliders']['all']);

      // Add Slider Action Start
      if($r->actionType=='addSlider'){
        $slider =Attribute::latest()->where('type',1)->where('addedby_id',Auth::id())->where('status','temp')->first();
        if(!$slider){
        $slider =new Attribute();
        $slider->type =1;
        $slider->status ='temp';
        $slider->addedby_id =Auth::id();
        $slider->save();
        }else{
        $slider->created_at =Carbon::now();
        $slider->save();
        }
        return redirect()->route('admin.slidersEdit',$slider->id);
      }
      // Add Slider Action End

      $sliders=Attribute::latest()->where('type',1)->where('status','<>','temp')->where('parent_id',null)
      ->where(function($q) use ($allPer) {
          // Check Permission
          if($allPer){
           $q->where('addedby_id',auth::id()); 
          }
      })
      ->select(['id','name','location','type','created_at','addedby_id','status','fetured'])
      ->paginate(25);

      

      return view(adminTheme().'sliders.slidersAll',compact('sliders'));
    }

    public function slidersAction(Request $r,$action,$id=null){
      //Create Slider Start
      if($action=='create'){
        $slider =Attribute::latest()->where('type',1)->where('addedby_id',Auth::id())->where('status','temp')->first();
        if(!$slider){
        $slider =new Attribute();
        $slider->type =1;
        $slider->status ='temp';
        $slider->addedby_id =Auth::id();
        $slider->save();
        }else{
        $slider->created_at =Carbon::now();
        $slider->save();
        }
        return redirect()->route('admin.slidersAction',['edit',$slider->id]);
      }
      //Create Slider End
      
      $slider =Attribute::where('type',1)->find($id);
      if(!$slider){
        Session()->flash('error','This Slider Are Not Found');
        return redirect()->route('admin.sliders');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['sliders']['all']);
      if($allPer && $slider->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.sliders');
      }

      // Update Slider Action Start
      if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:191',
            'location' => 'nullable|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:25600',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $activeSlider =Attribute::where('type',1)->where('location',$r->location)->whereNotIn('id',[$slider->id])->first();
      
        if($activeSlider && $r->location){
          Session::flash('error','This Location Have Already a slider');
          return back();
        }

        $slider->name=$r->name;
        $slider->description=$r->description;
        $slider->location=$r->location;
        $slider->seo_title=$r->vediolink;

        ///////Image UploadStart////////////

        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$slider->id;
          $srcType  =3;
          $fileUse  =1;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        
        ///////Image Upload End////////////

        ///////Banner Upload End////////////

        if($r->hasFile('banner')){

          $file =$r->banner;
          $src  =$slider->id;
          $srcType  =3;
          $fileUse  =2;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);

        }

        ///////Banner Upload End////////////

        ///////Gallery Images UploadStart////////////
        
        $files=$r->file('images');
        if($files){
          
            foreach($files as $file)
            {

              $slide =new Attribute();
              $slide->type =1;
              $slide->parent_id =$slider->id;
              $slide->status ='active';
              $slide->addedby_id =Auth::id();
              $slide->save();

              $src  =$slide->id;
              $srcType  =3;
              $fileUse  =1;
              $author  =Auth::id();
              uploadFile($file,$src,$srcType,$fileUse,$author);

            }
          } 

        ///////Slide Drag Update Start////////////
        if(isset($r->slideid)){
          for ($i=0; $i < count($r->slideid); $i++) { 
            $slide =$slider->sliderItems->find($r->slideid[$i]);
            if($slide){
              $slide->view=$i;
              $slide->save();
            }
          }
        }
        ///////Slide Drag Update End////////////

        $slug =Str::slug($r->name);
        if($slug==null){
          $slider->slug=$slider->id;
        }else{
          if(Attribute::where('type',1)->where('slug',$slug)->whereNotIn('id',[$slider->id])->count() >0){
          $slider->slug=$slug.'-'.$slider->id;
          }else{
          $slider->slug=$slug;
          }
        }
        $slider->status =$r->status?'active':'inactive';
        $slider->editedby_id =Auth::id();
        $slider->save();

        Session()->flash('success','Your Are Successfully Done');
        return redirect()->back();

      }
      // Update Slider Action End

      // Delete Slider Action Start
      if($action=='delete'){
         //Sub Slider Items Delete
          foreach($slider->sliderItems as $slide){

            //Galleries  Media File Delete
              $medies =Media::where('src_type',3)->where('src_id',$slide->id)->get();
              foreach ($medies as  $media) {
                  if(File::exists($media->file_url)){
                      File::delete($media->file_url);
                  }
                  $media->delete();
              }

            $slide->delete();

          }

        //Galleries  Media File Delete
        $sliderMedies =Media::where('src_type',3)->where('src_id',$slider->id)->get();
        foreach ($sliderMedies as  $media) {
              if(File::exists($media->file_url)){
                  File::delete($media->file_url);
              }
              $media->delete();
          }

        $slider->delete();

        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->route('admin.sliders');

      }
      // Delete Slider Action End

      return view(adminTheme().'sliders.slidersEdit',compact('slider'));
    }

    public function slideAction(Request $r,$action,$id){
      $slide =Attribute::where('type',1)->find($id);
      if(!$slide){
        Session()->flash('error','This Slide Are Not Found');
        return redirect()->route('admin.sliders');
      }

      // Update Slide Slider Action Start
      if($action=='update'){
          $check = $r->validate([
              'name' => 'nullable|max:191',
              'buttonText' => 'nullable|max:200',
              'buttonLink' => 'nullable|max:200',
              'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
          ]);

        $slide->name =$r->name;
        $slide->seo_title=$r->buttonText?:null;
        $slide->seo_description=$r->buttonLink?:null;
        $slide->description=$r->description;

        ///////Image UploadStart////////////

        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$slide->id;
          $srcType  =3;
          $fileUse  =1;
          $author  =Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        
        ///////Image Upload End////////////

        $slug =Str::slug($r->name);
        if($slug==null){
          $slide->slug=$slide->id;
        }else{
          if(Attribute::where('type',1)->where('slug',$slug)->whereNotIn('id',[$slide->id])->count() >0){
          $slide->slug=$slug.'-'.$slide->id;
          }else{
          $slide->slug=$slug;
          }
        }
        $slide->status =$r->status?'active':'inactive';
        $slide->editedby_id =Auth::id();
        $slide->save();

        Session()->flash('success','Your Are Successfully Done');
        return redirect()->back();

      }
      // Update Slide Slider Action End

      // Delete Slide Slider Action Start
      if($action=='delete'){
        //Galleries  Media File Delete
        $medies =Media::where('src_type',3)->where('src_id',$slide->id)->get();
          foreach ($medies as  $media) {
              if(File::exists($media->file_url)){
                  File::delete($media->file_url);
              }
              $media->delete();
          }
        $slide->delete();
        Session()->flash('success','Your Are Successfully Deleted');
        return redirect()->back();
      }
      // Delete Slide Slider Action End


      return  view(adminTheme().'sliders.slideEdit',compact('slide'));
    }

    //Sliders Function End


    //Galleries Function Start

    public function galleries(Request $r){

      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['galleries']['all']);

      $galleries=Attribute::latest()->where('type',4)->where('status','<>','temp')->where('parent_id',null)
      ->where(function($q) use ($allPer) {
          // Check Permission
          if($allPer){
           $q->where('addedby_id',auth::id()); 
          }
      })
      ->select(['id','name','location','type','created_at','addedby_id','status','fetured'])
      ->paginate(25);
      return view(adminTheme().'galleries.galleriesAll',compact('galleries'));

    }

    public function galleriesAction(Request $r,$action,$id=null){

      //Create Gallery Start
      if($action=='create'){
        $gallery =Attribute::latest()->where('type',4)->where('addedby_id',Auth::id())->where('status','temp')->first();
        if(!$gallery){
        $gallery =new Attribute();
        $gallery->type =4;
        $gallery->status ='temp';
        $gallery->addedby_id =Auth::id();
        $gallery->save();
        }else{
        $gallery->created_at =Carbon::now();
        $gallery->save();
        }
        return redirect()->route('admin.galleriesAction',['edit',$gallery->id]);
      }
      //Create Gallery End

      $gallery =Attribute::where('type',4)->find($id);
      if(!$gallery){
        Session()->flash('error','This Gallery Are Not Found');
        return redirect()->route('admin.galleries');
      }

      //Check Authorized User
      $allPer = empty(json_decode(Auth::user()->permission->permission, true)['galleries']['all']);
      if($allPer && $gallery->addedby_id!=Auth::id()){
        Session()->flash('error','You are unauthorized Try!!');
        return redirect()->route('admin.galleries');
      }

      //Update Gallery Start
      if($action=='update'){

        $check = $r->validate([
            'name' => 'required|max:191',
            'location' => 'nullable|max:200',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:25600',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $activeGallery =Attribute::where('type',4)->where('location',$r->location)->whereNotIn('id',[$gallery->id])->first();
      
        if($activeGallery && $r->location){
          Session::flash('error','This Location Have Already a Gallery');
          return back();
        }
        
        $gallery->name=$r->name;
        $gallery->description=$r->description;
        $gallery->location=$r->location;

        ///////Image UploadStart////////////

        if($r->hasFile('image')){
          $file =$r->image;
          $src  =$gallery->id;
          $srcType  =3;
          $fileUse  =1;
          $author =Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);
        }
        ///////Image Upload End////////////

        ///////Banner Upload End////////////

        if($r->hasFile('banner')){

          $file =$r->banner;
          $src  =$gallery->id;
          $srcType  =3;
          $fileUse  =2;
          $author=Auth::id();
          uploadFile($file,$src,$srcType,$fileUse,$author);

        }

        ///////Banner Upload End////////////

        ///////Gallery Images UploadStart////////////
        
        $files=$r->file('images');
        if($files){
          
            foreach($files as $file)
            {
              
              $file =$file;
              $src  =$gallery->id;
              $srcType  =3;
              $fileUse  =3;
              $fileStatus=false;
              $author=Auth::id();
              uploadFile($file,$src,$srcType,$fileUse,$author,$fileStatus);
            }
          }
            
        ///////Gallery Images Upload End////////////
     
        $slug =Str::slug($r->name);
        if($slug==null){
          $gallery->slug=$gallery->id;
        }else{
          if(Attribute::where('type',1)->where('slug',$slug)->whereNotIn('id',[$gallery->id])->count() >0){
          $gallery->slug=$slug.'-'.$gallery->id;
          }else{
          $gallery->slug=$slug;
          }
        }
        $gallery->status =$r->status?'active':'inactive';
        $gallery->editedby_id =Auth::id();
        $gallery->save();

        if(isset($r->imageid)){

          for ($i=0; $i < count($r->imageid); $i++) { 
              $image =$gallery->galleryImages()->where('id',$r->imageid[$i])->first();
  
              if($image){
                $image->drag=$i;
                $image->alt_text=$r->imageName[$i];
                $image->description=$r->imageDescription[$i];
                $image->save();
              }
          }
  
        }
        
        if(isset($r->checkid)){
  
          for ($i=0; $i < count($r->checkid); $i++) { 
              $image =$gallery->galleryImages()->where('id',$r->checkid[$i])->first();
              if($image){
                if(File::exists($image->file_url)){
                    File::delete($image->file_url);
                }
               $image->delete();
              }
            
            }
        }

        
        Session()->flash('success','Your Are Successfully Update');
        return redirect()->back();

      }
      //Update Gallery End

      //Delete Gallery Start
      if($action=='delete'){

        //Galleries  Media all File Delete
        $galleryMedies =Media::where('src_type',3)->where('src_id',$gallery->id)->get();

        foreach ($galleryMedies as  $media) {
              if(File::exists($media->file_url)){
                  File::delete($media->file_url);
              }
              $media->delete();
          }

        $gallery->delete();

        Session()->flash('success','Your Are Successfully Done');
        return redirect()->route('admin.galleries');

      }
      //Delete Gallery End

      return view(adminTheme().'galleries.galleriesEdit',compact('gallery'));

    }

    //Galleries Function End


    public function themeSetting(Request $r){
      return view(adminTheme().'theme-setting.themeSetting');
    }



    // User Management Function Start

    public function usersAdmin(Request $r){

      //Filter Actions Start
      if($r->action){
        if($r->checkid){
  
        $datas=User::latest()->whereIn('status',[0,1])->where('admin',true)->whereIn('id',$r->checkid)->get();
  
        foreach($datas as $data){
  
            if($r->action==1){
              $data->status=1;
              $data->save();
            }elseif($r->action==2){
              $data->status=0;
              $data->save();
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){
              
              //User Media File Delete
              $data->admin=false;
              $data->addedby_at=null;
              $data->permission_id=null;
              $data->addedby_id=null;
              $data->save();
  
            }
  
        }
  
        Session()->flash('success','Action Successfully Completed!');
  
        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }
  
        return redirect()->back();
      }
  
      //Filter Action End

    
      $users =User::latest()->whereIn('status',[0,1])->where('admin',true)
      ->where(function($q) use($r) {
  
          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
              $q->orWhere('email','LIKE','%'.$r->search.'%');
              $q->orWhere('mobile','LIKE','%'.$r->search.'%');
          }

          if($r->role){
             $q->where('permission_id',$r->role);
          }
          
          if($r->startDate || $r->endDate)
          {
              if($r->startDate){
                  $from =$r->startDate;
              }else{
                  $from=Carbon::now()->format('Y-m-d');
              }
  
              if($r->endDate){
                  $to =$r->endDate;
              }else{
                  $to=Carbon::now()->format('Y-m-d');
              }
  
              $q->whereDate('addedby_at','>=',$from)->whereDate('addedby_at','<=',$to);
          }
  
      })
      ->select(['id','permission_id','name','email','mobile','addedby_at','addedby_id','status'])
      ->paginate(25)->appends([
        'search'=>$r->search,
        'startDate'=>$r->startDate,
        'endDate'=>$r->endDate,
      ]);

      //Total Count Results
      $totals = DB::table('users')->whereIn('status',[0,1])->where('admin',true)
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 1 then 1 end) as active")
      ->selectRaw("count(case when status = 0 then 1 end) as inactive")
      ->first();
      
      $roles =Permission::latest()->where('status','active')->get();

      return view(adminTheme().'users.admins.users',compact('users','totals','roles'));
    }
  
    public function usersAdminAction (Request $r,$action,$id=null){
      
      //Add Admin User Start
      if($action=='create' && $r->isMethod('post')){
  
        if(filter_var($r->username, FILTER_VALIDATE_EMAIL)){
          $hasUser =User::latest()->whereIn('status',[0,1])->where('email',$r->username)->first();
        }else{
          $hasUser =User::latest()->whereIn('status',[0,1])->where('mobile',$r->username)->first();
        }
    
        if(!$hasUser){
            Session()->flash('error','This User Are Not Register');
            return redirect()->route('admin.usersAdmin');
        }
    
        if($hasUser->admin){
            Session()->flash('error','This User Are already Admin Authorize');
            return redirect()->route('admin.usersAdmin');
        }
    
        $hasUser->admin=true;
        $hasUser->permission_id=1;
        $hasUser->addedby_at=Carbon::now();
        $hasUser->addedby_id=Auth::id();
        $hasUser->save();
       
        Session()->flash('success','User Are Successfully Admin Authorize Done!');
        return redirect()->route('admin.usersAdminAction',['edit',$hasUser->id]);
  
      }
      //Add Admin User End
  
  
      $user=User::whereIn('status',[0,1])->where('admin',true)->find($id);

      if(!$user){
        Session()->flash('error','This Admin User Are Not Found');
        return redirect()->route('admin.usersAdmin');
      }
  
        //Update User Profile Start
        if($action=='update' && $r->isMethod('post')){
  
            $check = $r->validate([
                 'name' => 'required|max:100|unique:users,name,'.$user->id,
                 'email' => 'required|max:100|unique:users,email,'.$user->id,
                 'mobile' => 'nullable|max:20|unique:users,mobile,'.$user->id,
                 'gender' => 'nullable|max:10',
                 'address' => 'nullable|max:191',
                 'division' => 'nullable|numeric',
                 'district' => 'nullable|max:191',
                 'city' => 'nullable|max:191',
                 'postal_code' => 'nullable|max:20',
                 'role' => 'nullable|numeric',
                 'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
     
             ]);
     
           $user->name =$r->name;
           $user->mobile =$r->mobile;
           $user->email =$r->email;
           $user->gender =$r->gender;
           $user->address_line1 =$r->address;
           $user->division =$r->division;
           $user->district =$r->district;
           $user->city =$r->city;
           $user->postal_code =$r->postal_code;
           $user->permission_id =$r->role;
           
           ///////Image UploadStart////////////
           if($r->hasFile('image')){
             $file =$r->image;
             $src  =$user->id;
             $srcType  =6;
             $fileUse  =1;
             $author=Auth::id();
             uploadFile($file,$src,$srcType,$fileUse,$author);
           }
           ///////Image Upload End////////////
     
           $user->status=$r->status?true:false;
           $user->save();
     
           Session()->flash('success','Your Updated Are Successfully Done!');
           return redirect()->route('admin.usersAdminAction',['edit',$user->id]);
        }
        //Update User Profile End
  
        //Update User Password Start
        if($action=='change-password' && $r->isMethod('post')){
          
          $validator = Validator::make($r->all(), [
              'old_password' => 'required|string|min:8',
              'password' => 'required|string|min:8|confirmed|different:old_password',
          ]);
        
          if($validator->fails()){
              return redirect()->route('admin.usersAdminAction',['edit',$user->id])->withErrors($validator)->withInput();
          }
  
          if(Hash::check($r->old_password, $user->password)){
            $user->password_show=$r->password;
            $user->password=Hash::make($r->password);
            $user->update();
  
            Session()->flash('success','Your Are Successfully Done');
            return redirect()->route('admin.usersAdminAction',['edit',$user->id]);
          }else{
            Session()->flash('error','Current Password Are Not Match');
            return redirect()->route('admin.usersAdminAction',['edit',$user->id]);
          }
        }
        //Update User Password End
  
        //Delete User End
        if($action=='delete'){
          $user->admin=false;
          $user->addedby_at=null;
          $user->permission_id=null;
          $user->addedby_id=null;
          $user->save();
  
          Session()->flash('success','Admin User Are Removed Successfully Done');
          return redirect()->route('admin.usersAdmin');
        }
        //Delete User End
        $roles =Permission::latest()->where('status','active')->get();
  
        return view(adminTheme().'users.admins.editUser',compact('user','roles'));
  
    }
  
  
  
    public function usersCustomer(Request $r){

      //Filter Actions Start
      if($r->action){
        if($r->checkid){
  
        $datas=User::latest()->whereIn('status',[0,1])->whereIn('id',$r->checkid)->get();
  
        foreach($datas as $data){
  
            if($r->action==1){
              $data->status=1;
              $data->save();
            }elseif($r->action==2){
              $data->status=0;
              $data->save();
            }elseif($r->action==3){
              $data->fetured=true;
              $data->save();
            }elseif($r->action==4){
              $data->fetured=false;
              $data->save();
            }elseif($r->action==5){
              
              $userFiles =Media::latest()->where('src_type',6)->where('src_id',$data->id)->get();
              foreach ($userFiles as $media) {
                  if(File::exists($media->file_url)){
                        File::delete($media->file_url);
                    }
                  $media->delete();
              }
              $data->delete();
  
            }
  
        }
  
        Session()->flash('success','Action Successfully Completed!');
  
        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }
  
        return redirect()->back();
      }
  
      //Filter Action End
  
      $users =User::latest()->whereIn('status',[0,1])
      ->where(function($q) use($r) {
  
          if($r->search){
              $q->where('name','LIKE','%'.$r->search.'%');
              $q->orWhere('email','LIKE','%'.$r->search.'%');
              $q->orWhere('mobile','LIKE','%'.$r->search.'%');
          }
          
          if($r->startDate || $r->endDate)
          {
              if($r->startDate){
                  $from =$r->startDate;
              }else{
                  $from=Carbon::now()->format('Y-m-d');
              }
  
              if($r->endDate){
                  $to =$r->endDate;
              }else{
                  $to=Carbon::now()->format('Y-m-d');
              }
  
              $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
          }
  
      })
      ->select(['id','permission_id','name','email','mobile','created_at','addedby_id','status'])
        ->paginate(25)->appends([
          'search'=>$r->search,
          'startDate'=>$r->startDate,
          'endDate'=>$r->endDate,
        ]);

      //Total Count Results
      $totals = DB::table('users')->whereIn('status',[0,1])
      ->selectRaw('count(*) as total')
      ->selectRaw("count(case when status = 1 then 1 end) as active")
      ->selectRaw("count(case when status = 0 then 1 end) as inactive")
      ->first();
  
      return view(adminTheme().'users.customers.users',compact('users','totals'));
    }
  
  
    public function usersCustomerAction(Request $r,$action,$id=null){
       
      //Add New User Start
      if($action=='create' && $r->isMethod('post')){

        $user =User::where('email',$r->email)->orWhere('name',$r->name)->first();
        if(!$user){
          $password=Str::random(8);
          $user =new User();
          $user->name =$r->name;
          $user->email =$r->email;
          $user->password_show=$password;
          $user->password=Hash::make($password);
          $user->save();
        }
        
        return redirect()->route('admin.usersCustomerAction',['edit',$user->id]);
      }
      //Add New User End
      
      
      $user=User::whereIn('status',[0,1])->find($id);
      if(!$user){
        Session()->flash('error','This User Are Not Found');
        return redirect()->route('admin.usersCustomer');
      }
  
      //Update User Profile Start
      if($action=='update' && $r->isMethod('post')){

          $check = $r->validate([
                'name' => 'required|max:100|unique:users,name,'.$user->id,
                'email' => 'required|max:100|unique:users,email,'.$user->id,
                'mobile' => 'nullable|max:20|unique:users,mobile,'.$user->id,
                'gender' => 'nullable|max:10',
                'address' => 'nullable|max:191',
                'division' => 'nullable|numeric',
                'district' => 'nullable|numeric',
                'city' => 'nullable|numeric',
                'created_at' => 'nullable|date|max:50',
                'postal_code' => 'nullable|max:20',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            ]);
    
          $user->name =$r->name;
          $user->mobile =$r->mobile;
          $user->email =$r->email;
          $user->gender =$r->gender;
          $user->address_line1 =$r->address;
          $user->division =$r->division;
          $user->district =$r->district;
          $user->city =$r->city;
          $user->postal_code =$r->postal_code;
          $user->created_at =$r->created_at?:Carbon::now();
    
          ///////Image UploadStart////////////
          if($r->hasFile('image')){
    
            $file =$r->image;
            $src  =$user->id;
            $srcType  =6;
            $fileUse  =1;
            $author =Auth::id();
            uploadFile($file,$src,$srcType,$fileUse,$author);
          }
          ///////Image Upload End////////////
    
          $user->status=$r->status?true:false;
          $user->save();
    
          Session()->flash('success','Your Updated Are Successfully Done!');
          return redirect()->back();
  
        }
        //Update User Profile End
  
        //Update User Password Change Start
        if($action=='change-password' && $r->isMethod('post')){
     
          $validator = Validator::make($r->all(), [
              'old_password' => 'required|string|min:8',
              'password' => 'required|string|min:8|confirmed|different:old_password',
          ]);
         
          if($validator->fails()){
              return redirect()->back()->withErrors($validator)->withInput();
          }
  
          if(Hash::check($r->old_password, $user->password)){
            $user->password_show=$r->password;
            $user->password=Hash::make($r->password);
            $user->update();
           
            Session()->flash('success','Your Are Successfully Done');
            return redirect()->back();
          }else{
          Session()->flash('error','Current Password Are Not Match');
          return redirect()->back();
          }
  
        }
        //Update User Password Change End
  
        //Delete User Start
        if($action=='delete'){
  
          $userFiles =Media::latest()->where('src_type',6)->where('src_id',$user->id)->get();
          foreach ($userFiles as $media) {
              if(File::exists($media->file_url)){
                    File::delete($media->file_url);
                }
              $media->delete();
          }
          $user->delete();
          Session()->flash('success','User Are Deleted Successfully Deleted!');
          return redirect()->back();
        }
        //Delete User End
  
      return view(adminTheme().'users.customers.editUser',compact('user'));
  
    }

    public function subscribes(Request $r){
  
    // Filter Action Start
      if($r->action){
        if($r->checkid){

        PostExtra::latest()->where('type',1)->whereIn('id',$r->checkid)->delete();

        Session()->flash('success','Action Successfully Completed!');

        }else{
          Session()->flash('info','Please Need To Select Minimum One Post');
        }
        
        return redirect()->back();
      }

      //Filter Action End

    $subscribes =PostExtra::where('type',1)
    ->where(function($q) use ($r){

      if($r->search){
            $q->where('name','LIKE','%'.$r->search.'%');
      }
        
      if($r->startDate || $r->endDate)
        {
            if($r->startDate){
                $from =$r->startDate;
            }else{
                $from=Carbon::now()->format('Y-m-d');
            }

            if($r->endDate){
                $to =$r->endDate;
            }else{
                $to=Carbon::now()->format('Y-m-d');
            }

            $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);
        }

    })
    ->select(['id','name','created_at'])
    ->paginate(50)->appends([
      'search'=>$r->search,
      'startDate'=>$r->startDate,
      'endDate'=>$r->endDate,
    ]);

    return view(adminTheme().'users.subscribes.subscribeAll',compact('subscribes'));
  }


public function userRoles(Request $r){
  $allPer = empty(json_decode(Auth::user()->permission->permission, true)['services']['all']);

  $roles =Permission::latest()
  ->where('status','active')
  ->where(function($q) use($r,$allPer) {

      if($r->search){
          $q->where('name','LIKE','%'.$r->search.'%');
      }
      
      if($r->startDate || $r->endDate)
      {
          if($r->startDate){
              $from =$r->startDate;
          }else{
              $from=Carbon::now()->format('Y-m-d');
          }

          if($r->endDate){
              $to =$r->endDate;
          }else{
              $to=Carbon::now()->format('Y-m-d');
          }

          $q->whereDate('created_at','>=',$from)->whereDate('created_at','<=',$to);

      }

     // Check Permission
      if($allPer){
       $q->where('addedby_id',auth::id()); 
      }

  })
  ->select(['id','name','created_at','addedby_id','status'])
    ->paginate(25)->appends([
      'search'=>$r->search,
      'startDate'=>$r->startDate,
      'endDate'=>$r->endDate,
    ]);

  return view(adminTheme().'users.roles.userRoles',compact('roles'));
}


  public function userRoleAction(Request $r,$action,$id=null){

    if($action=='create'){
        $role  =Permission::where('addedby_id',Auth::id())->where('status','temp')->first();
        if(!$role){
          $role = new Permission();
          $role->status='temp';
          $role->addedby_id=Auth::id();
        }
        $role->created_at=Carbon::now();
        $role->save();

        return redirect()->route('admin.userRoleAction',['edit',$role->id]);
    }

    $role=Permission::find($id);
    if(!$role){
      Session()->flash('error','This Role Are Not Found');
      return redirect()->route('admin.userRoles');
    }
    
    if($action=='update'){

      //Role Update
      $check = $r->validate([
          'name' => 'required|max:100',
      ]);

      $role->name =$r->name;
      if($role->id==1){
      $role->permission =$r->permission;
      }else{
        $role->permission =$r->permission;  
      }
      $role->status ='active';
      $role->save();

      Session()->flash('success','Role Updated Are Successfully Done!');
      return redirect()->back();
    }
    
    if($action=='delete'){
      //Role Delete
      $role->delete();

      Session()->flash('success','Role Deleted Are Successfully Done!');
      return redirect()->route('admin.userRoles');

    }

    return view(adminTheme().'users.roles.userRoleEdit',compact('role'));

  }
    
  // User Management Function End


  // Setting Function Start
  public function setting(Request $r,$type){

    $general =General::first();
    if($type=='general'){
      return view(adminTheme().'setting.general',compact('general','type'));
    }else if($type=='mail'){
      return view(adminTheme().'setting.mail',compact('general','type'));
    }else if($type=='preview-mail'){
        
        if($r->template=='Contact Mail'){
            $data =array(
                        'name'=>'Demo Name',
                        'phone'=>'+8801745454545',
                        'email'=>'demoemail@gmail.com',
                        'type'=>'Testing Contact Mail for Admin',
                        'message'=>'We are testing for email testing form admin access . i hope all are successfully working.',
                    );
            $datas =array('r'=>$data);
            return view('mails.ContactMail',compact('general','type','datas'));
        }elseif($r->template=='Register Mail'){
            
            $user =Auth::user();
            $datas =array('user'=>$user);
            return view('mails.registrationMail',compact('general','type','datas'));
        }
      return view('mails.deafultMail',compact('general','type'));
      
    }else if($type=='sms'){
      return view(adminTheme().'setting.sms',compact('general','type'));
    }else if($type=='social'){
      return view(adminTheme().'setting.social',compact('general','type'));
    }else if($type=='document'){
      return view(adminTheme().'setting.document',compact('general','type'));
    }else if($type=='logo'){

      if(File::exists($general->logo)){
            File::delete($general->logo);
      }
      $general->logo=null;
      $general->save();

      Session()->flash('success','Logo Deleted Are Successfully Done!');
      return redirect()->back();
    }else if($type=='favicon'){
       if(File::exists($general->favicon)){
            File::delete($general->favicon);
      }
      $general->favicon=null;
      $general->save();

      Session()->flash('success','Logo Deleted Are Successfully Done!');
      return redirect()->back();
    }else if($type=='banner'){
       if(File::exists($general->banner)){
            File::delete($general->banner);
      }
      $general->banner=null;
      $general->save();

      Session()->flash('success','Banner Deleted Are Successfully Done!');
      return redirect()->back();
    }else if($type=='cache-clear'){

      //return view(adminTheme().'setting.cacheDatabase',compact('general','type'));
      
      Artisan::call('cache:clear');
      Artisan::call('config:clear');
      Artisan::call('config:cache');
      Artisan::call('view:clear');
      Artisan::call('route:clear');
      Artisan::call('clear-compiled');

      Session()->flash('success','Cache Clear Are Successfully Done!');

      return redirect()->route('admin.dashboard');

    }else{
      return redirect()->route('admin.setting','general');
    }

  }


  public function settingUpdate(Request $r,$type){


    $general =General::first();

    if($type=='general'){

        $check = $r->validate([
            'title' => 'nullable|max:100',
            'subtitle' => 'nullable|max:200',
            'mobile' => 'nullable|max:100',
            'email' => 'nullable|max:100',
            'currency' => 'nullable|max:10',
            'website' => 'nullable|max:100',
            'meta_author' => 'nullable|max:100',
            'meta_title' => 'nullable|max:200',
            'meta_description' => 'nullable|max:200',
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'favicon' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'banner' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $general->title=$r->title;
        $general->subtitle=$r->subtitle;
        $general->mobile=$r->mobile;
        $general->email=$r->email;
        $general->address_one=$r->address_one;
        $general->address_two=$r->address_two;
        $general->currency=$r->currency;
        $general->website=$r->website;
        $general->meta_author=$r->meta_author;
        $general->meta_title=$r->meta_title;
        $general->meta_keyword=$r->meta_keyword;
        $general->meta_description=$r->meta_description;
        $general->script_head=$r->script_head;
        $general->script_body=$r->script_body;
        $general->custom_css=$r->custom_css;
        $general->custom_js=$r->custom_js;
        $general->copyright_text=$r->footer_text;
        

        ///////Image UploadStart////////////

        if($r->hasFile('logo')){

          $file=$r->logo;

          if(File::exists($general->logo)){
                File::delete($general->logo);
          }

          $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
          $fullName = basename($file->getClientOriginalName());
          $ext =$file->getClientOriginalExtension();
          $size =$file->getSize();

          $year =carbon::now()->format('Y');
          $month =carbon::now()->format('M');
          $folder = $month.'_'.$year;

          $img =safeUploadName($file);
          $path ="medies/".$folder;
          $fullPath ="medies/".$folder.'/'.$img;

          $file->move(public_path($path), $img);
          $general->logo =$fullPath;

      }

         ///////Image UploadStart////////////

        if($r->hasFile('favicon')){

            $file=$r->favicon;

            if(File::exists($general->favicon)){
                  File::delete($general->favicon);
            }

            $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
            $fullName = basename($file->getClientOriginalName());
            $ext =$file->getClientOriginalExtension();
            $size =$file->getSize();

            $year =carbon::now()->format('Y');
            $month =carbon::now()->format('M');
            $folder = $month.'_'.$year;

            $img =safeUploadName($file);
            $path ="medies/".$folder;
            $fullPath ="medies/".$folder.'/'.$img;

            $file->move(public_path($path), $img);
            $general->favicon =$fullPath;

        }
        
        if($r->hasFile('banner')){

            $file=$r->banner;

            if(File::exists($general->banner)){
                  File::delete($general->banner);
            }

            $name = basename($file->getClientOriginalName(), '.'.$file->getClientOriginalExtension());
            $fullName = basename($file->getClientOriginalName());
            $ext =$file->getClientOriginalExtension();
            $size =$file->getSize();

            $year =carbon::now()->format('Y');
            $month =carbon::now()->format('M');
            $folder = $month.'_'.$year;

            $img =safeUploadName($file);
            $path ="medies/".$folder;
            $fullPath ="medies/".$folder.'/'.$img;

            $file->move(public_path($path), $img);
            $general->banner =$fullPath;

        }
        $general->commingsoon_mode=$r->commingsoon_mode?true:false;
        $general->save();

        Session()->flash('success','General Updated Are Successfully Done!');

    }


    if($type=='mail'){

      $check = $r->validate([
            'mail_from_address' => 'nullable|max:100',
            'mail_from_name' => 'nullable|max:100',
            'mail_driver' => 'nullable|max:100',
            'mail_host' => 'nullable|max:100',
            'mail_port' => 'nullable|max:100',
            'mail_encryption' => 'nullable|max:100',
            'mail_username' => 'nullable|max:100',
            'mail_password' => 'nullable|max:100',
            'admin_mails' => 'nullable|max:1000',
        ]);

      $general->mail_from_address=$r->mail_from_address;
      $general->mail_from_name=$r->mail_from_name;
      $general->mail_driver=$r->mail_driver;
      $general->mail_host=$r->mail_host;
      $general->mail_port=$r->mail_port;
      $general->mail_encryption=$r->mail_encryption;
      $general->mail_username=$r->mail_username;
      $general->mail_password=$r->mail_password;
      $general->admin_mails=$r->admin_mails;
      $general->mail_status=$r->mail_status?true:false;
      $general->register_mail_user=$r->register_mail_user?true:false;
      $general->register_mail_author=$r->register_mail_author?true:false;
      $general->forget_password_mail_user=$r->forget_password_mail_user?true:false;
      $general->register_verify_mail_user=$r->register_verify_mail_user?true:false;
      $general->save();

      Session()->flash('success','Mail Updated Are Successfully Done!');

    }
    
    if($type=='send-testing-mail'){

        $check = $r->validate([
            'mail_type' => 'required|max:200',
            'mail_address' => 'required|max:200',
        ]);
        
        if(general()->mail_status && $r->mail_address){
            //Mail Data
            //return $r;
            if($r->mail_type=='Contact Mail'){
                $data =array(
                        'name'=>'Demo Name',
                        'phone'=>'+8801745454545',
                        'email'=>'demoemail@gmail.com',
                        'type'=>'Testing Contact Mail for Admin',
                        'message'=>'We are testing for email testing form admin access . i hope all are successfully working.',
                    );
                $datas =array('r'=>$data);
                $template ='mails.ContactMail';
            }elseif($r->mail_type=='Register Mail'){
                $user =Auth::user();
                $datas =array('user'=>$user);
                $template ='mails.registrationMail';
            }else{
                $template ='mails.deafultMail';
            }
            
            $toEmail =$r->mail_address?:general()->mail_address;
            $toName =general()->mail_from_name;
            $subject =$r->mail_type.' Mail Testing Form '.general()->title;
            
            $attachments=null;
            if(1==2){
                $attachments[]=array(
                        'path'=>asset($r->bannerFile->file_url),
                        'name'=>$r->bannerFile->file_name,
                        'mime'=>$r->bannerFile->mime_type
                    );
            }

            $isSend =sendMail($toEmail,$toName,$subject,$datas,$template,$attachments);

            if($isSend){
                Session()->flash('success','Mail Send Are Successfully Done!.Please chack Email address');
            }else{
                Session()->flash('error','Mail Send Are Fail!.Please chack Email Setup Configration.');
            }
            
        }else{
                Session()->flash('error','Please Active your Mail status.');
        }
        
        
        return redirect()->route('admin.setting','mail');
        
    }

    if($type=='sms'){
        
        $check = $r->validate([
            'sms_type' => 'nullable|max:50',
            'sms_senderid' => 'nullable|max:50',
            'sms_url_nonmasking' => 'nullable|max:200',
            'sms_url_masking' => 'nullable|max:200',
            'sms_username' => 'nullable|max:50',
            'sms_password' => 'nullable|max:50',
            'admin_numbers' => 'nullable|max:1000',
        ]);
        
      $general->sms_type=$r->sms_type;
      $general->sms_senderid=$r->sms_senderid;
      $general->sms_url_nonmasking=$r->sms_url_nonmasking;
      $general->sms_url_masking=$r->sms_url_masking;
      $general->sms_username=$r->sms_username;
      $general->sms_password=$r->sms_password;
      $general->admin_numbers=$r->admin_numbers;
      $general->sms_status=$r->sms_status?true:false;
      $general->register_sms_user=$r->register_sms_user?true:false;
      $general->register_sms_author=$r->register_sms_author?true:false;
      $general->forget_password_sms_user=$r->forget_password_sms_user?true:false;
      $general->register_verify_sms_user=$r->register_verify_sms_user?true:false;
      $general->save();

      Session()->flash('success','SMS Updated Are Successfully Done!');

    }

    if($type=='social'){
      

        $check = $r->validate([
            'facebook_link' => 'nullable|max:200',
            'twitter_link' => 'nullable|max:200',
            'instagram_link' => 'nullable|max:200',
            'linkedin_link' => 'nullable|max:200',
            'pinterest_link' => 'nullable|max:200',
            'youtube_link' => 'nullable|max:200',
            'fb_app_id' => 'nullable|max:100',
            'fb_app_secret' => 'nullable|max:100',
            'fb_app_redirect_url' => 'nullable|max:200',
            'google_client_id' => 'nullable|max:100',
            'google_client_secret' => 'nullable|max:100',
            'google_client_redirect_url' => 'nullable|max:200',
            'tw_app_id' => 'nullable|max:100',
            'tw_app_secret' => 'nullable|max:100',
            'tw_app_redirect_url' => 'nullable|max:200',
        ]);

        $general->facebook_link=$r->facebook_link;
        $general->twitter_link=$r->twitter_link;
        $general->instagram_link=$r->instagram_link;
        $general->linkedin_link=$r->linkedin_link;
        $general->pinterest_link=$r->pinterest_link;
        $general->youtube_link=$r->youtube_link;
        $general->fb_app_id=$r->fb_app_id;
        $general->fb_app_secret=$r->fb_app_secret;
        $general->fb_app_redirect_url=$r->fb_app_redirect_url;
        $general->google_client_id=$r->google_client_id;
        $general->google_client_secret=$r->google_client_secret;
        $general->google_client_redirect_url=$r->google_client_redirect_url;
        $general->tw_app_id=$r->tw_app_id;
        $general->tw_app_secret=$r->tw_app_secret;
        $general->tw_app_redirect_url=$r->tw_app_redirect_url;
        $general->save();

        Session()->flash('success','Advance Updated Are Successfully Done!');

    }

    
    return redirect()->route('admin.setting',$type);


  }

  // Setting Function End
  
  public function contentEditor(Request $r,$action,$id){
    $primary =Post::find($id);
    if(!$primary){
      return abort(404);
    }
    $datas =PostContentElement::where('parent_id',null)->where('src_id',$primary->id)->orderBy('drag')->get();

    return view(adminTheme().'setting.contentEditor',compact('primary','action','datas'));
  }

  public function contentEditorAction(Request $r,$action,$id=null){

    if($action=='addSection'){
    $data =new PostContentElement();
    $data->src_id=$id;
    $data->type=0;
    $data->status='active';
    $data->save();
    }
    
    if($action=='copySection'){
        $parentData =PostContentElement::find($id);
        if($parentData){
            $newParent =new PostContentElement();
            $newParent->src_id=$parentData->src_id;
            $newParent->parent_id=$parentData->parent_id;
            $newParent->drag=$parentData->drag;
            $newParent->item_type=$parentData->item_type;
            $newParent->grid_column=$parentData->grid_column;
            $newParent->class_name=$parentData->class_name;
            $newParent->type=$parentData->type;
            $newParent->status='active';
            $newParent->save();
            
            foreach($parentData->subItems as $i=>$item){
                $data =new PostContentElement();
                $data->parent_id=$newParent->id;
                $data->name=$item->name;
                $data->content=$item->content;
                $data->drag=$item->drag;
                $data->item_type=$item->item_type;
                $data->grid_column=$item->grid_column;
                $data->class_name=$item->class_name;
                $data->type=$item->type;
                $data->status='active';
                $data->save();
            }
            
        }
    }
    
    if($action=='deleteSection'){
        $data =PostContentElement::find($id);
        if($data){
          $data->delete();
        }
    }
    
    if($action=='serialSection'){
        if($r->serialIds){
            for($i=0;$i < count($r->serialIds); $i++){
                $data =PostContentElement::find($r->serialIds[$i]);
                if($data){
                    $data->drag=$i;
                    $data->save();
                }
            }
        }
    }
    
    if($action=='addItem'){
        $parentData =PostContentElement::find($id);
        if($parentData){
            $data =new PostContentElement();
            $data->parent_id=$parentData->id;
            $data->item_type=$r->itemtype;
            $data->type=0;
            $data->status='active';
            $data->save();
        }
    }
    
    if($action=='updateItem'){
    $data =PostContentElement::find($id);
    if($data){
        
        if($r->itemtype=='paragraph'){
            $check = $r->validate([
                'gridColumn' => 'nullable|numeric',
                'paraText' => 'nullable|max:10000',
            ]);
            
            $data->content=$r->paraText;
            $data->class_name=$r->className;
            $data->save();
        }elseif($r->itemtype=='heading'){
            $check = $r->validate([
                'gridColumn' => 'nullable|numeric',
                'headingName' => 'nullable|max:10',
                'headingText' => 'nullable|max:1000',
            ]);
            $data->name=$r->headingName?:'h1';
            $data->content=$r->headingText;
            $data->class_name=$r->className;
            $data->save();
        }elseif($r->itemtype=='image'){
            $check = $r->validate([
                'gridColumn' => 'nullable|numeric',
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:10240',
            ]);
            
            ///////Image UploadStart////////////
              if($r->hasFile('image')){
        
                $file =$r->image;
                $src  =$data->id;
                $srcType  =8;
                $fileUse  =1;
                $author =Auth::id();
                uploadFile($file,$src,$srcType,$fileUse,$author);
              }
            ///////Image Upload End////////////
        
            $data->class_name=$r->className;
            $data->save();
        }elseif($r->itemtype=='gridColumn'){
            $check = $r->validate([
                'gridColumn' => 'nullable|numeric',
                'className' => 'nullable|max:100',
            ]);
            
            $data->grid_column=$r->gridColumn?:12;
            $data->class_name=$r->className;
            $data->save();
        }elseif($r->itemtype=='serial'){
            $check = $r->validate([
                'serial' => 'nullable|numeric',
                'className' => 'nullable|max:100',
            ]);
            
            $data->drag=$r->serial?:0;
            $data->class_name=$r->className;
            $data->save();
        }
        
    }
    }
    
    if($action=='copyItem'){
        $parentData =PostContentElement::find($id);
        if($parentData){
            $newParent =new PostContentElement();
            $newParent->src_id=$parentData->src_id;
            $newParent->parent_id=$parentData->parent_id;
            $newParent->name=$parentData->name;
            $newParent->content=$parentData->content;
            $newParent->drag=$parentData->drag;
            $newParent->item_type=$parentData->item_type;
            $newParent->grid_column=$parentData->grid_column;
            $newParent->class_name=$parentData->class_name;
            $newParent->type=$parentData->type;
            $newParent->status='active';
            $newParent->save();
        }
        
        foreach($parentData->subItems as $i=>$item){
            $data =new PostContentElement();
            $data->parent_id=$newParent->id;
            $data->name=$item->name;
            $data->content=$item->content;
            $data->drag=$item->drag;
            $data->item_type=$item->item_type;
            $data->grid_column=$item->grid_column;
            $data->class_name=$item->class_name;
            $data->type=$item->type;
            $data->status='active';
            $data->save();
        }
        
        
    }
    if($action=='deleteItem'){
    $data =PostContentElement::find($id);
    if($data){
        $this->deleteItemAndSubItems($data);
    }
    }
    
    if($r->ajax()){
        return Response()->json([
              'success' => true
          ]);
    }

    Session()->flash('success','You Are Successfully Done!');
    return redirect()->back();

  }
  
    public function deleteItemAndSubItems($item) {
        // Delete related media files
        // $media = Media::where('src_type', 3)->where('src_id', $item->id)->get();
        // foreach ($media as $m) {
        //     if (File::exists($m->file_url)) {
        //         File::delete($m->file_url);
        //     }
        //     $m->delete();
        // }
    
        // Delete the item itself
        $item->delete();
    
        // Recursively delete sub-items
        foreach ($item->subItems as $sub) {
            $this->deleteItemAndSubItems($sub);
        }
    }
    


}
