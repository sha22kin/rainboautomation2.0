<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\User;
class APITokenMiddleware
{

     
    public function handle($request, Closure $next)
    {
        

        if($request->header('authorization')){
            //$token = $request->header('authorization');
            $token = $request->bearerToken();
            $user = User::where('api_token','<>',null)->where('api_token',$token)->first();
            if($user) {
                return $next($request);
            }else{
                return response()->json(['message'=>'User not found'], 401);
            }
            
          }
          return response()->json(['message'=>'Not a valid API request.'], 401);
    }
    
    
}
