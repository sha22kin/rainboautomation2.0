@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle($card ? 'Card Edit' : 'Add Card')}}</title>
@endsection
@push('css')
<style type="text/css">
    /* Live preview of the card as it looks on the website */
    .nx-card-preview {
        --accent: #287dcc;
        position: relative; overflow: hidden; padding: 27px 24px; border-radius: 22px;
        background: #fff; border: 1px solid #e8eef4; box-shadow: 0 10px 30px rgba(23, 40, 63, .08);
    }
    .nx-card-preview::before { content: ""; position: absolute; top: 0; left: 0; right: 0; height: 4px; background: var(--accent); }
    .nx-card-preview .preview-icon {
        display: inline-flex; align-items: center; justify-content: center; width: 54px; height: 54px; border-radius: 16px; font-size: 26px;
        background: color-mix(in srgb, var(--accent) 13%, #fff);
    }
    /* Services cards: icon on a solid accent background */
    .nx-card-preview.is-solid .preview-icon { background: var(--accent); color: #fff; border-radius: 12px; }
    .nx-card-preview h3 { font-size: 18px; color: #243249; margin: 15px 0 9px; font-weight: 700; word-break: break-word; }
    .nx-card-preview p { color: #63748a; font-size: 13px; line-height: 1.75; margin: 0; word-break: break-word; }
    .nx-color-row { display: flex; align-items: center; gap: 10px; }
    .nx-color-row input[type=color] { width: 46px; height: 38px; padding: 3px; flex: 0 0 46px; }
    .nx-color-presets { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 10px; }
    .nx-color-presets button { width: 26px; height: 26px; border-radius: 50%; border: 2px solid var(--nx-card, #fff); box-shadow: 0 0 0 1px var(--nx-line, #e6ecf3); cursor: pointer; padding: 0; }
    .nx-color-presets button:hover { transform: scale(1.12); }
    .nx-icon-input { font-size: 24px !important; text-align: center; height: 54px !important; max-width: 90px; }
</style>
@endpush
@section('contents')

@php
    $title = $card ? 'Card Edit' : 'Add Card';
    $value = function ($key) use ($data) { return old($key, $data[$key] ?? ''); };
    $presets = ['#29b65d', '#2683d5', '#8847bd', '#ffae22', '#f04469', '#18a4a7', '#25ae7b', '#d28d19', '#22a5b3'];
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
            @if($card)
            <a class="btn btn-outline-primary" href="{{route('admin.solutionCardsAction',[$section,'create'])}}">Add Card</a>
            @endif
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')
        <form action="{{route('admin.solutionCardsAction', $card ? [$section,'edit',$card->id] : [$section,'create'])}}" method="post">
            @csrf
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Card Details <small style="color:#999;">({{$sectionTitle}})</small></h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-3 col-4">
                                        <label for="icon">Icon <small style="color:#999;">(emoji)</small></label>
                                        <input type="text" class="form-control nx-icon-input {{$errors->has('icon')?'error':''}}" id="icon" name="icon" value="{{$value('icon')}}" maxlength="20" placeholder="✦" data-preview="icon" />
                                        @if ($errors->has('icon'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('icon') }}</p>
                                        @endif
                                    </div>
                                    <div class="form-group col-md-9 col-8">
                                        <label for="title">Card Title *</label>
                                        <input type="text" class="form-control {{$errors->has('title')?'error':''}}" id="title" name="title" value="{{$value('title')}}" maxlength="100" placeholder="Enter Card Title" required data-preview="title" style="height:54px;" />
                                        @if ($errors->has('title'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('title') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="description">Description</label>
                                    <textarea class="form-control {{$errors->has('description')?'error':''}}" id="description" name="description" rows="4" maxlength="500" placeholder="Short description shown on the card" data-preview="description">{{$value('description')}}</textarea>
                                    @if ($errors->has('description'))
                                    <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('description') }}</p>
                                    @endif
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label for="color">Accent Color *</label>
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
                            </div>
                        </div>
                    </div>

                    @if($section == 'services')
                    {{-- The same card is shown on the Home page "Our Core Services" section with its own design --}}
                    @php
                        $checked = function ($key) use ($data) { return old('_home_form') ? (bool)old($key) : (bool)($data[$key] ?? true); };
                        $homeColors = ['#2eb453' => 'Green', '#ec2331' => 'Red', '#2684d9' => 'Blue', '#8b28a4' => 'Purple', '#f3a51b' => 'Gold'];
                        $linkPages = ['about' => 'About Us', 'services' => 'Services', 'solar' => 'Solar Solution', 'training' => 'Training Academy', 'industrial' => 'Industrial Solution', 'contact' => 'Contact Us'];
                    @endphp
                    <input type="hidden" name="_home_form" value="1" />
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Show On</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="show_services" name="show_services" value="1" {{$checked('show_services') ? 'checked' : ''}} />
                                            <label class="custom-control-label" for="show_services">Services page <small style="color:#999;">(Our Core Engineering Services)</small></label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="show_home" name="show_home" value="1" {{$checked('show_home') ? 'checked' : ''}} data-home-toggle />
                                            <label class="custom-control-label" for="show_home">Home page <small style="color:#999;">(Our Core Services)</small></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" data-home-fields>
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Home Page Card <small style="color:#999;">(Our Core Services)</small></h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <div class="row">
                                    <div class="form-group col-md-9">
                                        <label for="badge">Badge</label>
                                        <input type="text" class="form-control {{$errors->has('badge')?'error':''}}" id="badge" name="badge" value="{{$value('badge')}}" maxlength="100" placeholder="e.g. Engineering Laboratory Equipment" />
                                    </div>
                                    <div class="form-group col-md-3">
                                        <label for="home_order">Home Order</label>
                                        <input type="number" class="form-control {{$errors->has('home_order')?'error':''}}" id="home_order" name="home_order" value="{{$value('home_order')}}" min="0" max="999" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label for="home_description">Home Description <small style="color:#999;">(empty = the description above)</small></label>
                                    <textarea class="form-control {{$errors->has('home_description')?'error':''}}" id="home_description" name="home_description" rows="3" maxlength="1000">{{$value('home_description')}}</textarea>
                                </div>
                                <div class="form-group">
                                    <label for="points">Points <small style="color:#999;">(one per line – shown as a tick list)</small></label>
                                    <textarea class="form-control {{$errors->has('points')?'error':''}}" id="points" name="points" rows="3" maxlength="1000">{{old('points', implode("\n", $data['points'] ?? []))}}</textarea>
                                </div>
                                <div class="row">
                                    <div class="form-group col-md-4">
                                        <label for="link_page">Link</label>
                                        <select class="form-control" id="link_page" name="link_page" data-link-page>
                                            <option value="">No link</option>
                                            @foreach($linkPages as $route => $name)
                                            <option value="{{$route}}" {{$value('link_page') == $route ? 'selected' : ''}}>{{$name}} page</option>
                                            @endforeach
                                            <option value="custom" {{$value('link_page') == 'custom' ? 'selected' : ''}}>Custom URL</option>
                                        </select>
                                    </div>
                                    <div class="form-group col-md-8" data-link-text>
                                        <label for="link_text">Link Text <small style="color:#999;">({count} = number of courses)</small></label>
                                        <input type="text" class="form-control {{$errors->has('link_text')?'error':''}}" id="link_text" name="link_text" value="{{$value('link_text')}}" maxlength="100" placeholder="e.g. Explore Industrial Solutions" />
                                    </div>
                                    <div class="form-group col-md-12" data-link-url>
                                        <label for="link_url">Custom URL *</label>
                                        <input type="text" class="form-control {{$errors->has('link_url')?'error':''}}" id="link_url" name="link_url" value="{{$value('link_url')}}" maxlength="500" placeholder="https://" />
                                        @if ($errors->has('link_url'))
                                        <p style="color: red; margin: 0; font-size: 10px;">{{ $errors->first('link_url') }}</p>
                                        @endif
                                    </div>
                                </div>
                                <div class="form-group" style="margin-bottom:0;">
                                    <label>Home Accent Color <small style="color:#999;">(empty = the accent color above)</small></label>
                                    <div class="nx-color-presets" style="margin-top:4px;">
                                        <label style="display:flex; align-items:center; gap:6px; margin:0 10px 0 0; font-weight:400;"><input type="radio" name="home_color" value="" {{$value('home_color') ? '' : 'checked'}} /> Same</label>
                                        @foreach($homeColors as $hex => $name)
                                        <label style="display:flex; align-items:center; gap:6px; margin:0 10px 0 0; font-weight:400;"><input type="radio" name="home_color" value="{{$hex}}" {{strtolower($value('home_color')) == $hex ? 'checked' : ''}} /><span style="display:inline-block; width:18px; height:18px; border-radius:50%; background:{{$hex}};"></span> {{$name}}</label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                            <h4 class="card-title">Website Preview</h4>
                        </div>
                        <div class="card-content">
                            <div class="card-body">
                                <article class="nx-card-preview {{str_starts_with($section, 'services') ? 'is-solid' : ''}}" id="cardPreview">
                                    <span class="preview-icon" data-show="icon"></span>
                                    <h3 data-show="title"></h3>
                                    <p data-show="description"></p>
                                </article>
                            </div>
                        </div>
                    </div>
                    <div class="card">
                        <div class="card-content">
                            <div class="card-body">
                                <button type="submit" class="btn btn-primary btn-block"><i class="fa fa-check"></i> {{$card ? 'Update Card' : 'Save Card'}}</button>
                                <button type="submit" name="save_new" value="1" class="btn btn-outline-primary btn-block"><i class="fa fa-plus"></i> Save &amp; Add Another</button>
                                <p style="color:#777; margin:12px 0 0;">Cards are shown in the <b>{{$sectionTitle}}</b> section of the <b>{{$page->name}}</b> page. Change the order from the list.</p>
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
        var preview = document.getElementById('cardPreview');
        var color = document.getElementById('color');
        var colorText = document.getElementById('colorText');
        var placeholders = {icon: '✦', title: 'Card title', description: 'Short description shown on the card'};

        function render() {
            document.querySelectorAll('[data-preview]').forEach(function (input) {
                var key = input.getAttribute('data-preview');
                if (key === 'color') {
                    preview.style.setProperty('--accent', input.value);
                    return;
                }
                var target = preview.querySelector('[data-show="' + key + '"]');
                target.textContent = input.value.trim() || placeholders[key];
                target.style.opacity = input.value.trim() ? 1 : .45;
            });
        }

        document.querySelectorAll('[data-preview]').forEach(function (input) {
            input.addEventListener('input', render);
        });
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
        render();

        // Home page card fields: only when "Home page" is ticked; custom URL only for "Custom URL"
        var homeToggle = document.querySelector('[data-home-toggle]');
        if (homeToggle) {
            var homeFields = document.querySelector('[data-home-fields]');
            var linkPage = document.querySelector('[data-link-page]');
            var sync = function () {
                homeFields.style.display = homeToggle.checked ? '' : 'none';
                document.querySelector('[data-link-url]').style.display = linkPage.value === 'custom' ? '' : 'none';
                document.querySelector('[data-link-text]').style.display = linkPage.value ? '' : 'none';
            };
            homeToggle.addEventListener('change', sync);
            linkPage.addEventListener('change', sync);
            sync();
        }
    })();
</script>
@endpush
