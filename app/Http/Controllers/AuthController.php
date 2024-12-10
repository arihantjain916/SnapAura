<?php

namespace App\Http\Controllers;

use App\Mail\EmailVerification;
use App\Models\User;
use Auth;
use DB;
use Http;
use Illuminate\Http\Request;
use Mail;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Storage;
use Str;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use Validator;

class AuthController extends Controller
{
    public function store(RegisterRequest $request)
    {
        $data = [
            "username" => $request->username,
            "email" => $request->email,
            "password" => $request->password,
        ];
        $register = User::create($data);

        if (!$register) {
            return response()->json([
                'success' => false,
                'message' => 'User not created',
            ]);
        }


        $this->sendEmail($register);
        $credentials = $request->only('email', 'password');

        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error Generating token',
            ], 401);
        }

        return response()->json([
            'success' => true,
            'message' => 'User created successfully',
            'data' => $register,
            'token' => $token
        ]);
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        $credentials = $request->only('email', 'password');
        $token = Auth::attempt($credentials);
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Email or password invalid',
            ], 401);
        }

        if (!$user->email_verified_at) {
            return response()->json([
                'success' => false,
                'message' => 'Email not verified',
            ]);
        }

        return response()->json([
            'status' => 'success',
            'token' => $token,
            "data" => $user
        ]);
    }

    public function profile()
    {
        $user = User::find(Auth::user()->id);
        return response()->json([
            'status' => 'success',
            'user' => $user
        ]);
    }

    public function passwordReset(Request $request)
    {
        $validate = Validator::make($request->all(), [
            "password" => "required|string|min:6",
        ]);
        if ($validate->fails()) {
            return response()->json([
                "status" => "error",
                "message" => $validate->errors()
            ]);
        }
        $user = User::find(Auth::user()->id);
        if ($user) {
            $user->update([
                'password' => $request->password
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Password reset successfully',
                'data' => $user
            ]);
        }
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ]);
    }

    public function verifyEmail(string $userId, string $token)
    {
        $user = User::where('id', $userId)->first();
        if ($user->remember_token != $token) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid token',
            ], 500);
        }
        if ($user) {
            if ($user->email_verified_at) {
                return response()->json([
                    'success' => false,
                    'message' => 'Email already verified',
                ], 500);
            } else {
                $user->update([
                    'email_verified_at' => now(),
                    "remember_token" => null
                ]);
                return response()->json([
                    'success' => true,
                    'message' => 'Email verified successfully',
                ], 200);
            }
        }
        return response()->json([
            'success' => false,
            'message' => 'User not found',
        ], 500);
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        try {
            $user = User::find(auth()->user()->id);
            $data = $request->only([
                "username",
                "email",
                "name"
            ]);

            if ($request->hasFile('profile')) {
                $data['profile'] = $this->uploadImage($request->file('profile'));
            }
            DB::beginTransaction();
            $isUpdate = $user->update($data);
            DB::commit();

            if ($isUpdate) {
                return response()->json([
                    "status" => true,
                    "message" => "Profile updated successfully",
                    "data" => $user->fresh(),
                ], 200);
            }
            return response()->json([
                "status" => false,
                "message" => "Unable to update profile",
            ], 500);
        } catch (\Exception $e) {
            return response()->json([
                "status" => false,
                "message" => $e->getMessage(),
            ], 500);
        }
    }

    public function logout()
    {
        $token = JWTAuth::getToken();

        $invalidate = JWTAuth::invalidate($token);

        if ($invalidate) {
            return response()->json([
                'status' => 'success',
                'message' => 'Logout successfully',
            ], 200);
        }
    }

    protected function sendEmail(object $user)
    {
        // $app_url = "localhost:3000";
        $app_url = "https://snap-aura.vercel.app";
        $token = Str::random(20);
        $url = "$app_url/auth/account-verify/$token/$user->id";
        $data = [
            "email" => $user->email,
            "link" => $url,
            "username" => $user->username
        ];
        $user->update([
            'remember_token' => $token
        ]);
        Mail::to($user->email)->send(new EmailVerification($data));
    }

    protected function uploadImage($file)
    {
        $uploadFolder = 'profile-image';
        $image = $file;
        $image_uploaded_path = $image->store($uploadFolder, 'public');
        $uploadedImageUrl = Storage::disk('public')->url($image_uploaded_path);

        return $uploadedImageUrl;
    }
}
