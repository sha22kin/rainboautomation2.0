{{-- Generic layout (login, register, error and CMS pages) wrapped in the Rainbow Automation header/footer --}}
@extends('welcome.layouts.master')

@prepend('css')
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"/>
<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet"/>
<link href="{{asset('welcome/css/pages.css')}}" rel="stylesheet"/>
@endprepend

@section('body')
@include('welcome.includes.header')
<main>
@yield('contents')
</main>
@include('welcome.includes.footer')
<script src="https://cdn.jsdelivr.net/npm/jquery@3.7.1/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@endsection
