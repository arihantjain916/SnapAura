<?php

namespace App\Http\Controllers;

use App\Models\Follow;
use App\Models\User;
use Illuminate\Http\Request;

class FollowRequestController extends Controller
{
    public function send($id)
    {
        $user_id = auth()->user()->id;
        $follower_id = $id;

        $isIdExist = User::find($id);

        if (!$isIdExist) {
            return response()->json([
                "status" => "error",
                "message" => "User not found"
            ], 404);
        }

        if ($user_id == $follower_id) {
            return response()->json([
                "status" => "error",
                "message" => "You can't follow yourself"
            ], 404);
        }
        $existingRequest = Follow::where('follower_id', $user_id)
            ->where('followed_id', $follower_id)
            ->first();

        if ($existingRequest) {
            return response()->json(['status' => false, 'message' => 'Follow request already sent'], 400);
        }

        Follow::create([
            'follower_id' => $user_id,
            'followed_id' => $follower_id,
            'status' => 'pending',
        ]);

        return response()->json(['status' => true, 'message' => 'Follow request sent successfully'], 200);
    }

    public function accept($id)
    {
        $user_id = auth()->user()->id;

        $follow = Follow::where('id', $id)
            ->where('followed_id', $user_id)
            ->first();

        if (!$follow) {
            return response()->json([
                "status" => "error",
                "message" => "Follow request not found"
            ], 404);
        }

        $follow->update([
            'status' => 'accepted'
        ]);

        return response()->json(['status' => true, 'message' => 'Follow request accepted successfully'], 200);
    }
}
