<?php

namespace App\Http\Controllers;

use App\Models\User;
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
}
