<!doctype html><html lang="en"><head><meta charset="utf-8"><title>{{ $certificate->code }} — {{ $certificate->student?->name }} | Rainbow Automation</title>
<style>@page{size:A4 landscape;margin:0}body{margin:0;background:#e9eef5;font-family:'Segoe UI',Arial,sans-serif}.bar{display:flex;gap:8px;justify-content:center;padding:12px}.bar button{padding:7px 14px;border-radius:8px;border:1px solid #c8d5e5;background:#fff;font-weight:700;cursor:pointer}.bar button.main{background:#277ccd;color:#fff;border-color:#277ccd}.sheet{width:1123px;margin:0 auto 24px;box-shadow:0 10px 40px #0002}@media print{body{background:#fff}.bar{display:none}.sheet{margin:0;box-shadow:none}}</style>
</head><body>
<div class="bar"><button class="main" onclick="window.print()">⎙ Print Certificate</button><button onclick="window.close()">Close</button></div>
<div class="sheet">@include('cms.erp.partials.certificate', ['t' => $template, 'v' => $values])</div>
<script>window.onload=()=>window.print()</script>
</body></html>
