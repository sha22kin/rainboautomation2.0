<!DOCTYPE html>
 <html class="loading" lang="en">

   <head>
     <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
     <meta http-equiv="X-UA-Compatible" content="IE=edge" />
     <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=0, minimal-ui" />
     <meta name="description" content="" />
     <meta name="keywords" content="" />
     <meta name="author" content="Company Name" />
     <title>Coming Soon {{websiteTitle()}}</title>
     <link rel="apple-touch-icon" href="{{asset(general()->favicon())}}" />
     <link rel="shortcut icon" type="image/x-icon" href="{{asset(general()->favicon())}}" />

     <!-- BEGIN: Vendor CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/vendors/css/vendors.min.css')}}" />
     <!-- END: Vendor CSS-->

     <!-- BEGIN: Theme CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/bootstrap.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/bootstrap-extended.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/colors.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/components.min.css')}}" />
     <!-- END: Theme CSS-->

     <!-- BEGIN: Page CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/core/menu/menu-types/vertical-menu-modern.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/core/colors/palette-gradient.min.css')}}" />
     <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/pages/coming-soon.css')}}" />
     <!-- END: Page CSS-->

     <!-- BEGIN: Custom CSS-->
     <link rel="stylesheet" type="text/css" href="{{asset('admin/app-assets/css/style.css')}}" />
     <!-- END: Custom CSS-->

   </head>
   <!-- END: Head-->

   <!-- BEGIN: Body-->
   <body class="vertical-layout vertical-menu-modern 1-column  comingsoonFlat blank-page blank-page" data-open="click" data-menu="vertical-menu-modern" data-col="1-column">
     <!-- BEGIN: Content-->
     <div class="app-content content">
       <div class="content-overlay"></div>
       <div class="content-wrapper">
         <div class="content-header row">
         </div>
         <div class="content-body"><!-- coming soon flat design -->
             <section class="flexbox-container">
                 <div class="col-12 d-flex align-items-center justify-content-center p-0">
                     <div class="col-sm-10 col-12 p-0">
                         <div class="card card-transparent box-shadow-0 border-0">
                             <div class="card-content">
                                 <div class="text-center">
                                     <h5 class="card-text pb-2 white">WE ARE LAUNCHING SOON. </h5>
                                     <img src="{{asset(App\Models\General::first()->logo())}}" class="img-responsive block width-300 mx-auto" width="300" alt="bg-img" />
                                     <div id="clockFlat" class="card-text getting-started pt-1 mt-2 white d-sm-flex justify-content-center d-inline-block"></div>
                                     <div class="col-12 pt-1">
                                         <p class="card-text lead white">Our website is under Constraction. </p>
                                     </div>
                                     <div class="col-12 text-center pt-2">
                                            <p class="socialIcon card-text">
                                            <a href="#"><i class="fa fa-facebook-square white"></i></a>
                                            <a href="#"><i class="fa fa-twitter-square white"></i></a>
                                            <a href="#"><i class="fa fa-google-plus-square white"></i></a>
                                            <a href="#"><i class="fa fa-linkedin-square white"></i></a>
                                         </p>
                                     </div>
                                 </div>
                             </div>
                         </div>
                     </div>
                 </div>
             </section>
            <!--/ coming soon flat design -->
         </div>
       </div>
     </div>
     <!-- END: Content-->


     <!-- BEGIN: Vendor JS-->
     <script src="{{asset('admin/app-assets/vendors/js/vendors.min.js')}}"></script>
     <!-- BEGIN Vendor JS-->

     <!-- BEGIN: Page Vendor JS-->
     <script src="{{asset('admin/app-assets/vendors/js/coming-soon/jquery.countdown.min.js')}}"></script>
     <!-- END: Page Vendor JS-->

     <!-- BEGIN: Theme JS-->
     <script src="{{asset('admin/app-assets/js/core/app-menu.min.js')}}"></script>
     <script src="{{asset('admin/app-assets/js/core/app.min.js')}}"></script>
     <!-- END: Theme JS-->

     <!-- BEGIN: Page JS-->
     <script src="{{asset('admin/app-assets/js/scripts/coming-soon/coming-soon.js')}}"></script>
     <!-- END: Page JS-->

   </body>
   <!-- END: Body-->
 </html>