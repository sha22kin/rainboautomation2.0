@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Buy a Course')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
@endsection
@push('css')
@include('welcome.student.partials.style')
<style>
.bc-head{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:12px;margin-bottom:6px}
.bc-head h1{margin:0;font-size:clamp(24px,3vw,32px);font-weight:800;color:#172338}
.bc-head p{margin:4px 0 0;color:#5d6f86}
.bc-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(250px,1fr));gap:14px}
.bc-card{position:relative;display:flex;flex-direction:column;border:2px solid #e3eaf2;border-radius:16px;background:#fff;overflow:hidden;cursor:pointer;transition:.18s}
.bc-card:hover{border-color:#9cc6ee;transform:translateY(-2px);box-shadow:0 12px 26px rgba(23,40,64,.08)}
.bc-card input{position:absolute;opacity:0;pointer-events:none}
.bc-card img{width:100%;height:120px;object-fit:cover;background:#eef4fb}
.bc-card .bc-body{padding:12px 14px 14px;display:flex;flex-direction:column;gap:6px;flex:1}
.bc-card h3{margin:0;font-size:15px;line-height:1.35;color:#172338;font-weight:800}
.bc-card .bc-meta{display:flex;flex-wrap:wrap;gap:6px;font-size:12px;color:#5d6f86}
.bc-card .bc-fee{margin-top:auto;font-size:18px;font-weight:800;color:#1e6fc4}
.bc-card .bc-tick{position:absolute;top:10px;right:10px;width:28px;height:28px;border-radius:50%;background:#fff;border:2px solid #d4dfeb;display:grid;place-items:center;color:transparent;font-size:13px}
.bc-card:has(input:checked){border-color:#287dcc;box-shadow:0 0 0 4px rgba(40,125,204,.14)}
.bc-card:has(input:checked) .bc-tick{background:#287dcc;border-color:#287dcc;color:#fff}
.bc-card:has(input:focus-visible){outline:3px solid #287dcc;outline-offset:2px}
.bc-pay{background:#f4f9ff;border:1px solid #d9e8f8;border-radius:14px;padding:14px 16px;color:#33506f;font-size:14px;line-height:1.7;margin-bottom:16px}
.bc-pay b{color:#172338}
.bc-sum{display:flex;justify-content:space-between;gap:10px;align-items:center;flex-wrap:wrap;padding:12px 16px;border-radius:12px;background:#fff;border:1px dashed #c7d8ea;margin-bottom:16px;font-size:14px;color:#40536b}
.bc-sum b{font-size:18px;color:#172338}
.sp-field textarea{width:100%;border:1px solid #dbe4ee;border-radius:12px;padding:12px 14px;font-size:14px;background:#fbfcfe;font-family:inherit;resize:vertical}
.sp-field input[type=number]{width:100%;border:1px solid #dbe4ee;border-radius:12px;padding:12px 14px;font-size:14px;background:#fbfcfe}
</style>
@endpush
@section('contents')
{{-- Student "Buy a Course" (StudentPortalController@buyCourse): the request waits for the admin, approval enrolls the student --}}
<section class="sp-wrap">
    <div class="wrap">
        <div class="bc-head">
            <div>
                <h1><i class="fa-solid fa-cart-shopping" style="color:#287dcc"></i> Buy a Course</h1>
                <p>Choose a course and send the request. Once our office approves it, the course is added to your profile.</p>
            </div>
            <a class="sp-btn sp-btn-light" href="{{route('student.profile')}}"><i class="fa-solid fa-arrow-left"></i> Back to Profile</a>
        </div>

        @if(session('error'))
        <div class="sp-alert err" role="alert"><i class="fa-solid fa-circle-exclamation"></i> {{session('error')}}</div>
        @elseif($errors->any())
        <div class="sp-alert err" role="alert"><i class="fa-solid fa-circle-exclamation"></i> {{$errors->first()}}</div>
        @endif

        @if($courses->isEmpty())
        <div class="sp-section">
            <div class="sp-empty"><i class="fa-solid fa-circle-check"></i><div>You already have every course, or your requests are waiting for approval. Check your profile for their status.</div></div>
        </div>
        @else
        @php $chosen = (int) old('course_id', $selected); @endphp
        <form action="{{route('student.courses.buy')}}" method="post" novalidate>
            @csrf
            <div class="sp-section">
                <div class="sp-section-head"><h2><i class="fa-solid fa-book-open"></i> 1. Choose a course</h2><span class="sp-count">{{$courses->count()}} available</span></div>
                <div class="bc-grid" role="radiogroup" aria-label="Courses">
                    @foreach($courses as $course)
                    <label class="bc-card">
                        <input type="radio" name="course_id" value="{{$course->id}}" data-fee="{{$course->courseFee()}}" data-title="{{$course->course('title')}}" @checked($chosen === (int) $course->id) required>
                        <span class="bc-tick"><i class="fa-solid fa-check"></i></span>
                        <img src="{{$course->courseImage()}}" alt="" loading="lazy" onerror="this.onerror=null;this.src='{{asset('medies/noimage.jpg')}}'">
                        <span class="bc-body">
                            <h3>{{$course->course('title')}}</h3>
                            <span class="bc-meta">
                                <span><i class="fa-solid fa-layer-group"></i> {{$course->courseCategory()['label'] ?? ''}}</span>
                                @if($course->course('duration'))<span><i class="fa-regular fa-clock"></i> {{$course->course('duration')}}</span>@endif
                            </span>
                            <span class="bc-fee">{{$course->courseFee()}}</span>
                        </span>
                    </label>
                    @endforeach
                </div>
            </div>

            <div class="sp-section">
                <div class="sp-section-head"><h2><i class="fa-solid fa-paper-plane"></i> 2. Send the request</h2></div>
                <div class="bc-sum" id="bcSum" @if(!$chosen) hidden @endif><span id="bcSumTitle">Selected course</span><b id="bcSumFee"></b></div>
                <div class="bc-pay"><i class="fa-solid fa-circle-info"></i> Your request goes to Rainbow Automation. Once it is approved, the course is added to your profile under <b>My Courses</b>.</div>
                <div class="sp-field {{$errors->has('note') ? 'has-err' : ''}}">
                    <label for="note">Note (optional)</label>
                    <textarea id="note" name="note" rows="3" maxlength="500" placeholder="Preferred batch time or anything else you want to tell us">{{old('note')}}</textarea>
                    @error('note')<div class="err">{{$message}}</div>@enderror
                </div>
                <button type="submit" class="sp-btn sp-btn-primary"><i class="fa-solid fa-paper-plane"></i> Send Course Request</button>
            </div>
        </form>
        @endif
    </div>
</section>
@endsection
@push('js')
<script>
// Summary of the chosen course above the payment fields
(function () {
    var sum = document.getElementById('bcSum');
    if (!sum) return;
    function show() {
        var picked = document.querySelector('input[name=course_id]:checked');
        sum.hidden = !picked;
        if (!picked) return;
        document.getElementById('bcSumTitle').textContent = picked.dataset.title;
        document.getElementById('bcSumFee').textContent = picked.dataset.fee;
    }
    document.querySelectorAll('input[name=course_id]').forEach(function (r) { r.addEventListener('change', show); });
    show();
})();
</script>
@endpush
