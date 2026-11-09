<!doctype html><html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex, nofollow">
<title>{{ $certificate->code }} — {{ $certificate->student?->name }} | Rainbow Automation</title>
{{-- A student's own certificate (StudentPortalController@certificate): view, download as PDF (made in the browser from this page) or print --}}
<style>
@page{size:A4 landscape;margin:0}
body{margin:0;background:#e9eef5;font-family:'Segoe UI',Arial,sans-serif;color:#1d2b3f}
.bar{display:flex;gap:8px;justify-content:center;flex-wrap:wrap;padding:14px}
.bar a,.bar button{display:inline-flex;align-items:center;gap:6px;padding:9px 16px;border-radius:10px;border:1px solid #c8d5e5;background:#fff;color:#22466d;font:700 13px 'Segoe UI',Arial,sans-serif;cursor:pointer;text-decoration:none}
.bar .main{background:#277ccd;color:#fff;border-color:#277ccd}
.bar button[disabled]{opacity:.6;cursor:wait}
.stage{overflow:hidden;margin:0 auto 24px}
.sheet{width:1123px;transform-origin:top left;box-shadow:0 10px 40px #0002;background:#fff}
@media print{body{background:#fff}.bar{display:none}.stage{width:auto!important;height:auto!important;margin:0}.sheet{transform:none!important;box-shadow:none}}
</style>
</head><body>
<div class="bar">
    <button type="button" class="main" id="downloadPdf">⬇ Download PDF</button>
    <button type="button" onclick="window.print()">⎙ Print</button>
    <a href="{{ $backUrl ?? route('student.profile') }}">← {{ $backLabel ?? 'My Profile' }}</a>
</div>
<div class="stage" id="stage"><div class="sheet" id="sheet">@include('cms.erp.partials.certificate', ['t' => $template, 'v' => $values])</div></div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script>
(function () {
    var stage = document.getElementById('stage');
    var sheet = document.getElementById('sheet');
    // The certificate is A4 landscape (1123 x 794 px): shrink it to fit small screens
    function fit() {
        var scale = Math.min(1, (window.innerWidth - 24) / 1123);
        sheet.style.transform = 'scale(' + scale + ')';
        stage.style.width = (1123 * scale) + 'px';
        stage.style.height = (794 * scale) + 'px';
    }
    fit();
    window.addEventListener('resize', fit);

    // html2canvas cannot read modern colours such as color-mix(), which the browser reports as "color(srgb r g b)":
    // the copy that is drawn gets the same colours written as rgba()
    var COLOR_PROPS = ['color', 'background-color', 'background-image', 'border-top-color', 'border-right-color', 'border-bottom-color', 'border-left-color', 'outline-color', 'box-shadow', 'text-shadow'];
    function toRgba(value) {
        return value.replace(/color\(srgb\s+([\d.]+)\s+([\d.]+)\s+([\d.]+)(?:\s*\/\s*([\d.]+%?))?\)/g, function (m, r, g, b, a) {
            var alpha = a === undefined ? 1 : (a.slice(-1) === '%' ? parseFloat(a) / 100 : parseFloat(a));
            return 'rgba(' + Math.round(r * 255) + ',' + Math.round(g * 255) + ',' + Math.round(b * 255) + ',' + alpha + ')';
        });
    }
    function plainColors(original, copy) {
        var from = [original].concat([].slice.call(original.querySelectorAll('*')));
        var to = [copy].concat([].slice.call(copy.querySelectorAll('*')));
        from.forEach(function (el, i) {
            if (!to[i]) return;
            var style = getComputedStyle(el);
            COLOR_PROPS.forEach(function (prop) {
                var value = style.getPropertyValue(prop);
                if (value && value.indexOf('color(') !== -1) to[i].style.setProperty(prop, toRgba(value));
            });
        });
    }

    var button = document.getElementById('downloadPdf');
    var fileName = @json(\Illuminate\Support\Str::slug($certificate->code.' '.($certificate->student?->name ?? 'certificate')).'.pdf');
    function download() {
        if (!window.html2canvas || !window.jspdf) { window.print(); return; }
        button.disabled = true;
        button.textContent = 'Preparing PDF…';
        var transform = sheet.style.transform;
        sheet.style.transform = 'none';
        html2canvas(sheet, {
            scale: 2, backgroundColor: '#ffffff', useCORS: true,
            onclone: function (doc) { plainColors(sheet, doc.getElementById('sheet')); }
        }).then(function (canvas) {
            sheet.style.transform = transform;
            var pdf = new window.jspdf.jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
            pdf.addImage(canvas.toDataURL('image/jpeg', 0.95), 'JPEG', 0, 0, 297, 210);
            pdf.save(fileName);
        }).catch(function () {
            sheet.style.transform = transform;
            window.print();
        }).finally(function () {
            button.disabled = false;
            button.textContent = '⬇ Download PDF';
        });
    }
    button.addEventListener('click', download);
    // Opened with the profile's "Download" button
    if (/[?&]download=1/.test(location.search)) {
        window.addEventListener('load', function () { setTimeout(download, 300); });
    }
})();
</script>
</body></html>
