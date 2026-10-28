@extends('cms.layouts.app', ['erpPage' => 'certificate-template'])
@section('screen')
@php use App\Support\CertificateTemplate; @endphp
@include('cms.erp.partials.title', ['t' => 'Certificate Template', 'description' => 'Edit every text of the printed certificate; {placeholders} are filled for each student', 'button' => '<form method="post" action="'.route('erp.resetCertificateTemplate').'" onsubmit="return confirm(\'Put back the original certificate texts?\')">'.csrf_field().'<button class="btn alt">Reset to Default</button></form>'])
<div class="certedit">
    <div class="panel">
        <form method="post" action="{{ route('erp.saveCertificateTemplate') }}" id="certForm">@csrf
            <div class="formgrid">
                @foreach(CertificateTemplate::FIELDS as $key => [$label, $default, $type])
                <div class="field {{ in_array($key, ['body', 'title', 'org_name', 'name_line', 'footer']) ? 'full' : '' }}"><label>{{ $label }}</label>
                    @if($type == 'textarea')<textarea name="{{ $key }}" rows="3" maxlength="600" data-field="{{ $key }}">{{ $template[$key] }}</textarea>
                    @else<input name="{{ $key }}" type="{{ $type }}" value="{{ $template[$key] }}" maxlength="200" data-field="{{ $key }}">@endif
                </div>
                @endforeach
            </div>
            <div class="placeholders"><b>Placeholders</b> <span class="muted">— click one to insert it into the field you are editing</span><div>@foreach(CertificateTemplate::PLACEHOLDERS as $ph => $about)<button type="button" class="chip" title="{{ $about }}" onclick="insertPlaceholder('{{ $ph }}')">{{ $ph }}</button>@endforeach</div></div>
            <div class="foot-actions"><button class="btn">Save Template</button></div>
        </form>
    </div>
    <div class="panel certpreview-panel"><div class="toolbar"><h2>Live Preview</h2><span class="muted">Sample student</span></div><div class="certpreview" id="certPreview"><div class="certscale">@include('cms.erp.partials.certificate', ['t' => $template, 'v' => $sample])</div></div></div>
</div>
@endsection
@push('js')
<script>
// Live preview: placeholders get the sample values while typing; the preview is scaled to its panel
const CERT_SAMPLE=@json($sample);let certField=null;
function certFill(text){return Object.entries(CERT_SAMPLE).reduce((t,[k,v])=>t.split(k).join(v),text)}
function certRefresh(el){let key=el.dataset.field;if(key==='color'){document.querySelector('#certPreview .cert').style.setProperty('--accent',el.value);return}document.querySelectorAll('#certPreview [data-tpl="'+key+'"]').forEach(n=>n.textContent=certFill(el.value))}
function certScale(){let box=document.getElementById('certPreview'),s=box.clientWidth/1123;box.querySelector('.certscale').style.transform='scale('+s+')';box.style.height=(794*s)+'px'}
function insertPlaceholder(ph){let el=certField;if(!el||el.type==='color')return toast('Click in a text field first');let a=el.selectionStart??el.value.length,b=el.selectionEnd??a;el.value=el.value.slice(0,a)+ph+el.value.slice(b);el.focus();el.selectionStart=el.selectionEnd=a+ph.length;certRefresh(el)}
document.querySelectorAll('#certForm [data-field]').forEach(el=>{el.addEventListener('input',()=>certRefresh(el));el.addEventListener('focus',()=>certField=el)});
window.addEventListener('resize',certScale);certScale();
</script>
@endpush
