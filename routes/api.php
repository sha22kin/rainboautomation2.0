<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\ApiWelcomeController;
use App\Http\Controllers\Api\ApiAuthController;
use App\Http\Controllers\Api\ApiUserController;

Route::get('/general',[ApiWelcomeController::class,'generalInfo'])->name('generalInfo');
Route::get('/slider',[ApiWelcomeController::class,'slider'])->name('slider');
Route::post('/subscribe',[ApiWelcomeController::class,'subscribe'])->name('subscribe');
Route::post('/contact-mail',[ApiWelcomeController::class,'contactMail'])->name('contactMail');
Route::get('/geo/filter/{id}',[ApiWelcomeController::class,'geo_filter'])->name('geo_filter');

Route::get('/home-content',[ApiWelcomeController::class,'homeContent'])->name('homeContent');
Route::get('/menu/{location?}',[ApiWelcomeController::class,'menu'])->name('menu');
Route::get('/page/{slug?}',[ApiWelcomeController::class,'pageView'])->name('pageView');

Route::get('/blog/category/{slug}',[ApiWelcomeController::class,'blogCategory'])->name('blogCategory');
Route::get('/blog/{slug?}',[ApiWelcomeController::class,'blogView'])->name('blogView');

Route::get('/service/category/{slug}',[ApiWelcomeController::class,'serviceCategory'])->name('serviceCategory');
Route::get('/service/{slug}',[ApiWelcomeController::class,'serviceView'])->name('serviceView');

Route::post('/login',[ApiAuthController::class,'login']);
Route::any('/registration',[ApiAuthController::class,'registration']);
Route::post('/forget/password',[ApiAuthController::class,'forgotPassword']);
Route::post('/reset/password',[ApiAuthController::class,'resetPassword']);

Route::group(['middleware'=>'APIToken'], function(){
    
Route::post('/log-out',[ApiUserController::class,'logOut']);


});

Route::group(['prefix'=>'user','middleware'=>'APIToken'], function(){
    
    Route::any('/profile',[ApiUserController::class,'profile']);
    Route::post('/change-password',[ApiUserController::class,'changePassword']);

    
});

