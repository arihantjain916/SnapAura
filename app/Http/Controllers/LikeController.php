<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Http\Requests\CreateLikeRequest;
use App\Models\NotificationMeta;
use App\Models\Post;
use App\Models\PostLike;
use DB;
use Illuminate\Http\Request;
use App\Models\Notification;

class LikeController extends Controller
{
    public function like($post_id)
    {
        try {
            $data = [
                "user_id" => auth()->user()->id,
                "post_id" => $post_id
            ];

            $like = PostLike::where("user_id", $data["user_id"])->where("post_id", $data["post_id"])->first();
            $post = Post::find($data["post_id"]);

            if ($like) {
                DB::beginTransaction();
                $like->delete();

                DB::commit();
                return response()->json([
                    "message" => "Post UnLiked Successfully",
                    "success" => true
                ], 200);
            }

            DB::beginTransaction();
            $like = PostLike::create($data);

            $this->sendNotification(auth()->user(), $post);

            DB::commit();
            return response()->json([
                "message" => "Post Liked Successfully",
                "like" => $like,
                "success" => true
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "message" => $e->getMessage()
            ], 500);
        }
    }

    private function sendNotification($user, $post)
    {
        $notificationData = [
            "user_id" => $post->user_id,
            "message" => "{$user->username} Like your post",
            "type" => "success",
            "is_read" => 0,
            "action_type" => "like"
        ];

        $notificationSave = Notification::create($notificationData);
        NotificationMeta::create([
            "notification_id" => $notificationSave->id,
            "post_id" => $post->id,
            "user_id" => $user->id
        ]);

        $notification = Notification::with(["user","meta"])->where("id", $notificationSave->id)->first();
        event(new NotificationEvent($notification, $user, $post));
    }

}
