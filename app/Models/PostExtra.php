<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PostExtra extends Model
{
    
    //Models Information Data
    /********
     * 
     * type ==0 : Page
     * type ==1 : Subscribe
     * type ==2 : Contact location (src_id = page id, name = label, content = JSON details)
     * type ==3 : Capability card (src_id = page id, name = title, content = JSON details)
     * type ==4 : Training course (src_id = page id, name = title, content = JSON details)
     * type ==5 : Course inquiry (src_id = course id, parent_id = page id, name, email, content = JSON details, status = unread/read)
     * type ==13 : Home page "Why Choose Us" section (one row, content = JSON heading + items, see homeWhyContent())
     *
     * ------------------------
     *  Status==
     * ------------------------
     * 
     * Column:
     * 
     * id            =bigint(20):None,
     * src_id        =bigint(20):null,
     * name          =varchar(191):null,
     * content       =text:null,
     * parent_id     =bigint(20):null,
     * drag          =int(5):0
     * type          =int(1):0
     * status        =varchar(10):null
     * fetured       =tinyint(1):null
     * addedby_id    =bigint(20):null
     * editedby_id   =bigint(20)::null
     * created_at    =timestamp:null
     * updated_at    =timestamp:null
     * 
     * 
     * 
     ****/

    // Contact location details (type 2): title, address, phones[], email, email_note, map
    public function location($key=null){
        $data = array_merge(
            ['title'=>null,'address'=>null,'phones'=>[],'email'=>null,'email_note'=>null,'map'=>null],
            json_decode($this->content ?: '[]', true) ?: []
        );
        return $key ? $data[$key] : $data;
    }

    // Card details (type 3 / 12): icon, title, description, color.
    // Services page core cards also carry the Home page "Our Core Services" card: badge, home_description,
    // points[], link_page (design route name / custom), link_url, link_text, home_color, home_order, show_services, show_home.
    public function card($key=null){
        $data = array_merge(
            ['icon'=>null,'title'=>$this->name,'description'=>null,'color'=>'#287dcc',
             'badge'=>null,'home_description'=>null,'points'=>[],'link_page'=>null,'link_url'=>null,'link_text'=>null,
             'home_color'=>null,'home_order'=>0,'show_services'=>true,'show_home'=>true],
            json_decode($this->content ?: '[]', true) ?: []
        );
        return $key ? $data[$key] : $data;
    }

    // Training course details (type 4): category, title, description, duration, fee, image, alt, modules[]
    public function course($key=null){
        $data = array_merge(
            ['category'=>'automation','title'=>$this->name,'description'=>null,'duration'=>null,'fee'=>null,'image'=>null,'alt'=>null,'modules'=>[]],
            json_decode($this->content ?: '[]', true) ?: []
        );
        return $key ? $data[$key] : $data;
    }

    // Course category settings (label, filter, color)
    public function courseCategory(){
        $categories = trainingCategories();
        return $categories[$this->course('category')] ?? reset($categories);
    }

    // Full course image URL (uploaded file or external link)
    public function courseImage(){
        $image = $this->course('image');
        if(!$image){
            return asset('medies/noimage.jpg');
        }
        return preg_match('#^https?://#i',$image) ? $image : asset($image);
    }

    // Course fee formatted like the design (৳ 12,000), or "On Inquiry"
    public function courseFee(){
        $fee = $this->course('fee');
        return is_numeric($fee) ? '৳ '.number_format($fee) : 'On Inquiry';
    }

    // Course inquiry details (type 5): phone, message, course_title, ip, stage (CMS follow-up: New, Follow-up, Converted)
    public function inquiry($key=null){
        $data = array_merge(
            ['phone'=>null,'message'=>null,'course_title'=>null,'ip'=>null,'stage'=>'New'],
            json_decode($this->content ?: '[]', true) ?: []
        );
        return $key ? $data[$key] : $data;
    }

    // Address used for the Google map / directions (the map field, or the address itself)
    public function mapQuery(){
        return rawurlencode($this->location('map') ?: $this->location('address'));
    }

}
