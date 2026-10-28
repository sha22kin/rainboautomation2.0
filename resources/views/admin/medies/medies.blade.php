@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Medias Library')}}</title>
@endsection @push('css')
<style type="text/css">
    .fileuploard-div {
        border: 2px dotted #e3e3e3;
        padding: 25px;
        text-align: center;
    }

    .fileuploard-div p {
        font-size: 20px;
        color: silver;
        text-transform: uppercase;
    }
    .fileuploard-div label {
        margin: 0;
    }
    .fileuploard-div i {
        font-size: 60px;
        cursor: pointer;
        color: #c6c2c2;
    }

    ul.medialists {
        margin: 0;
        padding: 0;
        list-style: none;
    }
    ul.medialists li img {
        width: 100%;
    }
    ul.medialists li {
        display: inline-block;
        width: 9%;
        border: 1px solid #d4d4d4;
        cursor: pointer;
        padding: 5px;
        margin: 3px;
        position: relative;
        height: 100px;
    }

    .progressdivs {
    }
    .mediaImagediv {
        overflow: hidden;
        position: absolute;
        top: 0;
        right: 0;
        bottom: 0;
        left: 0;
        opacity: 1;
        transition: opacity 0.1s;
    }

    @media only screen and (max-width: 600px) {
        ul.medialists li {
            width: 17%;
            height: 70px;
        }
    }
</style>
@endpush @section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Medias Library</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Medias Library</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group" aria-label="Button group with nested dropdown">
            <a class="btn btn-outline-primary" href="{{route('admin.medies')}}">
                <i class="fa-solid fa-rotate"></i>
            </a>
        </div>
    </div>
</div>

<div class="content-body">
    <!-- Basic Elements start -->
    <section class="basic-elements">
        <div class="row">
            <div class="col-md-12">
                @include(adminTheme().'alerts')
                @isset(json_decode(Auth::user()->permission->permission, true)['medies']['add'])
                <div class="card">
                    <div class="card-content">
                        <div class="card-body">
                            
                            <div class="fileuploard-div">
                                    <div>
                                        <p>Drag & Drop your files or <span class="filepond--label-action" tabindex="0">Browse</span></p>
                                    </div>
                                    <div class="progress">
                                      <div class="progress-bar" role="progressbar" style="width: 0%" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100"></div>
                                    </div>
                                    <div>
                                        @if(session('errors'))
                                        <ul style="list-style: none;">
                                            @foreach ($errors->all() as $error)
                                            <li style="color: #f44336; font-weight: bold; font-size: 12px;">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        @endif
                                        <span class="Errormsg" style="color: #FF5722;"></span>
                                    </div>
                                    <div>
                                        <label>
                                            <input type="file" name="file" class="fileupload" style="cursor: pointer;"/>
                                        </label>
                                    </div>
                                </div>
                            
                            <form action="{{route('admin.mediesCreate')}}" method="post" enctype="multipart/form-data">
                                @csrf
                                <div class="fileuploard-div">
                                    <div>
                                        <p>Click To Files upload</p>
                                    </div>
                                    <div>
                                        @if(session('errors'))
                                        <ul style="list-style: none;">
                                            @foreach ($errors->all() as $error)
                                            <li style="color: #f44336; font-weight: bold; font-size: 12px;">{{ $error }}</li>
                                            @endforeach
                                        </ul>
                                        @endif
                                    </div>
                                    <div>
                                        <label>
                                            <input type="file" name="images[]" multiple="" class="fileuploard" />
                                        </label>
                                        <button type="submit" class="btn btn-success">Submit</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                @endisset
                <form action="{{route('admin.medies')}}" class="mediaAllForm" >
                    <input type="hidden" name="actionType" value="allDelete">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Medias All 
                                @isset(json_decode(Auth::user()->permission->permission, true)['medies']['delete'])
                                <a href="javascript:void(0)" class="btn btn-sm btn-danger mediaAllDeleted">Delete</a>
                                @endisset
                            </h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body moremediesDiv">
                                <div class="dataLastPage" data-lastpage="{{$medies->lastPage()}}" data-nowpage="1"></div>
                                <ul class="postsAuto medialists">
                                    @include(adminTheme().'medies.includes.mediesAll')
                                </ul>
                                <div class="text-center mt-2" style="display: none;">
                                    <div class="loader"><i class="fa fa-spin fa-spinner"></i></div>
                                </div>
                                @if($medies->lastPage() > 1)
                                <div>
                                    <p class="moremedies">
                                        <span class="badge" style="color: white; background: #1c84c6; cursor: pointer;">More ..</span>
                                    </p>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </section>
    <!-- Basic Inputs end -->
