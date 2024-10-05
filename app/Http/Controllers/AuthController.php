<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function store(Request $request)
    {
        $data = [
            "email" => $request->email,
            "password" => $request->password,
            "name" => $request->name
        ];
        $register = User::create($data);

        if ($register) {
            // $this->sendEmail($isCreate);
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $register
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'User not created',
        ]);
    }

    public function login(Request $request)
    {
        $user = User::where('email', $request->email)->first();
        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email not verified',
            ]);
        }

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        $user = Auth::user()->makeHidden('email_verified_at', "created_at", "updated_at", "remember_token");
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        $user = Auth::user();
        return response()->json([
            'status' => 'success',
            'user' => $user,
            'token' => $token,
        ]);
    }
}
