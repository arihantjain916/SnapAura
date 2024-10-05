<?php

namespace App\Http\Controllers;

use App\Models\User;
use Auth;
use Http;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;
use Storage;
use Str;
use App\Http\Requests\UpdateProfileRequest;
use App\Http\Requests\RegisterRequest;

class AuthController extends Controller
{
    public function store(RegisterRequest $request)
    {
        $data = [
            "email" => $request->email,
            "password" => $request->password,
            "name" => $request->name
        ];
        $register = User::create($data);

        if ($register) {
            $this->sendEmail($register);
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
        if (!$token) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized',
            ], 401);
        }

        return response()->json([
            'status' => 'success',
            'token' => $token,
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
        $user = User::where('email', $request->email)->first();
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
        $user = User::find(auth()->user()->id);
        $data = $request->only([
            "name",
            "email",
        ]);
        if ($request->hasFile('profile')) {
            $data['profile'] = $this->uploadImage($request->file('profile'));
        }

        $isUpdate = $user->update(attributes: $data);
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
        $app_url = env("APP_URL");
        $token = Str::random(20);
        $url = "$app_url/api/verify/email/$user->id/$token";
        $data = [
            "email" => $user->email,
            "body" => "<a>$url</a>"
        ];
        $user->update([
            'remember_token' => $token
        ]);
        Http::post("https://connect.pabbly.com/workflow/sendwebhookdata/IjU3NjYwNTZkMDYzMjA0M2M1MjY4NTUzZDUxMzQi_pc", $data);
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
