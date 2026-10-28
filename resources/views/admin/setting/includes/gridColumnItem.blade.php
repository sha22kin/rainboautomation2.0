<div class="col-{{$item->grid_column}}" style="padding:5px;">
    <div class="itemGrid">
        <div class="title">
            <div class="row" style="margin:0;">
                <div class="col-6" style="padding:0;text-align: left;">
                    <span>Grid Column</span>
                </div>
                <div class="col-6 itemTools" style="padding:0;">
                    <span class="portlet-header" data-id="">Drag</span>
                    <span class="badge badge-success" data-toggle="modal" data-target="#editItem_{{$item->id}}" style="cursor:pointer;"> <i class="fa fa-edit"></i></span>
                    <a href="{{route('admin.contentEditorAction',['copyItem',$item->id])}}" class="badge badge-info"> <i class="fa fa-copy"></i></a>
                    <a href="{{route('admin.contentEditorAction',['deleteItem',$item->id])}}" class="badge badge-danger" onclick="return confirm('Are You Want To Delete?')"> <i class="fa fa-trash"></i></a>
                </div>
            </div>
       </div>
       <div class="icon">
            <i class="font fa fa-columns"></i>
            <span class="font-title">Grid Column</span><br>
            <span class="columnAddItem" data-toggle="modal" data-target="#AddGridItem_{{$item->id}}" >Add Item</span>
            <div class="row column" style="margin:0 -5px;">
            @foreach($item->subItems as $i=>$columnItem)
                @if($columnItem->item_type=='paragraph')
                    @include(adminTheme().'setting.includes.paragraphItem',['item'=>$columnItem])
                @elseif($columnItem->item_type=='heading')
                    @include(adminTheme().'setting.includes.headingItem',['item'=>$columnItem])
                @elseif($columnItem->item_type=='image')
                    @include(adminTheme().'setting.includes.imageItem',['item'=>$columnItem])
                @elseif($columnItem->item_type=='gridColumn')
                    @include(adminTheme().'setting.includes.gridColumnItem',['item'=>$columnItem])
                @endif
            @endforeach
            </div>
       </div>
    </div>
    
    <!-- Modal -->
    <div class="modal fade text-left" id="editItem_{{$item->id}}" tabindex="-1" >
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header" style="padding: 5px 15px;background: #edeff6;">
                    <h4 class="modal-title" id="myModalLabel1">Column Edit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times; </span>
                    </button>
                </div>
                <div class="modal-body">
                    <form action="{{route('admin.contentEditorAction',['updateItem',$item->id,'itemtype'=>'gridColumn'])}}" method="post">
                        @csrf
                        <div class="table-responsive">
                           <table class="table table-borderless">
                               
                               <tr>
                                   <th style="width: 200px;min-width: 200px;">Class <small>(style CSS)</small></th>
                                   <td>
                                       <input type="text" name="className" value="{{$item->class_name}}" class="form-control form-control-sm" placeholder="Enter Class Name">
                                   </td>
                               </tr>
                               <tr>
                                   <th>Grid Column <small>(withIn-12) {{$item->grid_column}}</small></th>
                                   <td>
                                       <select class="form-control form-control-sm" name="gridColumn">
                                           <option value="12" {{$item->grid_column=='12'?'selected':''}} >12 Column</option>
                                           <option value="11" {{$item->grid_column=='11'?'selected':''}} >11 Column</option>
                                           <option value="10" {{$item->grid_column=='10'?'selected':''}} >10 Column</option>
                                           <option value="9" {{$item->grid_column=='9'?'selected':''}} >9 Column</option>
                                           <option value="8" {{$item->grid_column=='8'?'selected':''}} >8 Column</option>
                                           <option value="7" {{$item->grid_column=='7'?'selected':''}} >7 Column</option>
                                           <option value="6" {{$item->grid_column=='6'?'selected':''}} >6 Column</option>
                                           <option value="5" {{$item->grid_column=='5'?'selected':''}} >5 Column</option>
                                           <option value="4" {{$item->grid_column=='4'?'selected':''}} >4 Column</option>
                                           <option value="3" {{$item->grid_column=='3'?'selected':''}} >3 Column</option>
                                           <option value="2" {{$item->grid_column=='2'?'selected':''}} >2 Column</option>
                                           <option value="1" {{$item->grid_column=='1'?'selected':''}} >1 Column</option>
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
    <div class="modal fade text-left" id="AddGridItem_{{$item->id}}" tabindex="-1" >
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
                               <li data-url="{{route('admin.contentEditorAction',['addItem',$item->id,'itemtype'=>'paragraph'])}}">
                                   <div class="title">
                                       <span>Text</span>
                                   </div>
                                   <div class="icon">
                                       <i class="font fa fa-edit"></i>
                                   </div>
                               </li>
                               <li data-url="{{route('admin.contentEditorAction',['addItem',$item->id,'itemtype'=>'heading'])}}">
                                   <div class="title">
                                       <span>Heading</span>
                                   </div>
                                   <div class="icon">
                                       <i class="font fa fa-header" aria-hidden="true"></i>
                                   </div>
                               </li>
                               <li data-url="{{route('admin.contentEditorAction',['addItem',$item->id,'itemtype'=>'image'])}}">
                                   <div class="title">
                                       <span>Image</span>
                                   </div>
                                   <div class="icon">
                                       <i class="font fa fa-image"></i>
                                   </div>
                               </li>
                               <li data-url="{{route('admin.contentEditorAction',['addItem',$item->id,'itemtype'=>'gridColumn'])}}" >
                                   <div class="title">
                                       <span>Grid Column</span>
                                   </div>
                                   <div class="icon">
                                       <i class="font fa fa-columns"></i>
                                   </div>
                               </li>
                           </ul>
                       </div>
                    </div>
            </div>
        </div>
    </div>
    
</div>
