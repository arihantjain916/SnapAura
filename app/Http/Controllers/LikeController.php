<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateLikeRequest;
use App\Models\PostLike;
use DB;
use Illuminate\Http\Request;

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

}
