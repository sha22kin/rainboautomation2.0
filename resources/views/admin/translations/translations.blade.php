@extends(adminTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Website Translations')}}</title>
@endsection
@push('css')
<style type="text/css">
    .nx-tr-en { color: #243249; font-weight: 600; white-space: pre-wrap; word-break: break-word; }
    .nx-tr-table textarea { min-height: 62px; }
    .nx-scan-log { margin-top: 10px; color: #607086; font-size: 12px; }
    #scanFrame { position: absolute; left: -10000px; top: 0; width: 1280px; height: 900px; border: 0; }
</style>
@endpush
@section('contents')

{{-- Bangla for website texts added from the CMS ($rows = PostExtra type 14, content {en, bn}) --}}
<div class="content-header row">
    <div class="content-header-left col-md-6 col-12 mb-2">
        <h3 class="content-header-title mb-0">Website Translations</h3>
        <div class="row breadcrumbs-top">
            <div class="breadcrumb-wrapper col-12">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{route('admin.dashboard')}}">Dashboard </a></li>
                    <li class="breadcrumb-item active">Website Translations</li>
                </ol>
            </div>
        </div>
    </div>
    <div class="content-header-right col-md-6 col-12 mb-md-0 mb-2">
        <div class="btn-group float-md-right" role="group">
            <button type="button" class="btn btn-primary" id="scanBtn"><i class="fa fa-search"></i> Scan Website</button>
        </div>
    </div>
</div>

<div class="content-body">
    <section class="basic-elements">
        @include(adminTheme().'alerts')

        <div class="card">
            <div class="card-content">
                <div class="card-body">
                    <p style="margin:0;">
                        The <b>বাংলা</b> button of the website translates every text it has a Bangla version for.
                        Texts you add or change in the CMS (pages, courses, Why Choose Us, …) need their Bangla here.
                        Click <b>Scan Website</b> after adding content: new English texts are listed under <b>Needs Bangla</b>.
                        A text without Bangla stays in English on the Bangla website.
                    </p>
                    <div class="nx-scan-log" id="scanLog"></div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                <div class="btn-group" role="group">
                    <a class="btn btn-sm {{$filter=='pending' ? 'btn-primary' : 'btn-outline-primary'}}" href="{{route('admin.translations',['filter'=>'pending'])}}">Needs Bangla ({{(int) $counts->pending}})</a>
                    <a class="btn btn-sm {{$filter=='done' ? 'btn-primary' : 'btn-outline-primary'}}" href="{{route('admin.translations',['filter'=>'done'])}}">Translated ({{(int) $counts->total - (int) $counts->pending}})</a>
                    <a class="btn btn-sm {{$filter=='all' ? 'btn-primary' : 'btn-outline-primary'}}" href="{{route('admin.translations',['filter'=>'all'])}}">All ({{(int) $counts->total}})</a>
                </div>
                <form action="{{route('admin.translations')}}" method="get" style="display:inline-flex; gap:6px; float:right;">
                    <input type="hidden" name="filter" value="{{$filter}}" />
                    <input type="text" class="form-control form-control-sm" name="search" value="{{request('search')}}" placeholder="Search text" />
                    <button class="btn btn-sm btn-outline-primary" type="submit"><i class="fa fa-search"></i></button>
                </form>
            </div>
            <div class="card-content">
                <div class="card-body">
                    <form action="{{route('admin.translations')}}" method="post">
                        @csrf
                        <div class="table-responsive">
                            <table class="table table-bordered nx-tr-table">
                                <thead>
                                    <tr>
                                        <th style="width:45%;">English (on the website)</th>
                                        <th>Bangla</th>
                                        <th style="width:110px;">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($rows as $row)
                                    @php $data = json_decode($row->content ?: '[]', true) ?: []; @endphp
                                    <tr>
                                        <td><div class="nx-tr-en">{{$data['en'] ?? ''}}</div></td>
                                        <td><textarea class="form-control" name="bn[{{$row->id}}]" rows="2" maxlength="2000" placeholder="বাংলা লিখুন">{{$data['bn'] ?? ''}}</textarea></td>
                                        <td style="padding:5px;">
                                            <a class="btn btn-sm btn-outline-info" href="https://translate.google.com/?sl=en&tl=bn&op=translate&text={{rawurlencode($data['en'] ?? '')}}" target="_blank" rel="noopener" title="Open in Google Translate">Help</a>
                                            <a class="btn btn-sm btn-danger" href="{{route('admin.translationsAction',['delete',$row->id])}}" onclick="return confirm('Are You Want To Delete?')">Delete</a>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="3" style="text-align:center; color:#999;">{{$filter=='pending' ? 'Every text has Bangla. Click "Scan Website" after adding new content.' : 'No text found.'}}</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                        @if($rows->count())
                        <button type="submit" class="btn btn-primary"><i class="fa fa-check"></i> Save Translations</button>
                        @endif
                    </form>
                    <div style="margin-top:15px;">{{$rows->links('pagination')}}</div>
                </div>
            </div>
        </div>

        <form action="{{route('admin.translationsAction','add')}}" method="post">
            @csrf
            <div class="card">
                <div class="card-header" style="border-bottom: 1px solid #e3ebf3;">
                    <h4 class="card-title">Add Text By Hand</h4>
                </div>
                <div class="card-content">
                    <div class="card-body">
                        <div class="row">
                            <div class="form-group col-md-6">
                                <label>English * <small style="color:#999;">(exactly as on the website)</small></label>
                                <textarea class="form-control" name="en" rows="2" maxlength="1000" required>{{old('en')}}</textarea>
                            </div>
                            <div class="form-group col-md-6">
                                <label>Bangla</label>
                                <textarea class="form-control" name="bn" rows="2" maxlength="2000">{{old('bn')}}</textarea>
                            </div>
                        </div>
                        <button type="submit" class="btn btn-outline-primary"><i class="fa fa-plus"></i> Add Text</button>
                    </div>
                </div>
            </div>
        </form>
        <iframe id="scanFrame" title="Website scan" aria-hidden="true"></iframe>
    </section>
</div>

@endsection
@push('js')
<script>
    (function () {
        var pages = @json($scanPages);
        var button = document.getElementById('scanBtn');
        var log = document.getElementById('scanLog');
        var frame = document.getElementById('scanFrame');

        // Each website page is opened in a hidden frame (in English); the language switch lists its texts without Bangla
        function scanPage(url) {
            return new Promise(function (resolve) {
                var done = false;
                var finish = function (texts) { if (!done) { done = true; resolve(texts); } };
                frame.onload = function () {
                    setTimeout(function () {
                        try { finish(frame.contentWindow.RAi18n ? frame.contentWindow.RAi18n.collect() : []); }
                        catch (e) { finish([]); }
                    }, 2500);
                };
                setTimeout(function () { finish([]); }, 20000);
                frame.src = url + (url.indexOf('?') < 0 ? '?' : '&') + 'ra-scan=1';
            });
        }

        button.addEventListener('click', async function () {
            button.disabled = true;
            var texts = [];
            for (var i = 0; i < pages.length; i++) {
                log.textContent = 'Scanning ' + (i + 1) + ' / ' + pages.length + ': ' + pages[i];
                texts = texts.concat(await scanPage(pages[i]));
            }
            log.textContent = 'Saving…';
            var response = await fetch(@json(route('admin.translationsAction', 'scan')), {
                method: 'POST',
                headers: {'Content-Type': 'application/json', 'X-CSRF-TOKEN': @json(csrf_token()), 'Accept': 'application/json'},
                body: JSON.stringify({texts: Array.from(new Set(texts))})
            });
            var result = response.ok ? await response.json() : null;
            if (!result) { log.textContent = 'Scan failed. Please try again.'; button.disabled = false; return; }
            log.textContent = result.added + ' new text(s) found.';
            setTimeout(function () { location.href = @json(route('admin.translations', ['filter' => 'pending'])); }, 900);
        });
    })();
</script>
@endpush
