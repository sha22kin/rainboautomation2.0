@php
    $cmsUser = Auth::user();
    $initials = collect(preg_split('/\s+/', trim($cmsUser->name ?: 'RA')))->filter()->take(2)->map(fn ($w) => mb_strtoupper(mb_substr($w, 0, 1)))->implode('') ?: 'RA';
@endphp
<div class="profile"><span class="chip">● Online</span><div class="sub">{{ $cmsUser->permission ? Str::title($cmsUser->permission->name) : 'Administrator' }}</div><button type="button" class="avatar" onclick="toggleUserMenu(event)" title="{{ $cmsUser->name }}" aria-haspopup="true">{{ $initials }}</button><div class="usermenu" id="userMenu"><div class="usermenu-head"><b>{{ $cmsUser->name }}</b><span class="muted">{{ $cmsUser->email }}</span></div><a href="{{ route('admin.myProfile') }}">My Profile</a><a href="{{ config('cms.website_url') }}" target="_blank" rel="noopener">Visit Website ↗</a><form method="post" action="{{ route('logout') }}">@csrf<button type="submit" class="danger">Logout</button></form></div></div>
