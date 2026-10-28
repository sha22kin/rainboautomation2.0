@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Apps Documents')}}</title>
@endsection

@push('css')
<style type="text/css">

</style>
@endpush
@section('contents')


<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
     <h3 class="content-header-title mb-0">Apps Documents</h3>
     <div class="row breadcrumbs-top">
       <div class="breadcrumb-wrapper col-12">
         <ol class="breadcrumb">
           <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a>
           </li>
           <li class="breadcrumb-item active">Apps Documents</li>
         </ol>
       </div>
     </div>
   </div>
   <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
     <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
       	<a class="btn btn-outline-primary reloadPage" href="javascript:void(0)">
       		<i class="fa-solid fa-rotate"></i>
       	</a>
     </div>
   </div>
</div>
 
 <div class="content-body"><!-- Basic Elements start -->
	 <section class="basic-elements">
	     <div class="row">
	         <div class="col-md-12">
	             <div class="card">
	                 <div class="card-content">
	                     <div class="card-body">
	                        <h1>Apps Documents white...</h1>
	                     </div>
	                 </div>
	             </div>


	         </div>
	     </div>
	 </section>
	 <!-- Basic Inputs end -->
</div>



@endsection
@push('js')

@endpush