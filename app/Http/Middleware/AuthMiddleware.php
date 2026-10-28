<?php

namespace App\Http\Middleware;

use Auth;
use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        
        if(Auth::check() && url('log-out')!==$request->url()){

                return redirect()->route('admin.dashboard');

               //return redirect()->back();
          
        }
        return $next($request);
    }
}
