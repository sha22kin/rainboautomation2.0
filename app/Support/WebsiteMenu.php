<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Request;

// Options of the sidebar "Website" menu: the former website admin panel, grouped.
// Each item: [label, route, route params, active URL patterns, permission [group, key] or null]
class WebsiteMenu
{
    public static function groups(){
        return [
            'Overview' => [
                ['Website Overview', 'admin.dashboard', [], ['admin/website/dashboard'], null],
                ['My Profile', 'admin.myProfile', [], ['admin/website/my-profile'], null],
            ],
            'Pages' => [
                ['All Pages', 'admin.pages', [], ['admin/website/pages*'], ['pages', 'list']],
                ['Home: Why Choose Us', 'admin.homeWhy', [], ['admin/website/home-why-choose-us*'], ['pages', 'add']],
                ['Contact Locations', 'admin.contactLocations', [], ['admin/website/contact-locations*'], ['pages', 'add']],
                ['Industrial Solution', 'admin.industrialCapabilities', [], ['admin/website/industrial-capabilities*', 'admin/website/solution-*/industrial*'], ['pages', 'add']],
                ['Solar Solution', 'admin.solarSolution', [], ['admin/website/solar-solution*', 'admin/website/solution-*/solar*'], ['pages', 'add']],
                ['Services Page', 'admin.servicesPage', [], ['admin/website/services-page*', 'admin/website/solution-*/services*'], ['pages', 'add']],
            ],
            'Training Academy' => [
                ['Training Courses', 'admin.trainingCourses', [], ['admin/website/training-courses*'], ['pages', 'add']],
                ['Training Page Content', 'admin.trainingPage', [], ['admin/website/training-page'], ['pages', 'add']],
                ['Course Inquiries', 'admin.trainingInquiries', [], ['admin/website/training-inquiries*'], ['pages', 'add']],
            ],
            'Blog & Services' => [
                ['All Posts', 'admin.posts', [], ['admin/website/posts', 'admin/website/posts/edit*', 'admin/website/posts/create*'], ['posts', 'list']],
                ['Post Categories', 'admin.postsCategories', [], ['admin/website/posts/categories*'], ['postsOther', 'category']],
                ['Post Tags', 'admin.postsTags', [], ['admin/website/posts/tags*'], ['postsOther', 'tags']],
                ['Comments', 'admin.postsCommentsAll', [], ['admin/website/posts/comments*'], ['postsOther', 'comments']],
                ['All Services', 'admin.services', [], ['admin/website/services', 'admin/website/services/edit*', 'admin/website/services/create*'], ['services', 'list']],
                ['Service Categories', 'admin.servicesCategories', [], ['admin/website/services/categories*'], ['servicesOthers', 'category']],
            ],
            'Media & Appearance' => [
                ['Media Library', 'admin.medies', [], ['admin/website/medies*'], ['medies', 'list']],
                ['Sliders', 'admin.sliders', [], ['admin/website/sliders*'], ['sliders', 'list']],
                ['Galleries', 'admin.galleries', [], ['admin/website/galleries*'], ['galleries', 'list']],
                ['Clients', 'admin.clients', [], ['admin/website/clients*'], ['clients', 'list']],
                ['Brands', 'admin.brands', [], ['admin/website/brands*'], ['brands', 'list']],
                ['Menus Setting', 'admin.menus', [], ['admin/website/menus*'], ['menus', 'list']],
                ['Theme Setting', 'admin.themeSetting', [], ['admin/website/theme-setting*'], ['themeSetting', 'list']],
            ],
            'Users' => [
                ['Administrator Users', 'admin.usersAdmin', [], ['admin/website/users/admin*'], ['adminUsers', 'list']],
                ['Roles & Permissions', 'admin.userRoles', [], ['admin/website/users/roles*'], ['adminRoles', 'list']],
                ['Customer Users', 'admin.usersCustomer', [], ['admin/website/users/customer*'], ['users', 'list']],
                ['Subscribers', 'admin.subscribes', [], ['admin/website/subscribes*'], ['subscribe', 'list']],
            ],
            'Settings' => [
                ['General Setting', 'admin.setting', ['general'], ['admin/website/setting/general*'], ['appsSetting', 'general']],
                ['Mail Setting', 'admin.setting', ['mail'], ['admin/website/setting/mail*'], ['appsSetting', 'mail']],
                ['SMS Setting', 'admin.setting', ['sms'], ['admin/website/setting/sms*'], ['appsSetting', 'sms']],
                ['Social Setting', 'admin.setting', ['social'], ['admin/website/setting/social*'], ['appsSetting', 'social']],
                ['Website Translations', 'admin.translations', [], ['admin/website/translations*'], ['appsSetting', 'general']],
            ],
        ];
    }

    // Role permissions of the signed in user (website Roles & Permissions)
    public static function permissions(){
        $role = Auth::user()?->permission;
        return $role ? (json_decode($role->permission, true) ?: []) : [];
    }

    public static function can($group, $key){
        return isset(static::permissions()[$group][$key]);
    }

    // Groups with only the items this user may open
    public static function visibleGroups(){
        $hasRole = (bool) Auth::user()?->permission;
        $groups = [];
        foreach(static::groups() as $name => $items){
            $items = array_values(array_filter($items, fn ($item) => $item[4] === null || ($hasRole && static::can(...$item[4]))));
            if($items){
                $groups[$name] = $items;
            }
        }
        return $groups;
    }

    public static function isActive($item){
        return Request::is(...$item[3]);
    }

    // Label of the open Website page, for the top bar title
    public static function activeLabel(){
        foreach(static::groups() as $items){
            foreach($items as $item){
                if(static::isActive($item)){
                    return $item[0];
                }
            }
        }
        return 'Website';
    }
}
