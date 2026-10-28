<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostContentElement extends Model
{
    
    //Models Information Data
    /********
     * 
     * 
     * Column:
     * 
     * id            =bigint(20):None,
     * src_id        =bigint(20):null,
     * parent_id     =bigint(20):null,
     * name          =varchar(191):null,
     * drag          =int(5):0
     * type          =int(1):0
     * status        =varchar(10):null
     * created_at    =timestamp:null
     * updated_at    =timestamp:null
     * 
     * 
     * 
     ****/
    
    public function imageFile(){
        return $this->hasOne(Media::class,'src_id')->where('src_type',8)->where('use_Of_file',1);
    }

    public function image(){
        
        if($this->imageFile){
            return $this->imageFile->file_url;
        }else{
            return 'medies/noimage.jpg';
        }
    }
     
    public function subItems(){
        return $this->hasMany(PostContentElement::class,'parent_id')->orderBy('drag');
    }
     
     
     
     

}
