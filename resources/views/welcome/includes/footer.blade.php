@php $homeLink = request()->routeIs('index') ? '' : route('index'); $general = general(); @endphp
<footer class="ra-footer">
{{-- Bootstrap grid. Phones: brand full width, Quick Links + Business Segments side by side, Featured Programs full width.
     Tablets (md): 2 x 2. Desktops (xl): 4 columns at the design widths 1.2 : .9 : .9 : 1. --}}
<div class="wrap footer-top">
<div class="row gx-3 gx-md-4 gy-4">
<div class="col-12 col-md-6 col-xl footer-brand">
<img alt="{{$general->title}}" src="{{asset($general->logo())}}"/>
<strong>{{$general->title}}</strong>
<p>
          Engineering Laboratory Equipment • e-GP Projects • Solar System Complete Solution • Professional Technical Training.
        </p>
</div>
<div class="col-6 col-xl footer-col">
<h4>Quick Links</h4>
<a href="{{$homeLink}}#home">Home</a>
<a href="{{pageLink('services')}}">Services</a>
<a href="{{pageLink('training')}}">Training Academy</a>
<a href="{{pageLink('contact')}}">Contact</a>
</div>
<div class="col-6 col-xl footer-col">
<h4>Business Segments</h4>
<span>Lab Equipment Supply</span>
<span>e-GP &amp; Tender Projects</span>
<span>Solar System Solution</span>
<span>Skill Development Training</span>
</div>
<div class="col-12 col-md-6 col-xl footer-col">
<h4>Featured Programs</h4>
<span>PLC / VFD / HMI / SCADA</span>
<span>IIoT / Servo / Pneumatic</span>
<span>CAD/CAM / SolidWorks / AutoCAD</span>
<span>CNC / Mechatronics / Manufacturing</span>
</div>
</div>
</div>
<div class="wrap footer-bottom">
<div>{{$general->copyright_text ?: '© '.date('Y').' '.$general->title.'. All rights reserved.'}}</div>
<div>Designed in a logo-matched minimal premium corporate style.</div>
</div>
</footer>
