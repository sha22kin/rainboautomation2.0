{{-- Card list table: $cards (PostExtra rows), $cardSection (URL key for solutionCardsAction) --}}
<div class="table-responsive">
    <table class="table table-striped table-bordered table-hover nx-card-table">
        <thead>
            <tr>
                <th style="width: 45px;">SL</th>
                <th style="width: 70px;">Icon</th>
                <th style="min-width: 260px;">Card</th>
                <th style="width: 110px;">Color</th>
                <th style="width: 80px;">Order</th>
                <th style="width: 150px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($cards as $i => $card)
            <tr>
                <td>{{$i + 1}}</td>
                <td style="text-align: center;"><span class="nx-card-icon" style="--rep-accent:{{$card->card('color')}}">{{$card->card('icon')}}</span></td>
                <td>
                    <a href="{{route('admin.solutionCardsAction',[$cardSection,'edit',$card->id])}}"><b>{{$card->card('title')}}</b></a>
                    <br />
                    <span class="nx-card-desc">{{\Illuminate\Support\Str::limit($card->card('description'), 110)}}</span>
                    @if($cardSection == 'services')
                    <br />
                    <span class="badge {{$card->card('show_services') ? 'badge-success' : 'badge-secondary'}}">Services {{$card->card('show_services') ? '✓' : '✕'}}</span>
                    <span class="badge {{$card->card('show_home') ? 'badge-info' : 'badge-secondary'}}">Home {{$card->card('show_home') ? '#'.$card->card('home_order') : '✕'}}</span>
                    @endif
                </td>
                <td><span class="nx-card-swatch" style="background:{{$card->card('color')}}"></span> <span class="nx-rep-hex">{{$card->card('color')}}</span></td>
                <td class="orderBtns" style="text-align: center;">
                    <a href="{{route('admin.solutionCardsAction',[$cardSection,'up',$card->id])}}" title="Move up"><i class="fa fa-arrow-up"></i></a>
                    <a href="{{route('admin.solutionCardsAction',[$cardSection,'down',$card->id])}}" title="Move down"><i class="fa fa-arrow-down"></i></a>
                </td>
                <td style="padding: 5px;">
                    <a href="{{route('admin.solutionCardsAction',[$cardSection,'edit',$card->id])}}" class="btn btn-sm btn-info">Edit</a>
                    <a href="{{route('admin.solutionCardsAction',[$cardSection,'delete',$card->id])}}" class="btn btn-sm btn-danger" onclick="return confirm('Are You Want To Delete?')">Delete</a>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" style="text-align: center; color: #999;">No card added. Click "Add Card".</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
