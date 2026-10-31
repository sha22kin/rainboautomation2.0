<?php

namespace App\Traits;
use App\Models\Permission;
use Auth;
trait UserPermission{
	public function checkRequestPermission(){

		if($activeRole =Permission::find(Auth::user()->permission_id)){
			if(
				
				empty(json_decode($activeRole->permission, true)['posts']['list']) && \Route::is('admin.posts')||
				empty(json_decode($activeRole->permission, true)['posts']['add']) && \Route::is('admin.postsCreate') ||
				empty(json_decode($activeRole->permission, true)['posts']['add']) && \Route::is('admin.postsUpdate') ||
				empty(json_decode($activeRole->permission, true)['posts']['delete']) && \Route::is('admin.postsDelete') ||

				empty(json_decode($activeRole->permission, true)['postsOther']['category']) && \Request::is('admin/website/posts/categories*')||
				empty(json_decode($activeRole->permission, true)['postsOther']['tags']) && \Request::is('admin/website/posts/tags*')||
				empty(json_decode($activeRole->permission, true)['postsOther']['comments']) && \Request::is('admin/website/posts/comments*')||

				empty(json_decode($activeRole->permission, true)['pages']['list']) && \Route::is('admin.pages')||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.pagesCreate') ||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.pagesUpdate') ||
				empty(json_decode($activeRole->permission, true)['pages']['delete']) && \Route::is('admin.pagesDelete') ||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.contactLocations') ||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.homeWhy') ||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.homeWhyAction') ||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.industrialCapabilities') ||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.trainingCourses') ||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.trainingCoursesAction') ||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.trainingInquiries') ||
				empty(json_decode($activeRole->permission, true)['pages']['add']) && \Route::is('admin.trainingInquiriesAction') ||

				empty(json_decode($activeRole->permission, true)['services']['list']) && \Route::is('admin.services')||
				empty(json_decode($activeRole->permission, true)['services']['add']) && \Route::is('admin.servicesCreate') ||
				empty(json_decode($activeRole->permission, true)['services']['add']) && \Route::is('admin.servicesUpdate') ||
				empty(json_decode($activeRole->permission, true)['services']['delete']) && \Route::is('admin.servicesDelete') ||

				empty(json_decode($activeRole->permission, true)['servicesOthers']['category']) && \Request::is('admin/website/services/categories*') ||

				empty(json_decode($activeRole->permission, true)['medies']['list']) && \Route::is('admin.medies')||
				empty(json_decode($activeRole->permission, true)['medies']['add']) && \Route::is('admin.mediesCreate') ||
				empty(json_decode($activeRole->permission, true)['medies']['add']) && \Route::is('admin.mediesUpdate') ||
				empty(json_decode($activeRole->permission, true)['medies']['delete']) && \Route::is('admin.mediesDelete') || 

				empty(json_decode($activeRole->permission, true)['clients']['list']) && \Route::is('admin.clients')||
				empty(json_decode($activeRole->permission, true)['clients']['add']) && \Route::is('admin.clientsCreate') ||
				empty(json_decode($activeRole->permission, true)['clients']['add']) && \Route::is('admin.clientsUpdate') ||
				empty(json_decode($activeRole->permission, true)['clients']['delete']) && \Route::is('admin.clientsDelete') ||

				empty(json_decode($activeRole->permission, true)['brands']['list']) && \Route::is('admin.brands')||
				empty(json_decode($activeRole->permission, true)['brands']['add']) && \Route::is('admin.brandsCreate') ||
				empty(json_decode($activeRole->permission, true)['brands']['add']) && \Route::is('admin.brandsUpdate') ||
				empty(json_decode($activeRole->permission, true)['brands']['delete']) && \Route::is('admin.brandsDelete') ||

				empty(json_decode($activeRole->permission, true)['sliders']['list']) && \Route::is('admin.sliders')||
				empty(json_decode($activeRole->permission, true)['sliders']['add']) && \Route::is('admin.slidersCreate') ||
				empty(json_decode($activeRole->permission, true)['sliders']['add']) && \Route::is('admin.slidersUpdate') ||
				empty(json_decode($activeRole->permission, true)['sliders']['add']) && \Route::is('admin.slideCreate') ||
				empty(json_decode($activeRole->permission, true)['sliders']['add']) && \Route::is('admin.slideUpdate') ||
				empty(json_decode($activeRole->permission, true)['sliders']['add']) && \Route::is('admin.slideDrug') ||
				empty(json_decode($activeRole->permission, true)['sliders']['delete']) && \Route::is('admin.slidersDelete') ||

				empty(json_decode($activeRole->permission, true)['galleries']['list']) && \Route::is('admin.galleries')||
				empty(json_decode($activeRole->permission, true)['galleries']['add']) && \Route::is('admin.galleriesCreate') ||
				empty(json_decode($activeRole->permission, true)['galleries']['add']) && \Route::is('admin.galleriesUpdate') ||
				empty(json_decode($activeRole->permission, true)['galleries']['add']) && \Route::is('admin.galleriesImagesCreate') ||
				empty(json_decode($activeRole->permission, true)['galleries']['add']) && \Route::is('admin.galleriesImagesUpdate') ||
				empty(json_decode($activeRole->permission, true)['galleries']['delete']) && \Route::is('admin.galleriesDelete') ||

				empty(json_decode($activeRole->permission, true)['menus']['list']) && \Route::is('admin.menus')||
				empty(json_decode($activeRole->permission, true)['menus']['add']) && \Route::is('admin.menusCreate') ||
				empty(json_decode($activeRole->permission, true)['menus']['add']) && \Route::is('admin.menusUpdate') ||
				empty(json_decode($activeRole->permission, true)['menus']['delete']) && \Route::is('admin.menusItemsDelete') ||
				empty(json_decode($activeRole->permission, true)['menus']['delete']) && \Route::is('admin.menusDelete') ||

				empty(json_decode($activeRole->permission, true)['users']['list']) && \Route::is('admin.customerUsers')||
				empty(json_decode($activeRole->permission, true)['users']['add']) && \Route::is('admin.usersCustomerAdd') ||
				empty(json_decode($activeRole->permission, true)['users']['add']) && \Route::is('admin.usersCustomerPost') ||
				empty(json_decode($activeRole->permission, true)['users']['update']) && \Route::is('admin.usersCustomerUpdate') ||
				empty(json_decode($activeRole->permission, true)['users']['delete']) && \Route::is('admin.usersCustomerDelete') ||

				empty(json_decode($activeRole->permission, true)['adminUsers']['list']) && \Route::is('admin.adminUsers')||
				empty(json_decode($activeRole->permission, true)['adminUsers']['add']) && \Route::is('admin.adminUsersCreate') ||
				empty(json_decode($activeRole->permission, true)['adminUsers']['add']) && \Route::is('admin.adminUsersPost') ||
				empty(json_decode($activeRole->permission, true)['adminUsers']['suspend']) && \Route::is('admin.adminUsersSuspend') ||

				empty(json_decode($activeRole->permission, true)['adminRoles']['list']) && \Route::is('admin.userRoles') ||
				empty(json_decode($activeRole->permission, true)['adminRoles']['add']) && \Route::is('admin.userRoleAction') ||
				empty(json_decode($activeRole->permission, true)['adminRoles']['delete']) && \Route::is('admin.usersRolesDelete') ||

				empty(json_decode($activeRole->permission, true)['appsSetting']['general']) && \Request::is('admin/website/setting/general*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['general']) && \Request::is('admin/website/setting/logo*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['general']) && \Request::is('admin/website/setting/favicon*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['mail']) && \Request::is('admin/website/setting/mail*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['sms']) && \Request::is('admin/website/setting/sms*') ||
				empty(json_decode($activeRole->permission, true)['appsSetting']['social']) && \Request::is('admin/website/setting/social*')


			){
				return abort('401');
			}
		}
	}
}