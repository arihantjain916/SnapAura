<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Storage;
use DB;
use App\Transformers\PostTransformer;

class PostController extends Controller
{
    public function display()
    {
        $post = Post::with('users')->get();
        $res = fractal([$post], new PostTransformer())->toArray();
        return response()->json([
            "status" => "success",
            "data" => $res['data'][0][0]
        ], 200);
    }

    public function specificPost($id)
    {
        $post = Post::find($id);
        // $res = fractal([$post], new PostTransformer())->toArray();

        if (!$post) {
            return response()->json([
                "status" => "error",
                "message" => "Post not found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data" => $post
        ], 200);
    }

    public function store(PostRequest $request)
    {
        try {
            $data = [
                "image" => $this->uploadImage($request->file('image')),
                "caption" => $request->caption,
            ];

            DB::beginTransaction();

            $post = Post::create($data);

            DB::commit();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not created',
                ], 500);
            }

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
            ], 500);
        }
    }

    protected function uploadImage($file)
    {
        $uploadFolder = 'posts';
        $image = $file;
        $image_uploaded_path = $image->store($uploadFolder, 'public');
        $uploadedImageUrl = Storage::disk('public')->url($image_uploaded_path);

        return $uploadedImageUrl;
    }
}
