@extends(adminTheme().'layouts.app') @section('title')
<title>{{websiteTitle('Dashboard')}}</title>
@endsection
@php
    $nxRole = Auth::user()->permission;
    $nxPerm = $nxRole ? (json_decode($nxRole->permission, true) ?: []) : [];
    $can = fn($group, $key) => isset($nxPerm[$group][$key]);
    $hour = (int) now()->format('G');
    $greeting = $hour < 12 ? 'Good morning' : ($hour < 17 ? 'Good afternoon' : 'Good evening');
@endphp
@section('contents')
<div class="content-body">

    <!-- Welcome -->
    <div class="nx-hero">
        <div class="row align-items-center">
            <div class="col-lg-8">
                <small>{{$greeting}}</small>
                <h2>Welcome back, {{Str::limit(Auth::user()->name, 28)}} 👋</h2>
                <p>Here is what is happening on {{general()->title}} today. Manage your content, pages and settings from one place.</p>
                <div class="nx-hero-actions">
                    @if($can('posts','add'))
                    <a class="nx-solid" href="{{route('admin.postsAction',['create'])}}"><i class="fa-solid fa-plus"></i> New Post</a>
                    @endif
                    @if($can('pages','add'))
                    <a href="{{route('admin.pagesAction','create')}}"><i class="fa-solid fa-file-circle-plus"></i> New Page</a>
                    @endif
                    <a href="{{route('index')}}" target="_blank" rel="noopener"><i class="fa-solid fa-globe"></i> View Website</a>
                </div>
            </div>
            <div class="col-lg-4 d-none d-lg-block">
                <div class="nx-hero-date">
                    <strong>{{now()->format('d')}}</strong>
                    <span>{{now()->format('l, F Y')}}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row">
        <div class="col-6 col-xl-3 mb-4">
            <a class="nx-stat nx-tone-blue" href="{{$can('services','list') ? route('admin.services') : 'javascript:void(0)'}}">
                <div class="nx-stat-icon"><i class="fa-solid fa-layer-group"></i></div>
                <h3>{{number_format($reports['services'])}}</h3>
                <p>Services</p>
                <span class="nx-chip"><i class="fa-solid fa-calendar-day"></i> This month</span>
            </a>
        </div>
        <div class="col-6 col-xl-3 mb-4">
            <a class="nx-stat nx-tone-green" href="{{$can('posts','list') ? route('admin.posts') : 'javascript:void(0)'}}">
                <div class="nx-stat-icon"><i class="fa-solid fa-file-lines"></i></div>
                <h3>{{number_format($reports['posts'])}}</h3>
                <p>Posts</p>
                <span class="nx-chip"><i class="fa-solid fa-calendar-day"></i> This month</span>
            </a>
        </div>
        <div class="col-6 col-xl-3 mb-4">
            <a class="nx-stat nx-tone-red" href="{{$can('pages','list') ? route('admin.pages') : 'javascript:void(0)'}}">
                <div class="nx-stat-icon"><i class="fa-solid fa-copy"></i></div>
                <h3>{{number_format($reports['pages'])}}</h3>
                <p>Pages</p>
                <span class="nx-chip"><i class="fa-solid fa-layer-group"></i> Total</span>
            </a>
        </div>
        <div class="col-6 col-xl-3 mb-4">
            <a class="nx-stat nx-tone-amber" href="{{$can('users','list') ? route('admin.usersCustomer') : 'javascript:void(0)'}}">
                <div class="nx-stat-icon"><i class="fa-solid fa-users"></i></div>
                <h3>{{number_format($reports['users'])}}</h3>
                <p>Users</p>
                <span class="nx-chip"><i class="fa-solid fa-circle-check"></i> Active</span>
            </a>
        </div>
    </div>

    <!-- Quick actions -->
    <div class="card">
        <div class="card-header d-flex align-items-center">
            <h4 class="card-title">Quick Actions</h4>
        </div>
        <div class="card-body">
            <div class="nx-quick-grid">
                @if($can('posts','add'))
                <a class="nx-quick nx-tone-blue" href="{{route('admin.postsAction',['create'])}}"><i class="fa-solid fa-pen-to-square"></i><div>New Post<small>Write an article</small></div></a>
                @endif
                @if($can('pages','add'))
                <a class="nx-quick nx-tone-red" href="{{route('admin.pagesAction','create')}}"><i class="fa-solid fa-file-circle-plus"></i><div>New Page<small>Add a page</small></div></a>
                @endif
                @if($can('medies','list'))
                <a class="nx-quick nx-tone-green" href="{{route('admin.medies')}}"><i class="fa-solid fa-photo-film"></i><div>Media<small>Upload files</small></div></a>
                @endif
                @if($can('menus','list'))
                <a class="nx-quick nx-tone-amber" href="{{route('admin.menus')}}"><i class="fa-solid fa-bars-staggered"></i><div>Menus<small>Edit navigation</small></div></a>
                @endif
                @if($can('sliders','list'))
                <a class="nx-quick nx-tone-blue" href="{{route('admin.sliders')}}"><i class="fa-solid fa-panorama"></i><div>Sliders<small>Home banners</small></div></a>
                @endif
                @if($can('appsSetting','general'))
                <a class="nx-quick nx-tone-green" href="{{route('admin.setting','general')}}"><i class="fa-solid fa-gear"></i><div>Settings<small>Site details</small></div></a>
                @endif
                <a class="nx-quick nx-tone-red" href="{{route('admin.myProfile')}}"><i class="fa-solid fa-user-tie"></i><div>My Profile<small>Your account</small></div></a>
            </div>
        </div>
    </div>

    <!-- Recent content -->
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Recent Posts</h4>
                    @if($can('posts','list'))
                    <a class="nx-card-link" href="{{route('admin.posts')}}">View all <i class="fa-solid fa-arrow-right"></i></a>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($posts->take(6) as $post)
                    <div class="nx-list-item">
                        <img class="nx-list-thumb" src="{{asset($post->image())}}" alt="">
                        <div class="nx-list-body">
                            <a href="{{route('admin.postsAction',['edit',$post->id])}}">{{$post->name ?: 'Untitled'}}</a>
                            <small><i class="fa-regular fa-calendar"></i> {{$post->created_at->format('d M Y')}} &middot; <i class="fa-regular fa-user"></i> {{Str::limit($post->user ? $post->user->name : 'No Author', 18)}}</small>
                        </div>
                        @if($post->status=='active')
                        <span class="badge badge-success">Active</span>
                        @else
                        <span class="badge badge-danger">{{ucfirst($post->status)}}</span>
                        @endif
                    </div>
                    @empty
                    <div class="nx-empty"><i class="fa-regular fa-file-lines"></i> No posts yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header d-flex align-items-center">
                    <h4 class="card-title">Recently Updated Pages</h4>
                    @if($can('pages','list'))
                    <a class="nx-card-link" href="{{route('admin.pages')}}">View all <i class="fa-solid fa-arrow-right"></i></a>
                    @endif
                </div>
                <div class="card-body">
                    @forelse($recentPages as $pg)
                    <div class="nx-list-item">
                        <img class="nx-list-thumb" src="{{asset($pg->image())}}" alt="">
                        <div class="nx-list-body">
                            <a href="{{route('admin.pagesAction',['edit',$pg->id])}}">{{$pg->name ?: 'Untitled'}}</a>
                            <small>
                                @if($pg->template)<i class="fa-solid fa-swatchbook"></i> {{$pg->template}} &middot; @endif
                                <i class="fa-regular fa-clock"></i> {{$pg->updated_at->diffForHumans()}}
                            </small>
                        </div>
                        <a href="{{route('pageView',$pg->slug?:'no-title')}}" target="_blank" rel="noopener" class="btn btn-sm btn-info" title="View page"><i class="fa-solid fa-eye"></i></a>
                    </div>
                    @empty
                    <div class="nx-empty"><i class="fa-regular fa-copy"></i> No pages yet.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
