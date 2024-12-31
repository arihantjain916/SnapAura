<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\NotificationMeta;
use App\Models\User;
use Illuminate\Http\Request;


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


    public function accept(Request $request, $id)
    {
        $notification_id = $request->id;

        $follow = Follow::where('id', $id)->first();


        if (!$follow) {
            return response()->json([
                "status" => "error",
                "message" => "Follow request not found"
            ], 404);
        }

        $follow->update([
            'status' => 'accepted'
        ]);

        $notification_meta = NotificationMeta::where("notification_id", $notification_id)->first();
        $notification_meta->update([
            "button_text" => "Following",
            "link" => route("follow.unfollow", $follow->id)
        ]);

        return response()->json(['status' => true, 'message' => 'Follow request accepted successfully'], 200);
    }

    public function reject(Request $request, $id)
    {
        $notification_id = $request->id;

        $follow = Follow::where('id', $id)
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

        $notification_meta = NotificationMeta::where("notification_id", $notification_id)->first();
        $notification_meta->update([
            "button_text" => "Follow",
            "link" => route("follow.accept", $follow->follower_id)
        ]);
        return response()->json(['status' => true, 'message' => 'Follow request rejected successfully'], 200);
    }

    public function unfollow(Request $request, $id)
    {
        try {
            $notification_id = $request->id;
            $follow = Follow::where('id', $id)
                ->first();

            if (!$follow) {
                return response()->json([
                    "status" => "error",
                    "message" => "Follow request not found"
                ], 404);
            }

            $follow->delete();

            $notification_meta = NotificationMeta::where("notification_id", $notification_id)->first();
            $notification_meta->update([
                "button_text" => null,
                "link" => null
            ]);

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
            "action_type" => "follow"
        ];

        $notificationSave = Notification::create($notificationData);

        NotificationMeta::create([
            "notification_id" => $notificationSave->id,
            "user_id" => $user->id,
            "link" => route("follow.accept", $follow_id),
        ]);
        $notification = Notification::with(["user", "meta.post", "meta.user"])->where("id", $notificationSave->id)->first();

        event(new NotificationEvent($notification));
    }
}
