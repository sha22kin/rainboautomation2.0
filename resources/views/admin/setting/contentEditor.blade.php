@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Apps Documents')}}</title>
@endsection

@push('css')
<style type="text/css">
    .addSection {
        text-align: center;
        border: 1px solid gray;
        padding: 10px 15px;
        border-radius: 2px;
        background: #d9f1fb;
        cursor: pointer;
    }
    ul.sectionList {
        margin: 0;
        padding: 0;
        list-style: none;
    }

    ul.sectionList li {
        border: 1px solid #c7c7c7;
        margin: 10px 0;
        padding: 0px;
    }
    .sectionItemsList{
        min-height:100px;
    }
    .sectionTools{
        text-align:right;
    }
    .itemToolList ul {
        padding: 0;
        margin: 0;
        list-style: none;
    }
    
    .itemToolList ul li {
        float: left;
        width: 138px;
        height: 80px;
        border: 1px solid #e5e5e5;
        margin: 5px;
        text-decoration: none;
        text-align: center;
        position: relative;
        cursor: pointer;
    }
    
    .itemToolList ul li .title {
        border-bottom: 1px solid #e5e5e5;
        background: #ededed;
    }
    .itemToolList ul li .icon {
        padding: 10px;
    }
    
    .itemToolList ul li .icon i {
        font-size: 30px;
        color: #545457;
    }
    
    .itemGrid {
        margin: 5px;
        border: 1px solid #dce7f7;
    }
    
    .itemGrid .icon {
        text-align: center;
        padding: 15px;
    }
    
    .itemGrid .icon .font-title {
        display: inline;
        font-size: 16px;
    }
    
    .itemGrid .icon .font {
        font-size: 20px;
    }
    
    .itemGrid .title {
        background: #dce7f7;
        padding: 5px;
        padding-top: 0;
    }
    .itemTools{
        text-align:right;
    }
    .columnAddItem {
        background: #f4f6f9;
        padding: 1px 10px;
        font-size: 14px;
        cursor: pointer;
        border: 1px solid #b9bec6;
    }
    .cloppaseArrow {
        cursor: pointer;
        position: relative;
    }
    .cloppaseArrow:before {
        content: "\f105";
        font-family: 'FontAwesome';
        font-size: 1rem;
        display: inline-block;
        position: absolute;
        right: 10px;
        transform: rotate(90deg);
    }
    .cloppaseArrow.active:before{
        transform: rotate(0deg);
    }
    .sectionItemsList.hide {
        display: none;
    }
    .portlet-placeholder {
        border: 1px dotted black;
        margin: 0 1em 1em 0;
        height: 50px;
    }
</style>
@endpush
@section('contents')


<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
     <h3 class="content-header-title mb-0">Content Editor</h3>
     <div class="row breadcrumbs-top">
       <div class="breadcrumb-wrapper col-12">
         <ol class="breadcrumb">
           <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a>
           </li>
           <li class="breadcrumb-item active">Content Editor</li>
         </ol>
       </div>
     </div>
   </div>
   <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
     <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
       	<a class="btn btn-outline-primary reloadPage" 
       	@if($action=='page')
       	href="{{route('pageView',$primary->slug?:'no-title')}}" target="_blank"
       	@else
       	href="javascript:void(0)"
       	@endif
       	>Preview</a>
       	<a class="btn btn-outline-primary reloadPage" href="javascript:void(0)">
       		<i class="fa-solid fa-rotate"></i>
       	</a>
     </div>
   </div>
