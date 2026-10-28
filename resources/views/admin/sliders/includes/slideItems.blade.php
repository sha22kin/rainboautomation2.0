
    <div class="table-responsive">
        <table class="table table-striped table-bordered table-hover" >
            <thead>
                <tr>
                    <th>Image</th>
                    <th width="60%">Info</th>
                </tr>
            </thead>
            <tbody class="sortable">
            @foreach($slider->sliderItems as $i=>$slide)
                <tr>
                    <td style="cursor: move;">
                    <input type="hidden" name="slideid[]" value="{{$slide->id}}">
                    <img src="{{asset($slide->image())}}" style="max-width: 100px;">
                    <br>
                    <a href="{{route('admin.slideAction',['edit',$slide->id])}}" class="btn btn-md btn-info">Edit</a>


                    @isset(json_decode(Auth::user()->permission->permission, true)['sliders']['delete'])
                    <a href="{{route('admin.slideAction',['delete',$slide->id])}}" class="btn btn-md btn-danger" onclick="return confirm('Are Your Want To Delete?')">Delete</a>
                    @endisset
                    </td>
                    <td style="cursor: move;">
                    <span><b>Name: </b>{{$slide->name}}</span><br>
                    <span><b>Description:</b> {{Str::limit($slide->description,150)}}</span>
                    @if($slide->seo_title || $slide->seo_description)
                   <br><a href="{{$slide->seo_description}}" class="btn btn-sm btn-success" target="_blank">{{$slide->seo_title}}</a>
                    @endif
                    <br>
                    @if($slide->status=='active')
                    <span><i class="fa fa-check" style="color: #1ab394;"></i></span>
                    @else
                    <span><i class="fa fa-time" style="color: #1ab394;"></i></span>
                    @endif
                    @if($slide->icon)
                    <span>Color: <span style="    width: 150px;height: 10px;display: inline-block;background:{{$slide->icon}}"></span></span>
                    @endif
                    </td>
                </tr>
        @endforeach
        @if($slider->sliderItems->count()==0)
                <tr>
                    <td colspan="2" style="text-align:center;">No Slide Found</td>
                </tr>
                @endif
            </tbody>
            <tfoot>
                <tr>
                    <th>Image</th>
                    <th>Info</th>
                </tr>
            </tfoot>
        </table>
    </div>
  
  <script type="text/javascript">
    $( function() {
            $( "#sortable" ).sortable();
            $( "#sortable" ).disableSelection();
          } );
  </script>