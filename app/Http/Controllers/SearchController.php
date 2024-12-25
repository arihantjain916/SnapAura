<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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

    public function searchProfile($name)
    {
        $user = User::with(["followers", "following", "posts.likes", "posts.images", 'posts.comments.user'])->where("username", $name)->first();

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
}
