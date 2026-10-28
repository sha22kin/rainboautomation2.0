{{-- Menu picker modal, vendor JS and shared page scripts of the website admin pages --}}
<!-- Modal -->
    <div class="modal fade text-left" id="MenuSetting" tabindex="-1" >
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" id="myModalLabel1">Menus Setting</h4>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times; </span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" class="newMenuItem">
                        <div class="form-group">
                            <label>Select Menu</label>
                            <select class="form-control ajaxMenuSelect" name="location">
                                <option value="">Select Location</option>
                                <option value="Top Header" >Top Header</option>
                                <option value="Header Menus" >Header Menus</option>
                                <option value="Footer Two" >Footer Two</option>
                                <option value="Footer Three" >Footer Three</option>
                            </select>
                        </div>
                        <div class="menuListBarSection">
                            <span class="loader"><img src="{{asset('medies/loading.gif')}}"></span>
                            <div class="menuListBar">
                            
                            </div>
                            
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn grey btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                    </div>
            </div>
        </div>
    </div>


     <!-- BEGIN: Vendor JS-->
     <script src="{{asset(assetLinkAdmin().'/app-assets/vendors/js/vendors.min.js')}}"></script>
     <script src="{{asset(assetLinkAdmin().'/app-assets/vendors/js/charts/apexcharts/apexcharts.min.js')}}"></script>
     <script src="{{asset(assetLinkAdmin().'/app-assets/vendors/js/forms/select/select2.full.min.js')}}"></script>
     <!-- END: Vendor JS-->

     <!-- Drag dropable data  -->
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"></script>

    <!-- JavaScript Bundle with Popper -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js" integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13" crossorigin="anonymous"></script>
     <!-- BEGIN: Page JS-->
     <script src="{{asset(assetLinkAdmin().'/app-assets/js/scripts/cards/card-statistics.min.js')}}"></script>
     <script src="{{asset(assetLinkAdmin().'/app-assets/js/scripts/forms/select/form-select2.min.js')}}"></script>
     <script src="{{asset(assetLinkAdmin().'/app-assets/js/tag-editor.js')}}"></script>
     <script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
     <!-- END: Page JS-->
     
     <script type="text/javascript">
      $( function() {
              $( ".sortable" ).sortable();
              $( ".sortable" ).disableSelection();
          } );

    </script>
     <script>
      $(document).ready(function(){
        
        $('.MenuSetting').click(function(){
            
            $('#MenuSetting').modal('show');
            
            var id =$(this).data('id');
            $('.newMenuItem').val(id);
            
        });
         
        $(document).on('change','.ajaxMenuSelect',function(){
            
            var url ="{{route('admin.menusAction','menuFilter')}}";
            var location=$(this).val();
            $('.loader').show();
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data:{location:location},
               success : function(data){
                $('.menuListBar').empty().append(data.viewData);
                $('.loader').hide();
               },error: function () {
                  alert('error');
                  $('.loader').hide();
                }
            });
            
        });
        
        $(document).on('click','.addMenu',function(){
            
            var url ="{{route('admin.menusAction','menuFilter')}}";
            var id  =$('.newMenuItem').val();
            var type  =$('.newMenuItem').data('type');
            var parentItem  =$(this).data('id');
            var menulocation  =$('.ajaxMenuSelect').val();
            $('.loader').show();
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data:{addmenuitem:id,addtype:type,parentItem:parentItem,menulocation:menulocation},
               success : function(data){
                $('.menuListBar').empty().append(data.viewData);
                $('.loader').hide();
               },error: function () {
                  alert('error');
                  $('.loader').hide();
                }
            });
            
        });
        
        $(document).on('click','.backMenus',function(){
            var url ="{{route('admin.menusAction','menuFilter')}}";
            var id  =$(this).data('id');
            var menulocation  =$('.ajaxMenuSelect').val();
            $('.loader').show();
            
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data:{backmenuitem:id,menulocation:menulocation},
              success : function(data){
                $('.menuListBar').empty().append(data.viewData);
                $('.loader').hide();
              },error: function () {
                  alert('error');
                  $('.loader').hide();
                }
            });
            
            
        });
        
        $(document).on('click','.removeItem',function(){
            var url ="{{route('admin.menusAction','menuFilter')}}";
            var id  =$(this).data('id');
            var menulocation  =$('.ajaxMenuSelect').val();
            $('.loader').show();
            
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data:{removeItem:id,menulocation:menulocation},
               success : function(data){
                $('.menuListBar').empty().append(data.viewData);
                $('.loader').hide();
               },error: function () {
                  alert('error');
                  $('.loader').hide();
                }
            });
            
        });
        
        
        $(document).on('click','.addNewLabelMenu',function(){
            var url ="{{route('admin.menusAction','menuFilter')}}";
            var id  =$(this).data('id');
            var menulocation  =$('.ajaxMenuSelect').val();
            $('.loader').show();
            
            $.ajax({
              url:url,
              dataType: 'json',
              cache: false,
              data:{nextLavelMenu:id,menulocation:menulocation},
               success : function(data){
                $('.menuListBar').empty().append(data.viewData);
                $('.loader').hide();
               },error: function () {
                  alert('error');
                  $('.loader').hide();
                }
            });
            
        });
          

         $.ajaxSetup({
              headers: {
                  'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
              }
            });

            $(document).on('click','.reloadPage',function(){

                location.reload();
                return true;

            });
          
          $(document).on('click','.showPassword',function(){
                $(this).toggleClass('active-show');
                if ($(this).hasClass('active-show')) {
                    $('input.password').prop('type','text');
                    $(this).empty().append('<i class="fa fa-eye"></i>');
                } else {
                    $('input.password').prop('type','password');
                    $(this).empty().append('<i class="fa fa-eye-slash"></i>');
                }
            });


          $("#division").on("change", function(){
                var id = $(this).val();
                  if(id==''){
                   $('#district').empty().append('<option value="">No District</option>');
                   $('#city').empty().append('<option value="">No City</option>');
                  }
                  var url ='{{url('geo/filter')}}' + '/'+id;
                  $.get(url,function(data){
                    $('#district').empty().append(data.geoData);
                    $('#city').empty().append('<option value="">No City</option>');
                  });   
            });

            $("#district").on("change", function(){
                var id = $(this).val();
                  if(id==''){
                   $('#city').empty().append('<option value="">No City</option>');
                  }
                  var url ='{{url('geo/filter')}}' + '/'+id;
                  $.get(url,function(data){
                    $('#city').empty().append(data.geoData);  
                  });   
            });


            $('.mediaDelete').click(function(e){
                e.preventDefault();

              var url =$(this).attr('href');

              if(confirm("Are you sure you want to delete this?")){
                
                $.ajax({
                  url : url,
                  type:'GET',
                  cache: false,
                  contentType: false,
                  dataType: 'json',
                  beforeSend: function()
                  {
                    
                  },
                  complete: function()
                  {
                      
                  },
                  }).done(function (data) {
                     
                     location.reload(true);
                    
                  }).fail(function () {
                      alert('fail');
                  });
                  
              }else{
                  return false;
              }

            });
          
      });
    </script>

    <script type="text/javascript">
      ///Check Box Select With Count show

          $(function() {
            $('.checkCounter').text('0');
            var generallen = $("input[name='checkid[]']:checked").length;
            if (generallen > 0) {
              $(".checkCounter").text('(' + generallen + ')');
            } else {
              $(".checkCounter").text(' ');
            }
            
          })
          
          function updateCounter() {
            var len = $("input[name='checkid[]']:checked").length;
            if (len > 0) {
              $(".checkCounter").text('(' + len + ')');
            } else {
              $(".checkCounter").text(' ');
            }
          }
          
          $("input:checkbox").on("change", function() {
            updateCounter();
          });

       
        $(document).ready(function(){
          $('#checkall').click(function() {
              var checked = $(this).prop('checked');
              $('input:checkbox').prop('checked', checked);
              updateCounter();
            });
        });
        
        ///Check Box Select With Count show
      </script>

      