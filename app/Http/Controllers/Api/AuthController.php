<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use Carbon\Carbon;

class AuthController extends Controller
{
    // User Register
    public function signup(Request $req)
    {
        $req->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = new User([
            'name' => $req->name,
            'email' => $req->email,
            'password' => \bcrypt($req->password),
        ]);

        $user->save();

        return response()->json([
            'message' => 'Successfully created user!'
        ], 201);
    }

    // User Login & Generate token
    public function login(Request $req)
    {
        // user form login validation
        $req->validate([
            'email' => 'required|string|email|max:255',
            'password' => 'required|string|min:6',
            'remember_me' => 'boolean',
        ]);

        $credential = [
            'email' => $req->email,
            'password' => $req->password,
        ];

        // login logic
        if(!auth()->attempt($credential)){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $user = $req->user();
        $generate_token = $user->createToken('Personal Access Token');
        $token = $generate_token->token;
        
        if($req->remember_me){
            $token->expires_at = Carbon::now()->addWeeks(1);
        }

        $token->save();

        return response()->json([
            'access_token' => $token->accessToken,
            'token_type' => 'Bearer',
            'expires_at' => Carbon::parse($token->expires_at)->toDateTimeString(),
        ]);

    }

    // User Logout
    public function logout(Request $req)
    {
        $req->user()->token()->revoke();

        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    // User Profile
    public function user(Request $req)
    {
        return response()->json($req->user());
    }
}
