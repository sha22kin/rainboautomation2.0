<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Erp\Certificate;
use App\Models\Erp\Student;
use Illuminate\Support\Facades\DB;
use App\Models\StudentAccount;
use App\Support\CertificateTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password as PasswordBroker;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;

// Website student accounts: register, login, profile with courses and certificates.
// Uses its own "student" guard and table, so a student session can never open the CMS.
class StudentPortalController extends Controller
{
    const LOGIN_ATTEMPTS = 5;
    const REGISTER_ATTEMPTS = 5;

    public function showRegister(){
        return view('welcome.student.register');
    }

    // Registration details of the design form. Registering: profession and course are required (course only
    // when the website has courses); editing the profile: all optional, so older accounts can still save.
    private function detailRules($registering){
        $courseIds = trainingCourses()->pluck('id')->all();
        $need = $registering ? 'required' : 'nullable';
        return [
            'dob' => 'nullable|date|after:1940-01-01|before:today',
            'profession' => [$need, Rule::in(StudentAccount::PROFESSIONS)],
            'organization' => 'nullable|string|max:150',
            'education' => ['nullable', Rule::in(StudentAccount::EDUCATIONS)],
            'preferred_course_id' => [$registering && $courseIds ? 'required' : 'nullable', Rule::in($courseIds)],
            'training_mode' => ['nullable', Rule::in(StudentAccount::TRAINING_MODES)],
            'training_location' => ['nullable', Rule::in(StudentAccount::TRAINING_LOCATIONS)],
            'address' => 'nullable|string|max:255',
        ];
    }

    private const DETAIL_MESSAGES = [
        'profession.required' => 'Please select your profession.',
        'preferred_course_id.required' => 'Please select a preferred training course.',
        'preferred_course_id.in' => 'Please select a training course from the list.',
        'dob.before' => 'Please enter a valid date of birth.',
        'dob.after' => 'Please enter a valid date of birth.',
    ];

    private function details(Request $r){
        $data = $r->only(['dob', 'profession', 'organization', 'education', 'preferred_course_id', 'training_mode', 'training_location', 'address']);
        return array_map(fn ($v) => is_string($v) && trim($v) === '' ? null : (is_string($v) ? trim($v) : $v), $data);
    }