</div>
 
 <div class="content-body"><!-- Basic Elements start -->
    @include(adminTheme().'alerts')
	 <section class="basic-elements">
	     <div class="row">
	         <div class="col-md-12">
	             <div class="card">
	                 <div class="card-content">
	                     <div class="card-body">
                            <div class="addSection">
                                <a href="{{route('admin.contentEditorAction',['addSection',$primary->id])}}" ><i class="fa fa-plus"></i> Add Section</a>
                            </div>

                            <ul class="sectionList column2" data-url="{{route('admin.contentEditorAction',['serialSection',$primary->id])}}">
                            @foreach($datas as $i=>$data)
                                <li>
                                    <div class="row" style="padding: 5px;background: #efecec;margin: 0;">
                                        <div class="col-md-4" style="padding:3px;">
                                            <div class="input-group">
                                                <div class="input-group-append">
                                                    <span class="input-group-text portlet-header2" data-id="{{$data->id}}" style="cursor:grab;"><i class="fas fa-bars"></i></span>
                                                    <span class="input-group-text" data-toggle="modal" data-target="#AddItem_{{$data->id}}" style="cursor:pointer;">Add Item</span>
                                                    <span class="input-group-text cloppaseArrow" data-class="sectionItemsList_{{$data->id}}"><i class="fas fa-atrow-down"></i></span>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-9 col-md-4" style="padding:3px;">
                                        </div>
                                        <div class="col-3 col-md-4 sectionTools" style="padding:3px;">
                                            <span class="badge badge-success" data-toggle="modal" data-target="#editSection_{{$data->id}}" style="cursor:pointer;" > <i class="fa fa-edit"></i></span>
                                            <a href="{{route('admin.contentEditorAction',['copySection',$data->id])}}" class="badge badge-info" onclick="return confirm('Are You Want To Change Status?')"> <i class="fa fa-copy"></i></a>
                                            <span class="badge badge-primary" onclick="return confirm('Are You Want To Change Status?')"> <i class="fa fa-eye"></i></span>
                                            <a href="{{route('admin.contentEditorAction',['deleteSection',$data->id])}}" class="badge badge-danger" onclick="return confirm('Are You Want To Delete?')"> <i class="fa fa-trash"></i></a>
                                        </div>
                                    </div>
                                    <div class="sectionItemsList sectionItemsList_{{$data->id}}">
                                        <div class="row column" data-id="{{$data->id}}" style="margin:0 -5px;">
                                        @foreach($data->subItems as $item)
                                        @if($item->item_type=='paragraph')
                                            @include(adminTheme().'setting.includes.paragraphItem')
                                        @elseif($item->item_type=='heading')
                                            @include(adminTheme().'setting.includes.headingItem')
                                        @elseif($item->item_type=='image')
                                            @include(adminTheme().'setting.includes.imageItem')
                                        @elseif($item->item_type=='gridColumn')
                                            @include(adminTheme().'setting.includes.gridColumnItem')
                                        @endif
                                        @endforeach
                                        </div>
                                    </div>
                                    <!-- Modal -->
                                    <div class="modal fade text-left" id="editSection_{{$data->id}}" tabindex="-1" >
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                <div class="modal-header" style="padding: 5px 15px;background: #edeff6;">
                                                    <h4 class="modal-title" id="myModalLabel1">Section Edit</h4>
                                                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                        <span aria-hidden="true">&times; </span>
                                                    </button>
                                                </div>
                                                <div class="modal-body">
                                                    <form action="{{route('admin.contentEditorAction',['updateItem',$data->id,'itemtype'=>'serial'])}}" method="post">
                                                        @csrf
                                                        <div class="table-responsive">
                                                           <table class="table table-borderless">
                                                               <tr>
                                                                   <th style="width: 200px;min-width: 200px;" >Class <small>(style CSS)</small></th>
                                                                   <td>
                                                                       <input type="text" name="className" value="{{$data->class_name}}" class="form-control form-control-sm" placeholder="Enter Class Name">
                                                                   </td>
                                                               </tr>
                                                               <tr>
                                                                   <th style="width: 200px;min-width: 200px;" >Position Serial</small></th>
                                                                   <td>
                                                                       <select class="form-control form-control-sm" name="serial">
                                                                           @foreach($datas as $i=>$dd)
                                                                            <option value="{{$i}}" {{$data->drag==$i?'selected':''}} >{{$i+1}} Serial</option>
                                                                           @endforeach
                                                                       </select>
                                                                   </td>
                                                               </tr>
                                                               <tr>
                                                                   <th>Action</th>
                                                                   <td>
                                                                       <button class="btn btn-md btn-info"> Submit</button>
                                                                   </td>
                                                               </tr>
                                                           </table>
                                                       </div>
                                                   </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Modal -->
                                    <div class="modal fade text-left" id="AddItem_{{$data->id}}" tabindex="-1" >
                                        <div class="modal-dialog modal-lg" role="document">
                                            <div class="modal-content">
                                                    <div class="modal-header" style="padding: 5px 15px;background: #edeff6;">
                                                        <h4 class="modal-title" id="myModalLabel1">Item List</h4>
                                                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                            <span aria-hidden="true">&times; </span>
                                                        </button>
                                                    </div>
                                                    <div class="modal-body">
                                                       <div class="itemToolList">
                                                           <ul>
                                                               <li data-url="{{route('admin.contentEditorAction',['addItem',$data->id,'itemtype'=>'paragraph'])}}">
                                                                   <div class="title">
                                                                       <span>Text</span>
                                                                   </div>
                                                                   <div class="icon">
                                                                       <i class="fa fa-edit"></i>
                                                                   </div>
                                                               </li>
                                                               <li data-url="{{route('admin.contentEditorAction',['addItem',$data->id,'itemtype'=>'heading'])}}">
                                                                   <div class="title">
                                                                       <span>Heading</span>
                                                                   </div>
                                                                   <div class="icon">
                                                                       <i class="fa fa-header " aria-hidden="true"></i>
                                                                   </div>
                                                               </li>
                                                               <li data-url="{{route('admin.contentEditorAction',['addItem',$data->id,'itemtype'=>'image'])}}">
                                                                   <div class="title">
                                                                       <span>Image</span>
                                                                   </div>
                                                                   <div class="icon">
                                                                       <i class="fa fa-image"></i>
                                                                   </div>
                                                               </li>
                                                               <li data-url="{{route('admin.contentEditorAction',['addItem',$data->id,'itemtype'=>'gridColumn'])}}" >
                                                                   <div class="title">
                                                                       <span>Grid Column</span>
                                                                   </div>
                                                                   <div class="icon">
                                                                       <i class="fa fa-columns"></i>
                                                                   </div>
                                                               </li>
                                                           </ul>
                                                       </div>
                                                    </div>
                                            </div>
                                        </div>
                                    </div>
                                </li>
                            @endforeach
                            </ul>
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
<script>

    $(".column2").sortable({
      handle: ".portlet-header2",
      placeholder: "portlet-placeholder",
      update: function (event, ui) {
            var dataId = [];
            $(this).find('.portlet-header2').each(function (index) {
                dataId.push($(this).data('id'));
            });
            //alert(dataId);
            var url =$(this).data('url');
            
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data:{serialIds:dataId},
              success : function(data){
                 // location.reload();
              },error: function () {
                  alert('error');
                }
            });
            
        },
    });
    
    $(".column").sortable({
      connectWith: ".column",
      handle: ".portlet-header",
      placeholder: "sortable-placeholder",
      update: function (event, ui) {
        // Get serialized data for the specific sortable group
        var serializedData = $(this).sortable("toArray", {
          attribute: "data-id",
        });
        console.log(serializedData);
      },
    });
    

    $(document).ready(function(){
        $(document).on('click','.itemToolList ul li',function(){
            var url =$(this).data('url');
            if(url){
                $.ajax({
                  url:url,
                  dataType: 'json',
                  cache: false,
                  //data:{addmenuitem:id,addtype:type,parentItem:parentItem,menulocation:menulocation},
                   success : function(data){
                    //
                     location.reload();
                   },error: function () {
                      alert('error');
                    }
                });
            }
        
        });
    });
