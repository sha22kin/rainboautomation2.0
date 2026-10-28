{{-- One certificate, A4 landscape (1123 × 794 px). $t = template texts, $v = placeholder values.
     Every text carries data-tpl so the template editor can refresh it while typing. --}}
@php $fill = fn ($key) => App\Support\CertificateTemplate::fill($t[$key], $v); @endphp
<div class="cert" style="--accent:{{ $t['color'] ?: '#1f5fa8' }}">
    <div class="cert-frame">
        <span class="cert-corner tl"></span><span class="cert-corner tr"></span><span class="cert-corner bl"></span><span class="cert-corner br"></span>
        <div class="cert-org" data-tpl="org_name">{{ $fill('org_name') }}</div>
        <div class="cert-tagline" data-tpl="org_tagline">{{ $fill('org_tagline') }}</div>
        <div class="cert-title" data-tpl="title">{{ $fill('title') }}</div>
        <div class="cert-subtitle" data-tpl="subtitle">{{ $fill('subtitle') }}</div>
        <div class="cert-intro" data-tpl="intro">{{ $fill('intro') }}</div>
        <div class="cert-name" data-tpl="name_line">{{ $fill('name_line') }}</div>
        <div class="cert-body" data-tpl="body">{{ $fill('body') }}</div>
        @if(trim($v['{points}'] ?? '') !== '')<div class="cert-points" data-tpl="points_line">{{ $fill('points_line') }}</div>@endif
        <div class="cert-note" data-tpl="note">{{ $fill('note') }}</div>
        <div class="cert-signs">
            <div class="cert-sign"><span class="cert-line"></span><b data-tpl="sign1_name">{{ $fill('sign1_name') }}</b><small data-tpl="sign1_title">{{ $fill('sign1_title') }}</small></div>
            <div class="cert-seal"><span>★</span></div>
            <div class="cert-sign"><span class="cert-line"></span><b data-tpl="sign2_name">{{ $fill('sign2_name') }}</b><small data-tpl="sign2_title">{{ $fill('sign2_title') }}</small></div>
        </div>
        <div class="cert-footer" data-tpl="footer">{{ $fill('footer') }}</div>
    </div>
</div>
@once
<style>
.cert{width:1123px;height:794px;padding:26px;background:#fff;color:#1d2b3f;font-family:Georgia,'Times New Roman',serif;box-sizing:border-box;-webkit-print-color-adjust:exact;print-color-adjust:exact}
.cert *{box-sizing:border-box}
.cert-frame{position:relative;height:100%;border:3px solid var(--accent);outline:1px solid var(--accent);outline-offset:-12px;padding:46px 80px 30px;display:flex;flex-direction:column;align-items:center;text-align:center;background:radial-gradient(circle at 50% 40%,color-mix(in srgb,var(--accent) 6%,#fff),#fff 70%)}
.cert-corner{position:absolute;width:58px;height:58px;border:6px solid var(--accent)}
.cert-corner.tl{top:-3px;left:-3px;border-right:0;border-bottom:0}.cert-corner.tr{top:-3px;right:-3px;border-left:0;border-bottom:0}
.cert-corner.bl{bottom:-3px;left:-3px;border-right:0;border-top:0}.cert-corner.br{bottom:-3px;right:-3px;border-left:0;border-top:0}
.cert-org{font-family:'Segoe UI',Arial,sans-serif;font-weight:800;letter-spacing:5px;font-size:20px;color:var(--accent)}
.cert-tagline{font-family:'Segoe UI',Arial,sans-serif;font-size:12px;letter-spacing:2px;text-transform:uppercase;color:#6b7a90;margin-top:3px}
.cert-title{font-size:64px;line-height:1;margin-top:34px;letter-spacing:2px;color:#13243d;text-transform:uppercase}
.cert-subtitle{font-size:22px;font-style:italic;color:var(--accent);margin-top:6px}
.cert-intro{font-size:17px;color:#55657c;margin-top:32px}
.cert-name{font-size:44px;font-style:italic;color:#13243d;margin-top:8px;padding:0 40px 6px;border-bottom:2px solid color-mix(in srgb,var(--accent) 45%,#fff);min-width:420px}
.cert-body{font-size:17px;line-height:1.6;color:#3c4b61;max-width:760px;margin-top:20px;white-space:pre-line}
.cert-points{font-family:'Segoe UI',Arial,sans-serif;font-size:15px;font-weight:700;color:var(--accent);margin-top:10px;letter-spacing:.5px}
.cert-note{font-size:15px;color:#55657c;margin-top:12px;font-style:italic}
.cert-signs{display:flex;align-items:flex-end;justify-content:space-between;width:100%;margin-top:auto}
.cert-sign{width:250px;display:flex;flex-direction:column;align-items:center;font-family:'Segoe UI',Arial,sans-serif}
.cert-line{width:100%;border-top:1.5px solid #1d2b3f;margin-bottom:7px}
.cert-sign b{font-size:15px}.cert-sign small{font-size:12px;color:#6b7a90}
.cert-seal{width:92px;height:92px;border-radius:50%;display:grid;place-items:center;background:var(--accent);color:#fff;font-size:34px;box-shadow:0 0 0 5px #fff,0 0 0 7px var(--accent)}
.cert-footer{font-family:'Segoe UI',Arial,sans-serif;font-size:11px;color:#7d8ba0;margin-top:18px;letter-spacing:.5px}
</style>
@endonce
