@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Contact Locations')}}</title>
@endsection
@section('contents')

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Contact Locations</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Contact Locations</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            @if($page)
            <a class="btn btn-outline-primary" href="{{route('pageView',$page->slug)}}" target="_blank"><i class="fa fa-eye"></i> View Contact Page</a>
            @endif
            <a class="btn btn-outline-primary" href="{{route('admin.contactLocations')}}"><i class="fa-solid fa-rotate"></i></a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
    @include(adminTheme().'alerts')

    @if($page)
        <form action="{{route('admin.contactLocations')}}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-9">
                    @include(adminTheme().'pages.includes.contactLocations')
                </div>
                <div class="col-md-3">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Save</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <p style="color:#777;">These address cards (with map) are shown on the <b>{{$page->name}}</b> page of the website, in the order listed.</p>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-check"></i> Save Locations</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="card">
            <div class="card-body">
                No active page uses the <b>Contact Us</b> template. Create or edit a page in
                <a href="{{route('admin.pages')}}">Pages</a> and set its Page Template to "Contact Us".
            </div>
        </div>
    @endif
    </section>
</div>

@endsection