</div>

@endsection @push('js')

<script type="text/javascript">
    $(".progressdivs1").hide();
    $(document).ready(function () {
        
        
        
        
        
        
        
        $('.fileupload').change(function(){
            var url ="{{route('admin.mediesFileUpload')}}"
            var formData = new FormData();
            var file = $(this)[0].files[0];
            formData.append('file', file);
            
            $.ajax({
                url: url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                xhr: function() {
                    var xhr = new window.XMLHttpRequest();
                    // Listen to the progress event
                    xhr.upload.addEventListener("progress", function(evt) {
                        if (evt.lengthComputable) {
                            console.log(evt.loaded);
                            var percentComplete = evt.loaded / evt.total * 100;
                            $('.progress-bar').css('width', percentComplete + '%');
                        }
                    }, false);
    
                    // Listen to the load event
                    xhr.upload.addEventListener("load", function(evt) {
                        // File upload completed successfully
                        console.log('Upload completed!');
                        // Show completion status
                        $('.progress-bar').addClass('bg-success').text('Upload completed');
                    }, false);
    
                    // Listen to the error event
                    xhr.upload.addEventListener("error", function(evt) {
                        // File upload encountered an error
                        console.error('Upload error');
                        // Show error status
                        $('.progress-bar').addClass('bg-danger').text('Upload failed');
                    }, false);
                    
                    return xhr;
                },
                success: function(response) {
                    // File upload completed successfully
                    console.log('Upload completed!');
                    // Show completion status
                    $('.progress-bar').addClass('bg-success').text('Upload completed');
                },
                error: function(xhr, status, error) {
                    // File upload failed
                    console.error('Upload error: ' + error);
                    // Show error status
                    $('.progress-bar').addClass('bg-danger').text('Upload failed');
                }
            });
        });
        
        
        
        
        
        
        
        
        
        
        
        
        $(".mediaAllDeleted").click(function () {
            if (confirm("Are you want to delete All Selected?")) {
                $("form.mediaAllForm").trigger("submit");
            } else {
                return false;
            }
        });

        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });

        $(".moremediesDiv").on("click", ".moremedies", function () {
            var LP = $(".dataLastPage").attr("data-lastpage");
            var pageWall = parseInt($(".dataLastPage").attr("data-nowpage"));

            if (pageWall < LP) {
                pageWall = pageWall + 1;
                $(".dataLastPage").attr("data-nowpage", pageWall);

                var dataUrl = $(".dataLastPage").attr("data-url");

                if (typeof dataUrl !== typeof undefined && dataUrl !== false) {
                    var url = dataUrl;
                } else {
                    var url = "";
                }

                getPosts(pageWall, url);
            } else {
                $(".moremedies").empty().append("NO Data");
            }

            function getPosts(pageWall, url) {
                $.ajax({
                    url: url + "?page=" + pageWall,
                    dataType: "json",
                    beforeSend: function () {
                        $(".loading").show();
                        $(".loader").show();
                    },
                    complete: function () {
                        $(".loading").hide();
                        $(".loader").hide();
                    },
                })
                    .done(function (data) {
                        $(".postsAuto").append(data.view);

                        // location.hash = pageWall;
                    })
                    .fail(function () {
                        $(".moremedies").html("No More Data.");
                    });
            }
        });
    });
</script>
@endpush