</script>
<script>
    $(".paragraphEditor").summernote({
        placeholder: "Write Your Contents",
        tabsize: 2,
        height: 120,
        toolbar: [
            ["font", ["bold", "underline"]],
            ["color", ["color"]],
            ["para", ["ul", "ol", "paragraph"]],
            ["insert", ["link", "picture"]],
            ["view", ["fullscreen", "codeview"]],
        ],
    });
    
    $(".headingEditor").summernote({
        placeholder: "Write Your Heading",
        tabsize: 2,
        height: 120,
        toolbar: [
            ["font", ["bold", "underline"]],
            ["color", ["color"]],
        ],
    });
    
    
    $(document).ready(function(){
        $('.cloppaseArrow').click(function(){
            $(this).toggleClass('active');
            var sectionClass ='.'+$(this).data('class');
            $(sectionClass).toggleClass('hide');
        });
        
        // When a file is selected, update the image preview
        $(document).on('change','.imageuploadItem', function (e) {
            var input = e.target;
            var profiImage ='.'+$(this).data('imageshow');

            if (input.files && input.files[0]) {
                var reader = new FileReader();

                reader.onload = function (e) {
                    $(profiImage).attr('src', e.target.result);
                };

                reader.readAsDataURL(input.files[0]);
            }
        });
        
        
    });
    
    
</script>
@endpush