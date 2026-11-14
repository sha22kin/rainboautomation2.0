@extends(welcomeTheme().'layouts.app')
@section('title')
<title>{{websiteTitle('Edit Profile')}}</title>
@endsection
@section('SEO')
<meta name="robots" content="noindex, nofollow" />
@endsection
@push('css')
@include('welcome.student.partials.style')
@endpush
@section('contents')
{{-- Student profile edit: name, email, mobile, photo and password (StudentPortalController@updateProfile / updatePassword) --}}
@php
    // Both forms have a "current password" field: show its error under the form that was sent
    $passwordForm = old('_form') == 'password';
@endphp
<section class="sp-wrap">
    <div class="wrap">
        @if(session('success'))
        <div class="sp-alert ok" role="status"><i class="fa-solid fa-circle-check"></i> {{session('success')}}</div>
        @endif
        @if($errors->any())
        <div class="sp-alert err" role="alert"><i class="fa-solid fa-circle-exclamation"></i><div><strong>Not saved.</strong> Please fix the following:<ul>@foreach(array_unique($errors->all()) as $message)<li>{{$message}}</li>@endforeach</ul></div></div>
        @endif

        @include('welcome.student.partials.hero', ['actions' => 'edit'])

        <div class="sp-form-grid" style="margin-top:22px;">
            <form class="sp-section" action="{{route('student.profile.edit')}}" method="post" enctype="multipart/form-data" novalidate>
                @csrf
                <input type="hidden" name="_form" value="profile">
                <div class="sp-section-head"><h2><i class="fa-solid fa-user-pen"></i> Profile Information</h2></div>

                <div class="sp-photo-edit">
                    <div class="sp-photo" id="photoPreview">
                        @if($account->photoUrl())
                        <img src="{{$account->photoUrl()}}" alt="{{$account->name}}">
                        @else
                        <span aria-hidden="true">{{$account->initials()}}</span>
                        @endif
                    </div>
                    <div>
                        <label for="photo" style="display:block;font-weight:700;font-size:13px;color:#273750;margin-bottom:6px;">Profile photo</label>
                        <input type="file" id="photo" name="photo" accept="image/jpeg,image/png,image/webp">
                        <div class="hint" style="font-size:12px;color:#6b7c92;margin-top:6px;">JPG, PNG or WEBP, up to 2 MB. It is cropped to a square.</div>
                        @if($account->photo)
                        <button type="submit" form="removePhotoForm" class="sp-btn sp-btn-danger" style="margin-top:10px;padding:7px 12px;"><i class="fa-regular fa-trash-can"></i> Remove photo</button>
                        @endif
                    </div>
                </div>

                <div class="sp-field {{$errors->has('name') ? 'has-err' : ''}}">
                    <label for="name">Full name <span class="req">*</span></label>
                    <input type="text" id="name" name="name" value="{{old('name', $account->name)}}" maxlength="150" autocomplete="name" required>
                    @error('name')<div class="err">{{$message}}</div>@enderror
                </div>
                <div class="sp-row2">
                    <div class="sp-field {{$errors->has('email') ? 'has-err' : ''}}">
                        <label for="email">Email <span class="req">*</span></label>
                        <input type="email" id="email" name="email" value="{{old('email', $account->email)}}" maxlength="150" autocomplete="email" required data-original="{{$account->email}}">
                        @error('email')<div class="err">{{$message}}</div>@enderror
                    </div>
                    <div class="sp-field {{$errors->hasAny(['phone','phone_key']) ? 'has-err' : ''}}">
                        <label for="phone">Mobile number <span class="req">*</span></label>
                        <input type="tel" id="phone" name="phone" value="{{old('phone', $account->phone)}}" maxlength="20" placeholder="01X-XXXXXXXX" autocomplete="tel" required>
                        @error('phone')<div class="err">{{$message}}</div>@enderror
                        @error('phone_key')<div class="err">{{$message}}</div>@enderror
                    </div>
                </div>
                @php
                    $pick = function ($name, $options, $placeholder) use ($account) {
                        $value = (string) old($name, $account->$name);
                        return '<option value="">'.e($placeholder).'</option>'.collect($options)->map(fn ($label, $key) => '<option value="'.e($key).'"'.($value === (string) $key ? ' selected' : '').'>'.e($label).'</option>')->implode('');
                    };
                    $list = fn ($values) => array_combine($values, $values);
                    $detail = fn ($name, $label, $control) => '<div class="sp-field '.($errors->has($name) ? 'has-err' : '').'"><label for="'.$name.'">'.e($label).'</label>'.$control.($errors->has($name) ? '<div class="err">'.e($errors->first($name)).'</div>' : '').'</div>';
                @endphp
                <div class="sp-row2">
                    {!! $detail('dob', 'Date of birth', '<input type="date" id="dob" name="dob" value="'.e(old('dob', $account->dob?->toDateString())).'" max="'.now()->subDay()->toDateString().'">') !!}
                    {!! $detail('profession', 'Profession', '<select id="profession" name="profession">'.$pick('profession', $list(App\Models\StudentAccount::PROFESSIONS), 'Select profession').'</select>') !!}
                </div>
                <div class="sp-row2">
                    {!! $detail('organization', 'Institute / Company', '<input type="text" id="organization" name="organization" value="'.e(old('organization', $account->organization)).'" maxlength="150" placeholder="University, college or employer">') !!}
                    {!! $detail('education', 'Highest education', '<select id="education" name="education">'.$pick('education', $list(App\Models\StudentAccount::EDUCATIONS), 'Select qualification').'</select>') !!}
                </div>
                <div class="sp-row2">
                    {!! $detail('preferred_course_id', 'Preferred training course', '<select id="preferred_course_id" name="preferred_course_id">'.$pick('preferred_course_id', trainingCourses()->mapWithKeys(fn ($c) => [$c->id => $c->course('title') ?: $c->name])->all(), 'Select training course').'</select>') !!}
                    {!! $detail('training_mode', 'Training preference', '<select id="training_mode" name="training_mode">'.$pick('training_mode', $list(App\Models\StudentAccount::TRAINING_MODES), 'Select preference').'</select>') !!}
                </div>
                <div class="sp-row2">
                    {!! $detail('training_location', 'Preferred training location', '<select id="training_location" name="training_location">'.$pick('training_location', $list(App\Models\StudentAccount::TRAINING_LOCATIONS), 'Select location').'</select>') !!}
                    {!! $detail('address', 'Present address', '<input type="text" id="address" name="address" value="'.e(old('address', $account->address)).'" maxlength="255" placeholder="City / District / Full address">') !!}
                </div>
                <div class="sp-field {{$errors->has('current_password') && !$passwordForm ? 'has-err' : ''}}" id="emailPassword" style="{{old('email', $account->email) == $account->email ? 'display:none' : ''}}">
                    <label for="profile_current_password">Current password <span class="req">*</span></label>
                    <input type="password" id="profile_current_password" name="current_password" maxlength="100" autocomplete="current-password">
                    <div class="hint">Needed to change your email.</div>
                    @if(!$passwordForm)@error('current_password')<div class="err">{{$message}}</div>@enderror @endif
                </div>
                <button type="submit" class="sp-btn sp-btn-primary"><i class="fa-solid fa-check"></i> Save Changes</button>
            </form>

            <form class="sp-section" action="{{route('student.password')}}" method="post" novalidate>
                @csrf
                <input type="hidden" name="_form" value="password">
                <div class="sp-section-head"><h2><i class="fa-solid fa-lock"></i> Change Password</h2></div>
                <div class="sp-field {{$errors->has('current_password') && $passwordForm ? 'has-err' : ''}}">
                    <label for="current_password">Current password <span class="req">*</span></label>
                    <input type="password" id="current_password" name="current_password" maxlength="100" autocomplete="current-password" required>
                    @if($passwordForm)@error('current_password')<div class="err">{{$message}}</div>@enderror @endif
                </div>
                <div class="sp-field {{$errors->has('password') ? 'has-err' : ''}}">
                    <label for="password">New password <span class="req">*</span></label>
                    <input type="password" id="password" name="password" maxlength="100" autocomplete="new-password" required>
                    <div class="hint">At least 8 characters, with letters and numbers.</div>
                    @error('password')<div class="err">{{$message}}</div>@enderror
                </div>
                <div class="sp-field">
                    <label for="password_confirmation">Confirm new password <span class="req">*</span></label>
                    <input type="password" id="password_confirmation" name="password_confirmation" maxlength="100" autocomplete="new-password" required>
                </div>
                <button type="submit" class="sp-btn sp-btn-primary"><i class="fa-solid fa-key"></i> Change Password</button>
            </form>
        </div>
        <form id="removePhotoForm" action="{{route('student.photo.remove')}}" method="post" onsubmit="return confirm('Remove your profile photo?')">@csrf</form>
    </div>
</section>
@endsection
@push('js')
<script>
    (function () {
        // Photo preview before upload
        var input = document.getElementById('photo');
        var preview = document.getElementById('photoPreview');
        input.addEventListener('change', function () {
            var file = input.files && input.files[0];
            if (!file || !/^image\//.test(file.type)) return;
            var img = document.createElement('img');
            img.src = URL.createObjectURL(file);
            preview.innerHTML = '';
            preview.appendChild(img);
        });
        // A new email needs the current password
        var email = document.getElementById('email');
        var box = document.getElementById('emailPassword');
        email.addEventListener('input', function () {
            box.style.display = email.value.trim().toLowerCase() === email.getAttribute('data-original') ? 'none' : '';
        });
    })();
</script>
@endpush
