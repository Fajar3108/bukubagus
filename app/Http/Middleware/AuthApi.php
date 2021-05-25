<?php

namespace App\Http\Middleware;

use App\Models\{User, LoginToken};
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthApi
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
        if (!$request->has('token')) return response()->json(['message' => 'unautorized'], 401);

        $token = $request->token;
        $checkToken = LoginToken::where('token', $token)->first();

        if (!$checkToken) return response()->json(['message' => 'Invalid Token']);

        Auth::login($checkToken->user);

        return $next($request);
    }
}
