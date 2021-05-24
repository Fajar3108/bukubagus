<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Models\{User, LoginToken};
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        // Validation
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|alpha|min:2|max:20',
            'last_name' => 'required|alpha|min:2|max:20',
            'username' => 'required|alpha_dash|min:5|max:12|unique:users',
            'password' => 'required|min:5|max:12',
        ]);

        // Check if validation is fails
        if ($validator->fails()) {
            // response if fails
            return response()->json([
                'message' => 'invalid field',   
                'errors' => $validator->errors(),
            ], 422);
        }

        // Create new user
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'username' => $request->username,
            'password' => Hash::make($request->password),
        ]);

        // Create login token
        $token = LoginToken::create([
            'user_id' =>  $user->id,
            'token' => Hash::make( $user->id),
        ]);

        // response if success
        return response()->json([
            'token' => $token->token,
        ], 200);
    }

    public function login(Request $request)
    {
        $username = $request->username;
        $password = $request->password;

        // if login success
        if(Auth::attempt(['username' => $username, 'password' => $password])) {
            $token = LoginToken::create([
                'user_id' => Auth::user()->id,
                'token' => Hash::make(Auth::user()->id)
            ]);

            return response()->json([
                'token' => $token->token
            ]);
        }

        // if login failed
        return response()->json([
            'message' => 'Invalid Login'
        ], 401);
    }

    public function logout(Request $request)
    {
        $token = LoginToken::where('token', $request->token)->first();

        if ($token) {
            $token->delete();

            return response()->json([
                'message' => 'logout success'
            ], 200);
        }

        return response()->json([
            'message' => 'unautorized user'
        ], 401);
    }
}
