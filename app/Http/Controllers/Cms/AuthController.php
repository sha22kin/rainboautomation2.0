<?php

namespace App\Http\Controllers\Cms;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin(){
        return view('cms.auth.login');
    }

    // Email, mobile number or user name + password; only active administrator users may sign in
    public function login(Request $r){
        $r->validate([
            'username' => 'required|max:100',
            'password' => 'required|max:50',
        ]);

        $field = is_numeric($r->username) ? 'mobile' : (filter_var($r->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'name');
        $user = User::where($field, $r->username)->first();

        if(!$user || !Hash::check($r->password, $user->password)){
            return back()->withInput($r->only('username'))->with('error', 'These credentials do not match our records.');
        }
        if(!$user->admin){
            return back()->withInput($r->only('username'))->with('error', 'This account has no access to the CMS.');
        }
        if($user->status != 1){
            return back()->withInput($r->only('username'))->with('error', 'This account is suspended. Please contact the administrator.');
        }

        Auth::login($user, $r->boolean('remember'));
        $r->session()->regenerate();

        return redirect()->intended(route('erp.dashboard'));
    }

    public function logout(Request $r){
        Auth::logout();
        $r->session()->invalidate();
        $r->session()->regenerateToken();
        return redirect()->route('login');
    }
}
