<div class="col-{{$item->grid_column}}" style="padding:5px;">
    <div class="itemGrid">
        <div class="title">
            <div class="row" style="margin:0;">
                <div class="col-6" style="padding:0;text-align: left;">
                    <span>Heading {{$item->grid_column}}</span>
                </div>
                <div class="col-6 itemTools" style="padding:0;">
                    <span class="portlet-header">Drag</span>
                    <span class="badge badge-success" data-toggle="modal" data-target="#editItem_{{$item->id}}" style="cursor:pointer;"> <i class="fa fa-edit"></i></span>
                    <a href="{{route('admin.contentEditorAction',['deleteItem',$item->id])}}" class="badge badge-danger" onclick="return confirm('Are You Want To Delete?')"> <i class="fa fa-trash"></i></a>
                </div>
            </div>
       </div>
       <div class="icon">
           <i class="font fa fa-header"></i>
           <span>Heading</span>
       </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade text-left" id="editItem_{{$item->id}}" tabindex="-1" >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 5px 15px;background: #edeff6;">
                    <h4 class="modal-title" id="myModalLabel1">Heading Edit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times; </span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.contentEditorAction',['updateItem',$item->id,'itemtype'=>'heading'])}}" method="post">
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
                                   <th>Heading*</th>
                                   <td>
                                       <select class="form-control form-control-sm" name="headingName" required="">
                                           <option value="h1" {{$item->name=='h1'?'selected':''}} >Heading One (H1)</option>
                                           <option value="h2" {{$item->name=='h2'?'selected':''}} >Heading Two (H2)</option>
                                           <option value="h3" {{$item->name=='h3'?'selected':''}} >Heading Three (H3)</option>
                                           <option value="h4" {{$item->name=='h4'?'selected':''}} >Heading Four (H4)</option>
                                           <option value="h5" {{$item->name=='h5'?'selected':''}} >Heading Five (H5)</option>
                                           <option value="h6" {{$item->name=='h6'?'selected':''}} >Heading Six (H6)</option>
                                       </select>
                                   </td>
                               </tr>
                               <tr>
                                   <th>
                                       Title
                                   </th>
                                   <td>
                                       <textarea type="text" name="headingText" rows="7" class="form-control headingEditor" placeholder="Write Paragraph">{{$item->content}}</textarea>
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