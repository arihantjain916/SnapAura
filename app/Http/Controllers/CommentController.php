<?php

namespace App\Http\Controllers;

use App\Transformers\CommentTransform;
use App\Models\Comment;

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
}
