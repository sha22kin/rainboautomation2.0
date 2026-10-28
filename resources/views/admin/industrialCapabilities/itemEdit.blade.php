@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle(($item ? $label.' Edit' : 'Add '.$label))}}</title>
@endsection
@push('css')
<style type="text/css">
    /* Live previews: hero feature chip / workflow step as they look on the website */
    .nx-preview-stage { padding: 22px; border-radius: 18px; background: linear-gradient(145deg, #ffffff, #f4f8fd); border: 1px solid #e4ebf3; }
    .nx-chip-live { display: flex; align-items: flex-start; gap: 10px; background: #fff; border: 1px solid #e4ebf3; border-radius: 16px; padding: 13px 12px; box-shadow: 0 10px 24px rgba(27, 54, 88, .05); }
    .nx-chip-live .dot { width: 12px; height: 12px; border-radius: 50%; margin-top: 4px; flex: 0 0 12px; box-shadow: 0 0 0 6px rgba(38, 132, 217, .06); background: #287dcc; }
    .nx-chip-live b { display: block; font-size: 13px; color: #213b58; margin-bottom: 2px; word-break: break-word; }
    .nx-chip-live small { display: block; font-size: 11px; color: #76879a; line-height: 1.5; word-break: break-word; }
    .nx-step-live { padding: 23px; border-radius: 21px; background: #fff; border: 1px solid #e7edf4; box-shadow: 0 10px 26px rgba(24, 55, 91, .06); }
    .nx-step-live small { display: inline-block; font-weight: 900; color: #287dcc; font-size: 13px; letter-spacing: 1px; }
    .nx-step-live h3 { font-size: 17px; color: #243249; margin: 10px 0 8px; font-weight: 700; word-break: break-word; }
    .nx-step-live p { color: #63748a; font-size: 13px; line-height: 1.7; margin: 0; word-break: break-word; }
    .nx-color-row { display: flex; align-items: center; gap: 10px; }
    .nx-color-row input[type=color] { width: 46px; height: 38px; padding: 3px; flex: 0 0 46px; }
    .nx-color-presets { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .nx-color-presets button { width: 26px; height: 26px; border-radius: 50%; border: 2px solid var(--nx-card, #fff); box-shadow: 0 0 0 1px var(--nx-line, #e6ecf3); cursor: pointer; padding: 0; }
    .nx-color-presets button:hover { transform: scale(1.12); }
</style>
@endpush
@section('contents')

@php
    $isChip = $list == 'features';
    $title = $item ? $label.' Edit' : 'Add '.$label;
    $value = function ($key) use ($data) { return old($key, $data[$key] ?? ''); };
    $presets = ['#2eb454', '#f3a51b', '#287dcc', '#8847bd', '#f04469', '#18a4a7'];
    $actionArgs = $item ? [$section, $list, 'edit', $index] : [$section, $list, 'create'];
@endphp

<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">{{$title}}</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item"><a href="{{route($listRoute)}}">{{$template}}</a></li>
                    <li class="breadcrumb-item active">{{$title}}</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            <a class="btn btn-outline-primary" href="{{route($listRoute)}}">BACK</a>
            @if($item)
            <a class="btn btn-outline-primary" href="{{route('admin.solutionItemsAction',[$section,$list,'create'])}}">Add {{$label}}</a>
            @endif
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.solutionItemsAction', $actionArgs)}}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">{{$isChip ? 'Hero Feature Chip' : 'Workflow Step '.str_pad($number, 2, '0', STR_PAD_LEFT)}}</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="title">Title *</label>
                                    <input type="text" class="form-control {{$errors->has('title')?'error':''}}" id="title" name="title" value="{{$value('title')}}" maxlength="100" placeholder="{{$isChip ? 'e.g. On-Grid Solar' : 'e.g. Site Assessment'}}" required data-preview="title" />
                                    @if ($errors->has('title'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
                                    @endif
                                </div>
                                <div class="form-group" @if(!$isChip) style="margin-bottom:0;" @endif>
                                    <label for="text">Text</label>
                                    @if($isChip)
                                    <input type="text" class="form-control {{$errors->has('text')?'error':''}}" id="text" name="text" value="{{$value('text')}}" maxlength="191" placeholder="e.g. Net metering ready" data-preview="text" />
                                    @else
                                    <textarea class="form-control {{$errors->has('text')?'error':''}}" id="text" name="text" rows="4" maxlength="500" placeholder="Short description of this step" data-preview="text">{{$value('text')}}</textarea>
                                    @endif
                                    @if ($errors->has('text'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('text') }}</p>
                                    @endif
                                </div>
                                @if($isChip)
                                <div class="form-group" style="margin-bottom:0;">
                                    <label for="color">Dot Color *</label>
                                    <div class="nx-color-row">
                                        <input type="color" class="form-control" id="color" name="color" value="{{$value('color') ?: '#287dcc'}}" data-preview="color" />
                                        <input type="text" class="form-control" id="colorText" value="{{$value('color') ?: '#287dcc'}}" maxlength="7" style="max-width:120px; font-family:monospace;" />
                                    </div>
                                    <div class="nx-color-presets">
                                        @foreach($presets as $preset)
                                        <button type="button" style="background:{{$preset}}" data-color="{{$preset}}" title="{{$preset}}"></button>
                                        @endforeach
                                    </div>
                                    @if ($errors->has('color'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('color') }}</p>
                                    @endif
                                </div>
                                @endif
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
                                <div class="nx-preview-stage" id="itemPreview">
                                    @if($isChip)
                                    <div class="nx-chip-live"><span class="dot" data-show="color"></span><div><b data-show="title"></b><small data-show="text"></small></div></div>
                                    @else
                                    <div class="nx-step-live"><small>{{str_pad($number, 2, '0', STR_PAD_LEFT)}}</small><h3 data-show="title"></h3><p data-show="text"></p></div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-check"></i> {{$item ? 'Update '.$label : 'Save '.$label}}</button>
                                <button type="submit" name="save_new" value="1" class="btn btn-outline-primary btn-block"><i class="fa fa-plus"></i> Save &amp; Add Another</button>
                                <p style="color:#777; margin:12px 0 0;">
                                    @if($isChip)
                                    Chips are shown under the animated hero panel of the <b>{{$page->name}}</b> page.
                                    @else
                                    Steps are shown in the <b>Project Workflow</b> section of the <b>{{$page->name}}</b> page and numbered automatically.
                                    @endif
                                    Change the order from the list.
                                </p>
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
        var color = document.getElementById('color');
        var colorText = document.getElementById('colorText');
        var placeholders = {title: 'Title', text: 'Short text'};

        function render() {
            document.querySelectorAll('[data-preview]').forEach(function (input) {
                var key = input.getAttribute('data-preview');
                var target = preview.querySelector('[data-show="' + key + '"]');
                if (!target) return;
                if (key === 'color') {
                    target.style.background = input.value;
                    return;
                }
                target.textContent = input.value.trim() || placeholders[key];
                target.style.opacity = input.value.trim() ? 1 : .45;
            });
        }

        document.querySelectorAll('[data-preview]').forEach(function (input) {
            input.addEventListener('input', render);
        });
        if (color) {
            color.addEventListener('input', function () { colorText.value = color.value; });
            colorText.addEventListener('input', function () {
                if (/^#[0-9a-fA-F]{6}$/.test(colorText.value)) { color.value = colorText.value; render(); }
            });
            document.querySelectorAll('[data-color]').forEach(function (button) {
                button.addEventListener('click', function () {
                    color.value = colorText.value = button.getAttribute('data-color');
                    render();
                });
            });
        }
        render();
    })();
</script>
@endpush
