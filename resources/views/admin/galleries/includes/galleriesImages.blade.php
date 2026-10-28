@if($gallery->galleryImages->count()>0)
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" >
            <thead>
                <tr>
                    <th width="30%">
                        <label style="cursor: pointer;margin-bottom: 0;">
                        @isset(json_decode(Auth::user()->permission->permission, true)['galleries']['delete'])
                        <input class="checkbox" type="checkbox" class="form-control" id="checkall">  All 
                        @endisset
                        <span class="checkCounter"></span>
                      </label>
                    Image</th>
                    <th width="70%">Content</th>
                </tr>
            </thead>
            <tbody id="sortable">
            @foreach($gallery->galleryImages as $i=>$image)
                <tr>
                    <td style="cursor: move;">
                    @if(isset(json_decode(Auth::user()->permission->permission, true)['galleries']['add']))
                    <span>Title</span>
                    <input type="text" class="form-control"  name="imageName[]" placeholder="Enter Title" value="{{$image->alt_text}}">
                    @else
                    <span>Title</span>
                    <input type="text" class="form-control" disabled=""  placeholder="Enter Title" value="{{$image->alt_text}}">
                    @endif
                    
                    <img src="{{asset($image->file_url)}}" style="max-width: 100px;">
                    @isset(json_decode(Auth::user()->permission->permission, true)['galleries']['delete'])
                    <input type="checkbox" name="checkid[]" value="{{$image->id}}"> <i class="fa fa-trash text-danger"></i>
                    @endisset
                    @isset(json_decode(Auth::user()->permission->permission, true)['galleries']['add'])
                    <input type="hidden" name="imageid[]" value="{{$image->id}}">
                    @endisset
                    
                    
                    </td>
                    <td style="cursor: move;">
                    
                    @if(isset(json_decode(Auth::user()->permission->permission, true)['galleries']['add']))
                    <span>Description</span>
                    <textarea class="form-control" rows="4" name="imageDescription[]" placeholder="Write Description">{{$image->description}}</textarea>
                    @else
                    <span>Description</span>
                    <textarea class="form-control" rows="4" disabled="" placeholder="Write Description">{{$image->description}}</textarea>
                    @endif
                    </td>
                </tr>
        @endforeach
            </tbody>
        </table>
    </div>

@endif
<script type="text/javascript">
    $( function() {
            $( "#sortable" ).sortable();
            $( "#sortable" ).disableSelection();
        } );
</script>