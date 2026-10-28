<div class="col-{{$item->grid_column}}" style="padding:5px;">
    <div class="itemGrid">
        <div class="title">
            <div class="row" style="margin:0;">
                <div class="col-6" style="padding:0;text-align: left;">
                    <span>Image</span>
                </div>
                <div class="col-6 itemTools" style="padding:0;">
                    <span class="portlet-header">Drag</span>
                    <span class="badge badge-success" data-toggle="modal" data-target="#editItem_{{$item->id}}" style="cursor:pointer;"> <i class="fa fa-edit"></i></span>
                    <a href="{{route('admin.contentEditorAction',['deleteItem',$item->id])}}" class="badge badge-danger" onclick="return confirm('Are You Want To Delete?')"> <i class="fa fa-trash"></i></a>
                </div>
            </div>
       </div>
       <div class="icon">
           <i class="font fa fa-image"></i>
           <span class="font-title">Image</span>
       </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade text-left" id="editItem_{{$item->id}}" tabindex="-1" >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 5px 15px;background: #edeff6;">
                    <h4 class="modal-title">Image Edit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times; </span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.contentEditorAction',['updateItem',$item->id,'itemtype'=>'image'])}}" method="post" enctype="multipart/form-data">
                        @csrf
                        <div class="table-responsive">
                           <table class="table table-borderless">
                               <tr>
                                   <th style="width: 200px;min-width: 200px;" >Class <small>(style CSS)</small></th>
                                   <td>
                                       <input type="text" name="className" value="{{$item->class_name}}" class="form-control form-control-sm" placeholder="Enter Class Name">
                                   </td>
                               </tr>
                               
                               <tr>
                                   <th>
                                       Upload Image
                                   </th>
                                   <td>
                                       <input type="file" class="form-control imageuploadItem" data-imageshow="imagePreview_{{$item->id}}" name="image">
                                   </td>
                               </tr>
                               <tr>
                                   <th>
                                       Preview
                                   </th>
                                   <td>
                                       <div class="imagePreview">
                                           <img src="{{asset($item->image())}}" class="imagePreview_{{$item->id}}" style="max-height: 100px;">
                                       </div>
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
</div>