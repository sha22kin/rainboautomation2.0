@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Slide Edit')}}</title>
@endsection

@push('css')
<style type="text/css">

</style>
@endpush
@section('contents')


<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
     <h3 class="content-header-title mb-0">Slide Edit</h3>
     <div class="row breadcrumbs-top">
       <div class="breadcrumb-wrapper col-12">
         <ol class="breadcrumb">
           <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a>
           </li>
           <li class="breadcrumb-item active">Slide Edit</li>
         </ol>
       </div>
     </div>
   </div>
   <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
     <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
       	<a class="btn btn-outline-primary" href="{{route('admin.slidersAction',['edit',$slide->parent_id])}}">BACK</a>
       	<a class="btn btn-outline-primary reloadPage" href="javascript:void(0)">
       		<i class="fa-solid fa-rotate"></i>
       	</a>
     </div>
   </div>
</div>
 
	

 <div class="content-body">
 	<!-- Basic Elements start -->
	 <section class="basic-elements">
	 	@include(adminTheme().'alerts')
	    <form action="{{route('admin.slideAction',['update',$slide->id])}}" method="post" enctype="multipart/form-data">
	    	@csrf
	     <div class="row">

	     		<div class="col-md-8">
			     	<div class="card">
		             	<div class="card-header " style="border-bottom: 1px solid #e3ebf3;">
						 	<h4 class="card-title">Slide Edit</h4>
					 	</div>
		                <div class="card-content">
		                    <div class="card-body">
			                    <div class="form-group">
			                     	<label for="name">Slide Name</label>
			                     	<input type="text" class="form-control {{$errors->has('name')?'error':''}}" name="name" placeholder="Enter Slider Name" value="{{$slide->name?:old('name')}}"  />
			                    	@if ($errors->has('name'))
			                    	<p style="color: red;margin: 0;font-size: 10px;">{{ $errors->first('name') }}</p>
			                    	@endif
					             		</div>
										 
					             		<div class="form-group">
											<label for="description">Description </label>
											<textarea name="description" rows="8" class="form-control {{$errors->has('description')?'error':''}}" placeholder="Enter Description">{!!$slide->description!!}</textarea>
											@if ($errors->has('description'))
											<p style="color: red;margin: 0;font-size: 10px;">{{ $errors->first('description') }}</p>
											@endif
			             				</div>

										<div class="row">
											<div class="form-group col-md-6">
												<label for="buttonText">Button Text </label>
												<input type="text" class="form-control {{$errors->has('buttonText')?'error':''}}" name="buttonText" placeholder="Enter Button Text" value="{{$slide->seo_title?:old('buttonText')}}" />
												@if ($errors->has('buttonText'))
												<p style="color: red;margin: 0;font-size: 10px;">{{ $errors->first('buttonText') }}</p>
												@endif
											</div>
											<div class="form-group col-md-6">
												<label for="buttonLink">Button Link </label>
												<input type="text" class="form-control {{$errors->has('buttonLink')?'error':''}}" name="buttonLink" placeholder="Enter Button Link" value="{{$slide->seo_description?:old('buttonLink')}}"  />
												@if ($errors->has('buttonLink'))
												<p style="color: red;margin: 0;font-size: 10px;">{{ $errors->first('buttonLink') }}</p>
												@endif
											</div>
										</div>
		                    </div>
		                 </div>
			     	</div>
				</div>
				<div class="col-md-4">
					<div class="card">
		             	<div class="card-header " style="border-bottom: 1px solid #e3ebf3;">
						 	<h4 class="card-title">Slide Layer</h4>
					 	</div>
		                <div class="card-content">
		                    <div class="card-body">
		                    	<div class="form-group">
	            					<label for="image">Slide Image</label>
	            					<input type="file" name="image" class="form-control {{$errors->has('image')?'error':''}}" >
	            					@if ($errors->has('image'))
	                              	<p style="color: red;margin: 0;font-size: 10px;">{{ $errors->first('image') }}</p>
	                               	@endif
	                			</div>
	                    		<div class="form-group">
	                				<img src="{{asset($slide->image())}}" style="max-width: 100px;">
	                				@if($slide->imageFile)
	                				<a href="{{route('admin.mediesDelete',$slide->imageFile->id)}}" class="mediaDelete" style="color:red;"><i class="fa fa-trash"></i></a>
	                				@endif
	                			</div>
	            				<div class="row">
		                     		<div class="form-group col-6">
		                    			<label for="status">Slider Status</label>
						               	<div class="custom-control custom-checkbox">
							                 <input type="checkbox" class="custom-control-input" id="status" name="status"  {{$slide->status=='active'?'checked':''}}/>
							                 <label class="custom-control-label" for="status">Active</label>
						               </div>
			                        </div> 
			                    </div>
			                    @isset(json_decode(Auth::user()->permission->permission, true)['sliders']['add'])
	                          	<button type="submit" class="btn btn-primary mr-sm-1 mb-1 mb-sm-0">Save
		                                  changes </button>
		                      @endisset

		                    </div>
		                </div>
		            </div>
				</div>
		    </div>
		</form>
	 </section>
	 <!-- Basic Inputs end -->
</div>



@endsection
@push('js')


@endpush