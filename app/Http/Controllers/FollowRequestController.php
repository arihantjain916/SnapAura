<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\User;


class FollowRequestController extends Controller
{
    public function send($id)
    {
        $user = auth()->user();
        $follower_id = $id;

        $isIdExist = User::find($id);
        if (!$isIdExist) {
            return response()->json([
                "status" => "error",
                "message" => "User not found"
            ], 404);
        }

        if ($user->id == $follower_id) {
            return response()->json([
                "status" => "error",
                "message" => "You can't follow yourself"
            ], 400);
        }

        $follower_details = User::find($follower_id);

        $existingRequest = Follow::where('follower_id', $user->id)
            ->where('followed_id', $follower_id)
            ->first();

        if ($existingRequest) {
            if ($existingRequest->status === 'rejected') {
                $existingRequest->update(['status' => 'pending']);
                $this->sendNotification($user, $follower_details, $existingRequest->id);
                return response()->json([
                    'status' => true,
                    'message' => 'Follow request sent successfully'
                ], 200);
            }

            return response()->json([
                'status' => false,
                'message' => 'Follow request already sent, notification sent again'
            ], 200);
        }

        $follow = Follow::create([
            'follower_id' => $user->id,
            'followed_id' => $follower_id,
            'status' => 'pending',
        ]);

        $this->sendNotification($user, $follower_details, $follow->id);

        return response()->json([
            'status' => true,
            'message' => 'Follow request sent successfully'
        ], 200);
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

    public function reject($id)
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
            'status' => 'rejected'
        ]);

        return response()->json(['status' => true, 'message' => 'Follow request rejected successfully'], 200);
    }

    public function unfollow($id)
    {
        $user_id = auth()->user()->id;
        $follower_id = $id;
        try {
            $follow = Follow::where("follower_id", $user_id)
                ->where("followed_id", $follower_id)
                ->first();

            if (!$follow) {
                return response()->json([
                    "status" => "error",
                    "message" => "Follow request not found"
                ], 404);
            }

            $follow->delete();

            return response()->json(['status' => true, 'message' => 'User unfollowed successfully'], 200);
        } catch (\Exception $e) {
            return response()->json([
                "status" => "error",
                "message" => "Something went wrong"
            ], 500);
        }
    }

    private function sendNotification($user, $follower, $follow_id)
    {
        $notificationData = [
            "user_id" => $follower->id,
            "message" => "{$user->username} sent you a follow request",
            "type" => "success",
            "is_read" => 0,
            "link" => route("follow.accept", $follow_id),
            "action_type" => "follow"
        ];

        $notificationSave = Notification::create($notificationData);
        $notification = Notification::with("user")->where("id", $notificationSave->id)->first();

        event(new NotificationEvent($notification, $user));
    }
}