    public function register(Request $r){
        $back = fn ($message) => back()->withInput($r->except('password', 'password_confirmation'))->with('error', $message);

        // Hidden field that people never see (named so browsers do not auto-fill it); bots that fill every field are turned away
        if(filled($r->input('ra_hp_field'))){
            return $back('Registration could not be completed. Please try again.');
        }

        // At most 5 new accounts per connection in an hour (failed tries are limited by the route: 20 a minute)
        $key = 'student-register:'.$r->ip();
        if(RateLimiter::tooManyAttempts($key, self::REGISTER_ATTEMPTS)){
            return $back('Too many accounts were registered from this connection. Please try again in '.ceil(RateLimiter::availableIn($key) / 60).' minute(s).');
        }

        $r->merge(['email' => Str::lower(trim((string) $r->email)), 'phone_key' => StudentAccount::phoneKey($r->phone)]);
        $r->validate([
            'name' => ['required', 'string', 'min:3', 'max:150', 'regex:/^[\pL\pM .\'-]+$/u'],
            'email' => 'required|email:rfc|max:150|unique:student_accounts,email',
            'phone' => ['required', 'string', 'max:20', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'phone_key' => 'unique:student_accounts,phone_key',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
            'agree' => 'accepted',
        ] + $this->detailRules(true), self::DETAIL_MESSAGES + [
            'name.regex' => 'Please write your name with letters only.',
            'phone.regex' => 'Please enter a valid Bangladeshi mobile number, e.g. 01X-XXXXXXXX.',
            'email.unique' => 'An account with this email already exists. Please log in instead.',
            'phone_key.unique' => 'An account with this mobile number already exists. Please log in instead.',
            'agree.accepted' => 'Please confirm that the information is correct.',
        ]);

        $account = DB::transaction(function () use ($r) {
            $account = StudentAccount::create([
                'name' => trim($r->name),
                'email' => $r->email,
                'phone' => trim($r->phone),
                'phone_key' => $r->phone_key,
                'password' => $r->password,
            ] + $this->details($r));
            return $account;
        });
        RateLimiter::hit($key, 3600);

        // Registration done: the account waits for the admin (ERP → Registered Students); the student can log in once approved
        return redirect()->route('student.login')
            ->withInput(['email' => $account->email])
            ->with('success', 'Registration successful! Please log in. Your account will be ready once Rainbow Automation approves your registration.');
    }

    public function showLogin(){
        return view('welcome.student.login');
    }

    public function login(Request $r){
        $r->validate([
            'email' => 'required|email|max:150',
            'password' => 'required|string|max:100',
        ]);

        // At most 5 wrong passwords per email and connection per minute
        $key = 'student-login:'.Str::lower($r->email).'|'.$r->ip();
        if(RateLimiter::tooManyAttempts($key, self::LOGIN_ATTEMPTS)){
            $seconds = RateLimiter::availableIn($key);
            return back()->withInput($r->only('email'))->with('error', 'Too many login attempts. Please try again in '.$seconds.' seconds.');
        }

        $account = StudentAccount::where('email', Str::lower($r->email))->first();
        if(!$account || !Hash::check($r->password, $account->password)){
            RateLimiter::hit($key, 60);
            return back()->withInput($r->only('email'))->with('error', 'The email or password is incorrect.');
        }
        if(!$account->status){
            return back()->withInput($r->only('email'))->with('error', 'This account is blocked. Please contact Rainbow Automation.');
        }
        RateLimiter::clear($key);
        Auth::guard('student')->login($account, $r->boolean('remember'));
        $r->session()->regenerate();
        $this->rememberLogin($account, $r);

        return redirect()->intended(route('student.profile'));
    }

    public function showForgot(){
        return view('welcome.student.forgot');
    }

    // Sends the reset link. The same message is shown whether or not the email has an account,
    // so the page cannot be used to find out who is registered.
    public function sendResetLink(Request $r){
        $r->validate(['email' => 'required|email|max:150']);
        $sent = 'If a student account uses this email, a password reset link has been sent to it. Please check your inbox (and spam folder).';

        try {
            $status = PasswordBroker::broker('students')->sendResetLink(['email' => Str::lower(trim($r->email))]);
        } catch (\Throwable $e) {
            report($e);
            return back()->withInput()->with('error', 'The email could not be sent right now. Please try again later or contact Rainbow Automation.');
        }
        if($status === PasswordBroker::RESET_THROTTLED){
            return back()->withInput()->with('error', 'A reset link was sent a moment ago. Please wait a minute before asking again.');
        }
        return back()->with('success', $sent);
    }

    public function showReset(Request $r, $token){
        return view('welcome.student.reset', ['token' => $token, 'email' => (string) $r->query('email', '')]);
    }

    public function resetPassword(Request $r){
        $r->validate([
            'token' => 'required|string|max:200',
            'email' => 'required|email|max:150',
            'password' => ['required', 'confirmed', Password::min(8)->letters()->numbers()],
        ]);

        $status = PasswordBroker::broker('students')->reset(
            ['email' => Str::lower(trim($r->email)), 'token' => $r->token, 'password' => $r->password, 'password_confirmation' => $r->password_confirmation],
            function (StudentAccount $account, $password) {
                // A new remember token: "Remember me" logins on other browsers stop working
                $account->password = $password;
                $account->setRememberToken(Str::random(60));
                $account->save();
            }
        );

        if($status !== PasswordBroker::PASSWORD_RESET){
            return back()->withInput($r->only('email'))->with('error', 'This reset link is invalid or has expired. Please ask for a new one.');
        }
        return redirect()->route('student.login')->withInput(['email' => Str::lower(trim($r->email))])
            ->with('success', 'Your password has been reset. Please log in with the new password.');
    }

    public function logout(Request $r){
        Auth::guard('student')->logout();
        $r->session()->regenerateToken();
        return redirect()->route('student.login')->with('success', 'You have been logged out.');
    }

    public function profile(Request $r){
        $account = $this->account($r);
        if(!$account){
            return redirect()->route('student.login')->with('error', 'This account is blocked. Please contact Rainbow Automation.');
        }
        // Not approved yet: the profile only says to wait for the admin
        if(!$account->isApproved()){
            return view('welcome.student.pending', compact('account'));
        }

        $enrollments = $account->enrollments()->whereNotNull('course_id')->with(['course', 'batch', 'payments'])->latest('admitted_on')->get();
        $certificates = $account->certificates()->with('course')->latest('issued')->get();

        return view('welcome.student.profile', compact('account', 'enrollments', 'certificates'));
    }

    public function editProfile(Request $r){
        $account = $this->account($r);
        if(!$account){
            return redirect()->route('student.login')->with('error', 'This account is blocked. Please contact Rainbow Automation.');
        }
        if(!$account->isApproved()){
            return redirect()->route('student.profile');
        }
        return view('welcome.student.edit', compact('account'));
    }

    // Name, email, mobile and photo. A new email needs the current password.
    public function updateProfile(Request $r){
        $account = $this->account($r);
        abort_unless($account, 403);
        // Profile changes open after the admin approves
        if(!$account->isApproved()){
            return redirect()->route('student.profile');
        }

        $r->merge(['email' => Str::lower(trim((string) $r->email)), 'phone_key' => StudentAccount::phoneKey($r->phone)]);
        $emailChanged = $r->email !== $account->email;
        $r->validate([
            'name' => ['required', 'string', 'min:3', 'max:150', 'regex:/^[\pL\pM .\'-]+$/u'],
            'email' => 'required|email:rfc|max:150|unique:student_accounts,email,'.$account->id,
            'phone' => ['required', 'string', 'max:20', 'regex:/^(?:\+?88)?01[3-9]\d{8}$/'],
            'phone_key' => 'unique:student_accounts,phone_key,'.$account->id,
            'photo' => 'nullable|image|mimes:jpeg,jpg,png,webp|max:2048|dimensions:min_width=100,min_height=100,max_width=6000,max_height=6000',
            'current_password' => $emailChanged ? 'required|current_password:student' : 'nullable',
        ] + $this->detailRules(false), self::DETAIL_MESSAGES + [
            'name.regex' => 'Please write your name with letters only.',
            'email.unique' => 'Another account already uses this email.',
            'phone.regex' => 'Please enter a valid Bangladeshi mobile number, e.g. 01X-XXXXXXXX.',
            'phone_key.unique' => 'Another account already uses this mobile number.',
            'photo.max' => 'The photo must be 2 MB or smaller.',
            'current_password.required' => 'Enter your current password to change the email.',
            'current_password.current_password' => 'The current password is incorrect.',
        ]);

        $account->fill(['name' => trim($r->name), 'email' => $r->email, 'phone' => trim($r->phone), 'phone_key' => $r->phone_key] + $this->details($r));
        if($r->hasFile('photo')){
            $account->photo = $this->storePhoto($r->file('photo'), $account->photo);
        }
        $account->save();

        return redirect()->route('student.profile.edit')->with('success', 'Your profile has been updated.');
    }

    public function removePhoto(Request $r){
        $account = $this->account($r);
        abort_unless($account, 403);
        // Profile changes open after the admin approves
        if(!$account->isApproved()){
            return redirect()->route('student.profile');
        }
        $this->deletePhoto($account->photo);
        $account->photo = null;
        $account->save();
        return redirect()->route('student.profile.edit')->with('success', 'Your photo has been removed.');
    }

    public function updatePassword(Request $r){
        $account = $this->account($r);
        abort_unless($account, 403);
        // Profile changes open after the admin approves
        if(!$account->isApproved()){
            return redirect()->route('student.profile');
        }

        $r->validate([
            'current_password' => 'required|current_password:student',
            'password' => ['required', 'confirmed', 'different:current_password', Password::min(8)->letters()->numbers()],
        ], [
            'current_password.current_password' => 'The current password is incorrect.',
            'password.different' => 'The new password must be different from the current one.',
        ], ['password' => 'new password']);

        // A new remember token: "Remember me" logins on other browsers stop working
        $account->password = $r->password;
        $account->setRememberToken(Str::random(60));
        $account->save();
        $r->session()->regenerate();

        return redirect()->route('student.profile.edit')->with('success', 'Your password has been changed.');
    }

    // The uploaded image is re-drawn as a 400 x 400 JPG with a random name (nothing hidden in the file is kept)
    private function storePhoto($file, $old){
        $dir = public_path('medies/students');
        if(!is_dir($dir)){
            mkdir($dir, 0755, true);
        }
        $name = 'medies/students/'.Str::random(32).'.jpg';
        \Image::make($file->getRealPath())->orientate()->fit(400, 400)->encode('jpg', 85)->save(public_path($name));
        $this->deletePhoto($old);
        return $name;
    }

    private function deletePhoto($path){
        if($path && str_starts_with($path, 'medies/students/') && is_file(public_path($path))){
            @unlink(public_path($path));
        }
    }

    // A student opens only certificates of their own linked course records
    public function certificate(Request $r, $id){
        $account = $this->account($r);
        $certificate = $account ? $account->certificates()->with('student.batch')->find($id) : null;
        abort_unless($certificate, 404);

        return view('welcome.student.certificate', [
            'certificate' => $certificate,
            'template' => CertificateTemplate::get(),
            'values' => CertificateTemplate::values($certificate),
        ]);
    }

    // The signed in account; a blocked account is signed out
    private function account(Request $r){
        $account = Auth::guard('student')->user();
        if($account && !$account->status){
            Auth::guard('student')->logout();
            $r->session()->regenerateToken();
            return null;
        }
        return $account;
    }

    private function rememberLogin(StudentAccount $account, Request $r){
        $account->forceFill(['last_login_at' => now(), 'last_login_ip' => $r->ip()])->save();
    }
}
