{{-- Student registration fields (design: desing/Student_Portal.html), used by the register page and the home page section.
     $p = id prefix; $keep = show the values sent before (after a failed register) --}}
@php
    $old = fn ($name) => $keep ? old($name) : null;
    $bad = fn (...$names) => $keep && $errors->hasAny($names) ? 'bad' : '';
    $err = function (...$names) use ($keep, $errors) {
        if(!$keep) return '';
        return collect($names)->map(fn ($n) => $errors->first($n))->filter()->unique()->map(fn ($m) => '<span class="sp-err">'.e($m).'</span>')->implode('');
    };
    $choices = function ($name, array $options, $placeholder = null) use ($old) {
        $html = $placeholder !== null ? '<option value="">'.e($placeholder).'</option>' : '';
        foreach($options as $value => $label){
            $html .= '<option value="'.e($value).'"'.((string) $old($name) === (string) $value ? ' selected' : '').'>'.e($label).'</option>';
        }
        return $html;
    };
    $list = fn ($values) => array_combine($values, $values);
    $courses = trainingCourses()->mapWithKeys(fn ($c) => [$c->id => $c->course('title') ?: $c->name])->all();
@endphp
<div class="sp-fields">
<div class="sp-field"><label for="{{$p}}name">Full Name <b>*</b></label><input id="{{$p}}name" name="name" value="{{$old('name')}}" class="{{$bad('name')}}" required maxlength="150" autocomplete="name" placeholder="As per your official ID">{!! $err('name') !!}</div>
<div class="sp-field"><label for="{{$p}}phone">Mobile Number <b>*</b></label><input id="{{$p}}phone" name="phone" value="{{$old('phone')}}" class="{{$bad('phone', 'phone_key')}}" required type="tel" inputmode="tel" maxlength="20" autocomplete="tel" placeholder="01XXXXXXXXX">{!! $err('phone', 'phone_key') !!}</div>
<div class="sp-field"><label for="{{$p}}email">Email Address <b>*</b></label><input id="{{$p}}email" name="email" value="{{$old('email')}}" class="{{$bad('email')}}" type="email" required maxlength="150" autocomplete="email" placeholder="name@example.com">{!! $err('email') !!}</div>
<div class="sp-field"><label for="{{$p}}dob">Date of Birth</label><input id="{{$p}}dob" name="dob" value="{{$old('dob')}}" class="{{$bad('dob')}}" type="date" max="{{now()->subDay()->toDateString()}}">{!! $err('dob') !!}</div>
<div class="sp-field"><label for="{{$p}}profession">Profession <b>*</b></label><select id="{{$p}}profession" name="profession" class="{{$bad('profession')}}" required>{!! $choices('profession', $list(App\Models\StudentAccount::PROFESSIONS), 'Select profession') !!}</select>{!! $err('profession') !!}</div>
<div class="sp-field"><label for="{{$p}}organization">Institute / Company</label><input id="{{$p}}organization" name="organization" value="{{$old('organization')}}" class="{{$bad('organization')}}" maxlength="150" autocomplete="organization" placeholder="University, college or employer">{!! $err('organization') !!}</div>
<div class="sp-field"><label for="{{$p}}education">Highest Education</label><select id="{{$p}}education" name="education" class="{{$bad('education')}}">{!! $choices('education', $list(App\Models\StudentAccount::EDUCATIONS), 'Select qualification') !!}</select>{!! $err('education') !!}</div>
<div class="sp-field"><label for="{{$p}}course">Preferred Training Course @if($courses)<b>*</b>@endif</label><select id="{{$p}}course" name="preferred_course_id" class="{{$bad('preferred_course_id')}}" @if($courses) required @endif>{!! $choices('preferred_course_id', $courses, 'Select training course') !!}</select>{!! $err('preferred_course_id') !!}</div>
<div class="sp-field"><label for="{{$p}}mode">Training Preference</label><select id="{{$p}}mode" name="training_mode" class="{{$bad('training_mode')}}">{!! $choices('training_mode', $list(App\Models\StudentAccount::TRAINING_MODES)) !!}</select>{!! $err('training_mode') !!}</div>
<div class="sp-field"><label for="{{$p}}location">Preferred Training Location</label><select id="{{$p}}location" name="training_location" class="{{$bad('training_location')}}">{!! $choices('training_location', $list(App\Models\StudentAccount::TRAINING_LOCATIONS)) !!}</select>{!! $err('training_location') !!}</div>
<div class="sp-field full"><label for="{{$p}}address">Present Address</label><input id="{{$p}}address" name="address" value="{{$old('address')}}" class="{{$bad('address')}}" maxlength="255" autocomplete="street-address" placeholder="City / District / Full address">{!! $err('address') !!}</div>
<div class="sp-field"><label for="{{$p}}pass">Create Password <b>*</b></label><div class="sp-pass"><input id="{{$p}}pass" name="password" class="{{$bad('password')}}" type="password" required maxlength="100" autocomplete="new-password" placeholder="Minimum 8 characters, letters and numbers"><button class="sp-btn outline" type="button" data-sp-show="{{$p}}pass" style="padding:8px 12px">SHOW</button></div>{!! $err('password') !!}</div>
<div class="sp-field"><label for="{{$p}}pass2">Confirm Password <b>*</b></label><div class="sp-pass"><input id="{{$p}}pass2" name="password_confirmation" type="password" required maxlength="100" autocomplete="new-password" placeholder="Re-enter your password"><button class="sp-btn outline" type="button" data-sp-show="{{$p}}pass2" style="padding:8px 12px">SHOW</button></div></div>
</div>
<div class="sp-foot"><label class="sp-check"><input type="checkbox" name="agree" value="1" required {{$old('agree') ? 'checked' : ''}}> I agree to be contacted regarding my training registration, and the information I entered is correct.</label></div>
{!! $err('agree') !!}
