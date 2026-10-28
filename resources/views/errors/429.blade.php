@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Too Many Requests')}}</title>
@endsection

@push('css')
<style type="text/css">
    .errorPage {
        text-align: center;
        padding: 20% 0;
    }
    .errorPage h1{
        font-size: 120px;
        color: #f1594c;
    }
    .btn-Success {
        background: #009688;
        color: white;
    }
</style>
@endpush
@section('contents')

<div class="container">
    <div class="row">
        <div class="col-md-3"></div>
        <div class="col-md-6">
            <div class="errorPage">
                <h1>429</h1>
                <h2>Oppos!</h2>
                <h5>Too Many Requests</h5><br><br>
                <a href="{{route('index')}}" class="btn btn-Success"><i class="fa fa-home"></i> Return Home</a>
            </div>
        </div>
    </div>
</div>

@endsection
@push('js')

@endpush

