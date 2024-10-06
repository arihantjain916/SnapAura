<?php

namespace App\Http\Controllers;

use App\Transformers\CommentTransform;
use App\Models\Comment;
use App\Http\Requests\CommentRequest;
use App\Http\Requests\CommentReplyRequest;

class CommentController extends Controller
{
    public function display()
    {
        $comments = Comment::with('replies', 'user')
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $nextPageUrl = $comments->nextPageUrl();
        $previousPageUrl = $comments->previousPageUrl();
        $count = $comments->count();

        if (count($comments) == 0) {
            return response()->json([
                'status' => 'error',
                'message' => 'No comments found'
            ]);
        }

        $comments = fractal([$comments], new CommentTransform())->toArray();

        return response()->json([
            'status' => 'success',
            'data' => $comments["data"][0][0],
            "meta" => [
                "next_page_url" => $nextPageUrl,
                "previous_page_url" => $previousPageUrl,
                "total_comment" => $count
            ]
        ], 200);
    }

    public function store(CommentRequest $request)
    {
        $comment = Comment::create([
            "post" => $request->post_id,
            'comment' => $request->comment,
            'user_id' => auth()->user()->id,
        ]);

        if (!$comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Comment created successfully',
        ], 200);
    }

    public function storeReply(CommentReplyRequest $request)
    {
        $comment = Comment::create([
            "comment" => $request->comment,
            "user_id" => auth()->user()->id,
            "parent_id" => $request->parent_id,
            "post" => $request->post_id
        ]);

        if (!$comment) {
            return response()->json([
                'status' => 'error',
                'message' => 'Something went wrong',
            ], 500);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Comment created successfully',
            "data" => $comment
        ], 200);
    }
}
