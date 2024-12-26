<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\Facades\JWTAuth;

class SearchController extends Controller
{
    public function search($name)
    {
        $user = User::where('username', 'like', '%' . $name . '%')->get();
        return response()->json([
            "success" => true,
            "data" => $user
        ]);
    }

    public function searchProfile(Request $request, $name)
    {
        $isToken = $this->checkToken($request);
        $user = null;

        $query = User::with(["followers", "following", "posts.likes", "posts.images", 'posts.comments.user'])
            ->where("username", $name);


        if ($isToken) {
            $user = $query->first();
            $user->isFollowing = Follow::where("follower_id", $isToken->id)
                ->where("followed_id", $user->id)
                ->exists();
        } else {
            $user = $query->get();
        }

        if ($user) {
            return response()->json([
                "success" => true,
                "data" => $user
            ], 200);
        }

        return response()->json([
            "success" => false,
            "data" => "User not found"
        ], 404);
    }


    public function checkToken($request)
    {
        $bearer = $request->bearerToken();
        if ($bearer) {
            $user = JWTAuth::parseToken()->authenticate();
            return $user;
        }

        return false;
    }
}
