@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle($item ? 'Why Choose Us Item Edit' : 'Add Why Choose Us Item')}}</title>
@endsection
@push('css')
<style type="text/css">
    /* Live preview: the item as it looks on the Home page */
    .nx-why-live { display: flex; gap: 14px; align-items: flex-start; padding: 18px; border-radius: 20px; background: #fff; border: 1px solid #e8edf3; box-shadow: 0 12px 28px rgba(17, 24, 39, .05); }
    .nx-why-live .icon { flex: 0 0 48px; width: 48px; height: 48px; border-radius: 16px; display: grid; place-items: center; color: #fff; font-weight: 800; }
    .nx-why-live h4 { margin: 0 0 6px; font-size: 17px; color: #20242e; word-break: break-word; }
    .nx-why-live p { margin: 0; font-size: 13px; color: #6b7280; line-height: 1.7; word-break: break-word; }
    .nx-color-row { display: flex; align-items: center; gap: 10px; }
    .nx-color-row input[type=color] { width: 46px; height: 38px; padding: 3px; flex: 0 0 46px; }
</style>
@endpush
@section('contents')

@php
    $title = $item ? 'Why Choose Us Item Edit' : 'Add Why Choose Us Item';
    $value = function ($key) use ($data) { return old($key, $data[$key] ?? ''); };
    $actionArgs = $item ? ['edit', $index] : ['create'];
@endphp

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">{{$title}}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item"><a href="{{route('admin.homeWhy')}}">Home: Why Choose Us</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            <a class="btn btn-outline-primary" href="{{route('admin.homeWhy')}}">BACK</a>
            @if($item)
            <a class="btn btn-outline-primary" href="{{route('admin.homeWhyAction','create')}}">Add Item</a>
            @endif
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.homeWhyAction', $actionArgs)}}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Item {{str_pad($number, 2, '0', STR_PAD_LEFT)}}</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">Title *</label>
                                    <input type="text" class="form-control {{$errors->has('title')?'error':''}}" id="title" name="title" value="{{$value('title')}}" maxlength="100" required data-preview="title" />
                                    @if ($errors->has('title'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
                                    @endif
                                </div>
                                <div class="form-group">
                                    <label for="text">Text</label>
                                    <textarea class="form-control {{$errors->has('text')?'error':''}}" id="text" name="text" rows="4" maxlength="500" data-preview="text">{{$value('text')}}</textarea>
                                    @if ($errors->has('text'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('text') }}</p>
                                    @endif
                                </div>
                                <div class="row">
                                    <div class="form-group col-6" style="margin-bottom:0;">
                                        <label>Number Color (start) *</label>
                                        <div class="nx-color-row"><input type="color" class="form-control" name="color" value="{{$value('color') ?: '#2684d9'}}" data-preview="color" /></div>
                                    </div>
                                    <div class="form-group col-6" style="margin-bottom:0;">
                                        <label>Number Color (end) *</label>
                                        <div class="nx-color-row"><input type="color" class="form-control" name="color2" value="{{$value('color2') ?: '#8b28a4'}}" data-preview="color2" /></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Website Preview</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="nx-why-live" id="itemPreview">
                                    <span class="icon" data-show="icon">{{str_pad($number, 2, '0', STR_PAD_LEFT)}}</span>
                                    <div><h4 data-show="title"></h4><p data-show="text"></p></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-check"></i> {{$item ? 'Update Item' : 'Save Item'}}</button>
                                <button type="submit" name="save_new" value="1" class="btn btn-outline-primary btn-block"><i class="fa fa-plus"></i> Save &amp; Add Another</button>
                                <p style="color:#777; margin:12px 0 0;">Items are shown in the <b>Why Choose Us</b> section of the Home page and numbered automatically. Change the order from the list.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>

@endsection
@push('js')
<script>
    (function () {
        var preview = document.getElementById('itemPreview');
        var placeholders = {title: 'Title', text: 'Short text'};
        function val(key) { return document.querySelector('[data-preview="' + key + '"]').value; }

        function render() {
            ['title', 'text'].forEach(function (key) {
                var target = preview.querySelector('[data-show="' + key + '"]');
                target.textContent = val(key).trim() || placeholders[key];
                target.style.opacity = val(key).trim() ? 1 : .45;
            });
            preview.querySelector('[data-show="icon"]').style.background = 'linear-gradient(135deg,' + val('color') + ',' + val('color2') + ')';
        }

        document.querySelectorAll('[data-preview]').forEach(function (input) {
            input.addEventListener('input', render);
        });
        render();
    })();
</script>
@endpush
