@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Home: Why Choose Us')}}</title>
@endsection
@section('contents')

{{-- Home page "Why Choose Us" section: heading texts + items ($content from homeWhyContent()) --}}
@php
    $defaults = homeWhyDefaults();
    $value = function ($key) use ($content) { return old($key, $content[$key] ?? ''); };
@endphp

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Home: Why Choose Us</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Home: Why Choose Us</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            <a class="btn btn-outline-primary" href="{{route('index')}}" target="_blank"><i class="fa fa-eye"></i> View Home</a>
            <a class="btn btn-outline-primary" href="{{route('admin.homeWhyAction','create')}}"><i class="fa fa-plus"></i> Add Item</a>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')

        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                <h4 class="card-title">Items <small style="color:#999;">(numbered automatically)</small></h4>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-striped table-bordered table-hover">
                            <thead>
                                <tr>
                                    <th style="width: 60px;">No</th>
                                    <th style="min-width: 260px;">Item</th>
                                    <th style="width: 80px;">Order</th>
                                    <th style="width: 150px;">Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($content['items'] as $i => $item)
                                <tr>
                                    <td style="text-align: center;">
                                        <span style="display:inline-grid;place-items:center;width:38px;height:38px;border-radius:12px;color:#fff;font-weight:800;background:linear-gradient(135deg,{{$item['color']}},{{$item['color2'] ?? $item['color']}})">{{str_pad($i + 1, 2, '0', STR_PAD_LEFT)}}</span>
                                    </td>
                                    <td>
                                        <a href="{{route('admin.homeWhyAction',['edit',$i])}}"><b>{{$item['title']}}</b></a>
                                        <br />
                                        <span style="color:#777;">{{\Illuminate\Support\Str::limit($item['text'], 120)}}</span>
                                    </td>
                                    <td style="text-align: center;">
                                        <a href="{{route('admin.homeWhyAction',['up',$i])}}" title="Move up"><i class="fa fa-arrow-up"></i></a>
                                        <a href="{{route('admin.homeWhyAction',['down',$i])}}" title="Move down"><i class="fa fa-arrow-down"></i></a>
                                    </td>
                                    <td style="padding: 5px;">
                                        <a href="{{route('admin.homeWhyAction',['edit',$i])}}" class="btn btn-sm btn-info">Edit</a>
                                        <a href="{{route('admin.homeWhyAction',['delete',$i])}}" class="btn btn-sm btn-danger" onclick="return confirm('Are You Want To Delete?')">Delete</a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" style="text-align: center; color: #999;">No item added. Click "Add Item".</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                    <a class="btn btn-primary" href="{{route('admin.homeWhyAction','create')}}"><i class="fa fa-plus"></i> Add Item</a>
                </div>
            </div>
        </div>

        <form action="{{route('admin.homeWhy')}}" method="post">
            @csrf
            <div class="card">
                <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                    <h4 class="card-title">Section Heading</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-4">
                                <label>Small Label</label>
                                <input type="text" class="form-control" name="eyebrow" value="{{$value('eyebrow')}}" placeholder="{{$defaults['eyebrow']}}" maxlength="100" />
                            </div>
                            <div class="form-group col-md-8">
                                <label>Title</label>
                                <input type="text" class="form-control" name="title" value="{{$value('title')}}" placeholder="{{$defaults['title']}}" maxlength="191" />
                            </div>
                            <div class="form-group col-md-12">
                                <label>Description</label>
                                <textarea class="form-control" name="subtitle" rows="3" placeholder="{{$defaults['subtitle']}}" maxlength="500">{{$value('subtitle')}}</textarea>
                            </div>
                        </div>
                        <p style="color:#777;">An empty field is hidden on the website.</p>
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save Heading</button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

@endsection
