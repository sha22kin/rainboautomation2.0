@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle($template)}}</title>
@endsection
@section('contents')

{{-- Shared editor of the Industrial Solution / Solar Solution / Services pages ($template, $routeName, $section from AdminController::solutionPageEditor) --}}
@php
    $defaults = solutionDefaults($template);
    $content = solutionContent($page, $template);
    $old = old('content', $content);
    $value = function ($group, $key) use ($old) { return $old[$group][$key] ?? ''; };
    $cards = $page ? $page->capabilityCards : collect();
    $supportCards = $page && isset($content['support']) ? $page->supportCards : collect();
    $features = array_values($content['features']);
    $steps = array_values($content['workflow']['steps']);
@endphp

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">{{$template}}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">{{$template}}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            @if($page)
            <a class="btn btn-outline-primary" href="{{route('pageView',$page->slug)}}" target="_blank"><i class="fa fa-eye"></i> View Page</a>
            @endif
            <a class="btn btn-outline-primary" href="{{route($routeName)}}"><i class="fa-solid fa-rotate"></i></a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
    @include(adminTheme().'alerts')

    @if($page)
        <form action="{{route($routeName)}}" method="post">
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
                                        <input type="text" class="form-control" name="content[hero][overline]" value="{{$value('hero','overline')}}" placeholder="{{$defaults['hero']['overline']}}" maxlength="100" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="content[hero][title]" value="{{$value('hero','title')}}" placeholder="{{$defaults['hero']['title']}}" maxlength="191" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Title <small style="color:#999;">(rainbow colour part)</small></label>
                                        <input type="text" class="form-control" name="content[hero][highlight]" value="{{$value('hero','highlight')}}" placeholder="{{$defaults['hero']['highlight']}}" maxlength="191" />
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Description</label>
                                        <textarea class="form-control" name="content[hero][description]" rows="3" placeholder="{{$defaults['hero']['description']}}" maxlength="500">{{$value('hero','description')}}</textarea>
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Main Button Text</label>
                                        <input type="text" class="form-control" name="content[hero][primary_text]" value="{{$value('hero','primary_text')}}" placeholder="{{$defaults['hero']['primary_text']}}" maxlength="100" />
                                    </div>
                                    <div class="form-group col-md-6">
                                        <label>Main Button Link <small style="color:#999;">(empty = Contact page)</small></label>
                                        <input type="text" class="form-control" name="content[hero][primary_link]" value="{{$value('hero','primary_link')}}" placeholder="{{pageLink('contact')}}" maxlength="500" />
                                    </div>
                                    <div class="form-group col-md-6" style="margin-bottom:0;">
                                        <label>Second Button Text</label>
                                        <input type="text" class="form-control" name="content[hero][secondary_text]" value="{{$value('hero','secondary_text')}}" placeholder="{{$defaults['hero']['secondary_text']}}" maxlength="100" />
                                    </div>
                                    <div class="form-group col-md-6" style="margin-bottom:0;">
                                        <label>Second Button Link <small style="color:#999;">(empty = {{$template == 'Services' ? 'Services' : 'Home'}} page)</small></label>
                                        <input type="text" class="form-control" name="content[hero][secondary_link]" value="{{$value('hero','secondary_link')}}" placeholder="{{$template == 'Services' ? pageLink('services') : route('index')}}" maxlength="500" />
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    @if($template != 'Services')
                    {{-- Hero feature chips: list (added / edited on their own page) --}}
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3; display:flex; justify-content:space-between; align-items:center;">
                            <h4 class="card-title">Hero Feature Chips <small style="color:#999;">({{count($features)}} chips · under the animated hero panel)</small></h4>
                            <a class="btn btn-sm btn-primary" href="{{route('admin.solutionItemsAction',[$section,'features','create'])}}"><i class="fa fa-plus"></i> Add Chip</a>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover nx-card-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 45px;">SL</th>
                                                <th style="min-width: 260px;">Chip</th>
                                                <th style="width: 110px;">Dot Color</th>
                                                <th style="width: 80px;">Order</th>
                                                <th style="width: 150px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($features as $i => $feature)
                                            <tr>
                                                <td>{{$i + 1}}</td>
                                                <td>
                                                    <span class="nx-chip-preview"><span class="nx-card-swatch" style="background:{{$feature['color'] ?? '#287dcc'}}"></span><span><a href="{{route('admin.solutionItemsAction',[$section,'features','edit',$i])}}"><b>{{$feature['title'] ?? ''}}</b></a><small>{{$feature['text'] ?? ''}}</small></span></span>
                                                </td>
                                                <td><span class="nx-card-swatch" style="background:{{$feature['color'] ?? '#287dcc'}}"></span> <span class="nx-rep-hex">{{$feature['color'] ?? '#287dcc'}}</span></td>
                                                <td class="orderBtns" style="text-align: center;">
                                                    <a href="{{route('admin.solutionItemsAction',[$section,'features','up',$i])}}" title="Move up"><i class="fa fa-arrow-up"></i></a>
                                                    <a href="{{route('admin.solutionItemsAction',[$section,'features','down',$i])}}" title="Move down"><i class="fa fa-arrow-down"></i></a>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <a href="{{route('admin.solutionItemsAction',[$section,'features','edit',$i])}}" class="btn btn-sm btn-info">Edit</a>
                                                    <a href="{{route('admin.solutionItemsAction',[$section,'features','delete',$i])}}" class="btn btn-sm btn-danger" onclick="return confirm('Are You Want To Delete?')">Delete</a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="5" style="text-align: center; color: #999;">No chip added — the chip row is hidden on the website.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Capabilities: section heading + card list (cards are added / edited on their own page) --}}
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3; display:flex; justify-content:space-between; align-items:center;">
                            <h4 class="card-title">{{$content['capabilities']['title']}} <small style="color:#999;">({{$cards->count()}} cards)</small></h4>
                            <a class="btn btn-sm btn-primary" href="{{route('admin.solutionCardsAction',[$section,'create'])}}"><i class="fa fa-plus"></i> Add Card</a>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row nx-rep-section">
                                    <div class="form-group col-md-4">
                                        <label>Small Label</label>
                                        <input type="text" class="form-control" name="content[capabilities][overline]" value="{{$value('capabilities','overline')}}" placeholder="{{$defaults['capabilities']['overline']}}" maxlength="100" />
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Section Title</label>
                                        <input type="text" class="form-control" name="content[capabilities][title]" value="{{$value('capabilities','title')}}" placeholder="{{$defaults['capabilities']['title']}}" maxlength="191" />
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Section Text</label>
                                        <input type="text" class="form-control" name="content[capabilities][description]" value="{{$value('capabilities','description')}}" placeholder="{{$defaults['capabilities']['description']}}" maxlength="500" />
                                    </div>
                                </div>
                                @include(adminTheme().'industrialCapabilities.includes.cardTable', ['cards' => $cards, 'cardSection' => $section])
                            </div>
                        </div>
                    </div>

                    @if(isset($content['support']))
                    {{-- Services: "What We Can Support" heading, quick strip and its own card list --}}
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3; display:flex; justify-content:space-between; align-items:center;">
                            <h4 class="card-title">{{$content['support']['title']}} <small style="color:#999;">({{$supportCards->count()}} cards)</small></h4>
                            <a class="btn btn-sm btn-primary" href="{{route('admin.solutionCardsAction',['services-support','create'])}}"><i class="fa fa-plus"></i> Add Card</a>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row nx-rep-section">
                                    <div class="form-group col-md-4">
                                        <label>Small Label</label>
                                        <input type="text" class="form-control" name="content[support][overline]" value="{{$value('support','overline')}}" placeholder="{{$defaults['support']['overline']}}" maxlength="100" />
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Section Title</label>
                                        <input type="text" class="form-control" name="content[support][title]" value="{{$value('support','title')}}" placeholder="{{$defaults['support']['title']}}" maxlength="191" />
                                    </div>
                                    <div class="form-group col-md-12" style="margin-bottom:0;">
                                        <label>Quick Strip <small style="color:#999;">(one item per line – shown under the cards)</small></label>
                                        <textarea class="form-control" name="content[support][strip]" rows="4" placeholder="{{$defaults['support']['strip']}}" maxlength="1000">{{$value('support','strip')}}</textarea>
                                    </div>
                                </div>
                                @include(adminTheme().'industrialCapabilities.includes.cardTable', ['cards' => $supportCards, 'cardSection' => 'services-support'])
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- Workflow: section heading + step list (steps are added / edited on their own page) --}}
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3; display:flex; justify-content:space-between; align-items:center;">
                            <h4 class="card-title">Project Workflow <small style="color:#999;">({{count($steps)}} steps)</small></h4>
                            <a class="btn btn-sm btn-primary" href="{{route('admin.solutionItemsAction',[$section,'steps','create'])}}"><i class="fa fa-plus"></i> Add Step</a>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row nx-rep-section">
                                    <div class="form-group col-md-4">
                                        <label>Small Label</label>
                                        <input type="text" class="form-control" name="content[workflow][overline]" value="{{$value('workflow','overline')}}" placeholder="{{$defaults['workflow']['overline']}}" maxlength="100" />
                                    </div>
                                    <div class="form-group col-md-8">
                                        <label>Section Title</label>
                                        <input type="text" class="form-control" name="content[workflow][title]" value="{{$value('workflow','title')}}" placeholder="{{$defaults['workflow']['title']}}" maxlength="191" />
                                    </div>
                                </div>
                                <div class="table-responsive">
                                    <table class="table table-striped table-bordered table-hover nx-card-table">
                                        <thead>
                                            <tr>
                                                <th style="width: 70px;">Step</th>
                                                <th style="min-width: 260px;">Title &amp; Text</th>
                                                <th style="width: 80px;">Order</th>
                                                <th style="width: 150px;">Action</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @forelse($steps as $i => $step)
                                            <tr>
                                                <td style="text-align: center;"><span class="nx-step-no">{{str_pad($i + 1, 2, '0', STR_PAD_LEFT)}}</span></td>
                                                <td>
                                                    <a href="{{route('admin.solutionItemsAction',[$section,'steps','edit',$i])}}"><b>{{$step['title'] ?? ''}}</b></a>
                                                    <br />
                                                    <span class="nx-card-desc">{{\Illuminate\Support\Str::limit($step['text'] ?? '', 120)}}</span>
                                                </td>
                                                <td class="orderBtns" style="text-align: center;">
                                                    <a href="{{route('admin.solutionItemsAction',[$section,'steps','up',$i])}}" title="Move up"><i class="fa fa-arrow-up"></i></a>
                                                    <a href="{{route('admin.solutionItemsAction',[$section,'steps','down',$i])}}" title="Move down"><i class="fa fa-arrow-down"></i></a>
                                                </td>
                                                <td style="padding: 5px;">
                                                    <a href="{{route('admin.solutionItemsAction',[$section,'steps','edit',$i])}}" class="btn btn-sm btn-info">Edit</a>
                                                    <a href="{{route('admin.solutionItemsAction',[$section,'steps','delete',$i])}}" class="btn btn-sm btn-danger" onclick="return confirm('Are You Want To Delete?')">Delete</a>
                                                </td>
                                            </tr>
                                            @empty
                                            <tr>
                                                <td colspan="4" style="text-align: center; color: #999;">No step added — the workflow section is hidden on the website.</td>
                                            </tr>
                                            @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Call to action --}}
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Call To Action <small style="color:#999;">(dark band at the bottom)</small></h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-12">
                                        <label>Title</label>
                                        <input type="text" class="form-control" name="content[cta][title]" value="{{$value('cta','title')}}" placeholder="{{$defaults['cta']['title']}}" maxlength="191" />
                                    </div>
                                    <div class="form-group col-md-12">
                                        <label>Text</label>
                                        <input type="text" class="form-control" name="content[cta][description]" value="{{$value('cta','description')}}" placeholder="{{$defaults['cta']['description']}}" maxlength="500" />
                                    </div>
                                    <div class="form-group col-md-6" style="margin-bottom:0;">
                                        <label>Button Text</label>
                                        <input type="text" class="form-control" name="content[cta][button_text]" value="{{$value('cta','button_text')}}" placeholder="{{$defaults['cta']['button_text']}}" maxlength="100" />
                                    </div>
                                    <div class="form-group col-md-6" style="margin-bottom:0;">
                                        <label>Button Link <small style="color:#999;">(empty = Contact page)</small></label>
                                        <input type="text" class="form-control" name="content[cta][button_link]" value="{{$value('cta','button_link')}}" placeholder="{{pageLink('contact')}}" maxlength="500" />
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
                                <p style="color:#777;">All texts of the <b>{{$page->name}}</b> page. A text field left empty shows the original design text.</p>
                                <p style="color:#777;">SEO title / description and the page link are edited in <a href="{{route('admin.pagesAction',['edit',$page->id])}}">Pages</a>.</p>
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
                No active page uses the <b>{{$template}}</b> template. Create or edit a page in
                <a href="{{route('admin.pages')}}">Pages</a> and set its Page Template to "{{$template}}".
            </div>
        </div>
    @endif
    </section>
</div>

@endsection
@push('css')
@include(adminTheme().'industrialCapabilities.includes.repeaterStyle')
@endpush
