@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Training Page Content')}}</title>
@endsection
@section('contents')

@php
    $defaults = trainingDefaults();
    $content = trainingContent($page);
    $old = old('content', $content);
    $value = function ($group, $key) use ($old) { return $old[$group][$key] ?? ''; };
    $chipRows = old('chips', $content['chips']);
@endphp

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Training Page Content</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.trainingCourses')}}">Training Courses</a></li>
                    <li class="breadcrumb-item active">Page Content</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            @if($page)
            <a class="btn btn-outline-primary" href="{{route('pageView',$page->slug)}}" target="_blank"><i class="fa fa-eye"></i> View Training Page</a>
            @endif
            <a class="btn btn-outline-primary" href="{{route('admin.trainingPage')}}"><i class="fa-solid fa-rotate"></i></a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
    @include(adminTheme().'alerts')

    @if($page)
        <form action="{{route('admin.trainingPage')}}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-9">

                    {{-- Hero --}}
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Hero Section <small style="color:#999;">(top banner)</small></h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Small Label</label>
                                        <input type="text" class="form-control" name="content[hero][eyebrow]" value="{{$value('hero','eyebrow')}}" placeholder="{{$defaults['hero']['eyebrow']}}" maxlength="100" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="content[hero][title]" value="{{$value('hero','title')}}" placeholder="{{$defaults['hero']['title']}}" maxlength="191" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Title <small style="color:#999;">(gradient colour line)</small></label>
                                        <input type="text" class="form-control" name="content[hero][highlight]" value="{{$value('hero','highlight')}}" placeholder="{{$defaults['hero']['highlight']}}" maxlength="191" />
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Description</label>
                                        <textarea class="form-control" name="content[hero][description]" rows="4" placeholder="{{$defaults['hero']['description']}}" maxlength="1000">{{$value('hero','description')}}</textarea>
                                    </div>
                                    <div class="form-group col-md-6" style="margin-bottom:0;">
                                        <label>Button Text <small style="color:#999;">(scrolls to the course list)</small></label>
                                        <input type="text" class="form-control" name="content[hero][button_text]" value="{{$value('hero','button_text')}}" placeholder="{{$defaults['hero']['button_text']}}" maxlength="100" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Course chips under the animated training lab --}}
                    <div class="card" data-repeater="chips">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3; display:flex; justify-content:space-between; align-items:center;">
                            <h4 class="card-title">Hero Course Chips <small style="color:#999;">(under the animated training lab)</small></h4>
                            <button type="button" class="btn btn-sm btn-primary" data-repeat-add><i class="fa fa-plus"></i> Add Chip</button>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="nx-rep-grid is-compact" data-repeat-list>
                                    @foreach($chipRows as $i => $row)
                                        @include(adminTheme().'industrialCapabilities.includes.listRow', ['list' => 'chips', 'index' => $i, 'row' => $row])
                                    @endforeach
                                </div>
                                <p class="nx-rep-empty" data-repeat-empty><i class="fa-regular fa-square-plus"></i>No chip added — the chip row is hidden.</p>
                            </div>
                        </div>
                        <template data-repeat-template>
                            @include(adminTheme().'industrialCapabilities.includes.listRow', ['list' => 'chips', 'index' => '__INDEX__', 'row' => []])
                        </template>
                    </div>

                    {{-- Catalog heading --}}
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Course List Heading</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="content[catalog][title]" value="{{$value('catalog','title')}}" placeholder="{{$defaults['catalog']['title']}}" maxlength="191" />
                                    </div>
                                    <div class="form-group col-md-12" style="margin-bottom:0;">
                                        <label>Text</label>
                                        <input type="text" class="form-control" name="content[catalog][description]" value="{{$value('catalog','description')}}" placeholder="{{$defaults['catalog']['description']}}" maxlength="500" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="col-md-3">
                    <div class="card" style="position:sticky; top:90px;">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Save</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <p style="color:#777;">Texts of the <b>{{$page->name}}</b> page. A text field left empty shows the original design text.</p>
                                <p style="color:#777;"><code>{count}</code> is replaced with the number of courses ({{trainingCourses()->count()}}). Write <code>**word**</code> to show a word in <b>bold</b>.</p>
                                <p style="color:#777;">Courses are managed in <a href="{{route('admin.trainingCourses')}}">All Courses</a>.</p>
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-check"></i> Save Page</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    @else
        <div class="card">
            <div class="card-body">
                No active page uses the <b>Training Academy</b> template. Create or edit a page in
                <a href="{{route('admin.pages')}}">Pages</a> and set its Page Template to "Training Academy".
            </div>
        </div>
    @endif
    </section>
</div>

@endsection
@push('css')
@include(adminTheme().'industrialCapabilities.includes.repeaterStyle')
@endpush
@push('js')
@include(adminTheme().'industrialCapabilities.includes.repeaterScript')
@endpush
