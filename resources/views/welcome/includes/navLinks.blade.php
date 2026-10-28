@php
    $navItems = [
        'index' => 'Home',
        'about' => 'About Us',
        'services' => 'Services',
        'solar' => 'Solar Solution',
        'training' => 'Training Academy',
        'industrial' => 'Industrial Solution',
        'contact' => 'Contact',
    ];
@endphp
<nav aria-label="Main navigation" class="{{$navClass ?? 'ra-nav-five'}}"@isset($navId) id="{{$navId}}"@endisset>@foreach($navItems as $routeName => $label)@php $link = pageLink($routeName); @endphp<a @if(url()->current() == $link) class="current" @endif href="{{$link}}">{{$label}}</a>@endforeach</nav>
