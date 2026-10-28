<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    
    //Models Information Data
    /********
     * 
     * type ==0 : Page
     * type ==1 : Post
     * type ==3 : Service
     * 
     * ------------------------
     *  Status==temp, active, inactive
     * ------------------------
     * 
     * Column:
     * 
     * id            =bigint(20):None,
     * name          =varchar(200):null,
     * slug          =varchar(250):null,
     * short_description =text:null,
     * description   =longText:null,
     * view          =bigint(20):0
     * type          =int(1):0
     * seo_title     =varchar(191):null
     * seo_desc      =text:null
     * seo_keyword   =text:null
     * search_key    =text:null
     * status        =varchar(10):null
     * featured       =tinyInt(1):null
     * addedby_id    =bigint(20):null
     * editedby_id   =bigint(20)::null
     * created_at    =timestamp:null
     * updated_at    =timestamp:null
     * 
     * 
     * 
     ****/


    //Image and Banner Functions Start
    /********
     * 
     * *********/

    public function imageFile(){
    	return $this->hasOne(Media::class,'src_id')->where('src_type',1)->where('use_Of_file',1);
    }

    public function image(){
        if($this->imageFile){
            return $this->imageFile->file_url;
        }else{
            return 'medies/noimage.jpg';
        }
    }
    
    public function imageName(){
        
        if($this->imageFile){
            return $this->imageFile->file_rename;
        }else{
            return 'noimage.jpg';
        }
    }

    public function bannerFile(){
        return $this->hasOne(Media::class,'src_id')->where('src_type',1)->where('use_Of_file',2);
    }

    public function banner(){
        if($this->bannerFile){
            return $this->bannerFile->file_url;
        }else{
            return 'medies/no-banner.png';
        }
    }
    
    public function bannerName(){
        
        if($this->bannerFile){
            return $this->bannerFile->file_rename;
        }else{
            return 'no-banner.png';
        }
    }

    public function galleryFiles(){
        return $this->hasMany(Media::class,'src_id')->where('src_type',1)->where('use_Of_file',3);
    }

    //Image and Banner Functions End


    //Post Category tag, comments Functions 
    public function postCtgs(){
        return $this->hasMany(PostAttribute::class,'src_id')->where('type',1)->orderBy('drag','asc');
    }

    // Office / training locations shown on the "Contact Us" design page (details are JSON in content)
    public function contactLocations(){
        return $this->hasMany(PostExtra::class,'src_id')->where('type',2)->orderBy('drag','asc');
    }

    // Service cards of the "Industrial Solution" (Industrial Automation Capabilities), "Solar Solution"
    // (Solar Power Solutions) and "Services" (Our Core Engineering Services) design pages (details are JSON in content)
    public function capabilityCards(){
        return $this->hasMany(PostExtra::class,'src_id')->where('type',3)->orderBy('drag','asc');
    }

    // "What We Can Support" cards of the "Services" design page (details are JSON in content)
    public function supportCards(){
        return $this->hasMany(PostExtra::class,'src_id')->where('type',12)->orderBy('drag','asc');
    }

    // Editable section texts of the "Industrial Solution" / "Solar Solution" / "Services" design pages (one row, JSON in content)
    public function sectionContent(){
        return $this->hasOne(PostExtra::class,'src_id')->where('type',6);
    }

    // Editable section texts of the "Training Academy" design page (one row, JSON in content)
    public function trainingContent(){
        return $this->hasOne(PostExtra::class,'src_id')->where('type',7);
    }

    // Courses shown on the "Training Academy" design page (details are JSON in content)
    public function trainingCourses(){
        return $this->hasMany(PostExtra::class,'src_id')->where('type',4)->orderBy('drag','asc');
    }

    public function postTags(){
        return $this->hasMany(PostAttribute::class,'src_id')->where('type',2)->orderBy('drag','asc');
    }

    public function postComments(){
        return $this->hasMany(Review::class,'src_id')->where('type',1);
    }
    
    public function postCategories(){
        return $this->belongsToMany(Attribute::class, PostAttribute::class,'src_id','reff_id')->wherePivot('type',1)->orderBy('drag', 'asc');
    }
    
     public function Tags(){
        return $this->belongsToMany(Attribute::class, PostAttribute::class,'src_id','reff_id')->wherePivot('type',2)->orderBy('drag', 'asc');
    }

    public function ctgPosts(){
        return $this->hasMany(PostAttribute::class,'src_id')->where('type',1);
    }

    public function tagPosts(){
        return $this->hasMany(PostAttribute::class,'src_id')->where('type',2);
    }

    public function relatedPosts(){
        return Post::whereHas('ctgPosts',function($q){
                    $q->whereIn('reff_id',$this->ctgPosts->pluck('reff_id'));
                })
                ->whereNot('id',$this->id)
                ->where('status','active')
                ->whereDate('created_at','<=',date('Y-m-d'));
 
    }

    //Post Category tag, comments Functions End


    //Services Category Functions 
    public function serviceCtgs(){
        return $this->hasMany(PostAttribute::class,'src_id')->where('type',0)->orderBy('drag','asc');
    }

    public function serviceCategories(){
        return $this->belongsToMany(Attribute::class, PostAttribute::class,'src_id','reff_id')->wherePivot('type',0)->orderBy('drag', 'asc');
    }

    public function ctgServices(){
        return $this->hasMany(PostAttribute::class,'src_id')->where('type',0);;
    }

    //Services Category Functions End
    
    public function hasNitEditorContent(){
    	return $this->hasMany(PostContentElement::class,'src_id')->where('parent_id',null)->orderBy('drag');
    }
    
    
    public function user(){
    	return $this->belongsTo(User::class,'addedby_id');
    }

    

    

    
}
