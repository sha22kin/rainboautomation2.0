{{-- Profile banner: photo, name, registration number, contact; $account, $actions = 'profile' or 'edit' --}}
<div class="sp-hero">
    <div class="sp-cover"></div>
    <div class="sp-hero-body">
        <div class="sp-photo">
            @if($account->photoUrl())
            <img src="{{$account->photoUrl()}}" alt="{{$account->name}}">
            @else
            <span aria-hidden="true">{{$account->initials()}}</span>
            @endif
        </div>
        <div class="sp-id">
            <h1>{{$account->name}}</h1>
            <span class="sp-reg"><i class="fa-solid fa-id-card"></i> Registration No: {{$account->code}}</span>
            <div class="sp-chips">
                <span><i class="fa-regular fa-envelope"></i> {{$account->email}}</span>
                <span><i class="fa-solid fa-mobile-screen"></i> {{$account->phone}}</span>
                <span><i class="fa-regular fa-calendar"></i> Member since {{$account->created_at?->format('M Y')}}</span>
            </div>
        </div>
        <div class="sp-actions">
            @if($actions == 'profile')
            <a class="sp-btn sp-btn-primary" href="{{route('student.profile.edit')}}"><i class="fa-solid fa-user-pen"></i> Edit Profile</a>
            @else
            <a class="sp-btn sp-btn-light" href="{{route('student.profile')}}"><i class="fa-solid fa-arrow-left"></i> Back to Profile</a>
            @endif
            <form action="{{route('student.logout')}}" method="post">
                @csrf
                <button type="submit" class="sp-btn sp-btn-danger"><i class="fa-solid fa-right-from-bracket"></i> Log Out</button>
            </form>
        </div>
    </div>
</div>
