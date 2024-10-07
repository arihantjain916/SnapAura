<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Storage;
use DB;
use App\Transformers\PostTransformer;
use App\Transformers\PostDisplayTransform;
use App\Models\Tag;
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
        $post = Post::with('users')->find($id);

        $res = fractal($post, new PostDisplayTransform())->toArray();

        if (!$post) {
            return response()->json([
                "status" => "error",
                "message" => "Post not found"
            ], 404);
        }

        return response()->json([
            "status" => "success",
            "data" => $res["data"]
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

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not created',
                ], 500);
            }

            if ($request->has('tags')) {
                $tagIds = collect($request->tags)->map(function ($tagName) {
                    if (substr($tagName, 0, 1) !== '#') {
                        $tagName = '#' . $tagName;
                    }
                    return Tag::firstOrCreate(['name' => $tagName])->id;
                });

                $post->tags()->sync($tagIds);
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post->load('tags'),
            ], 201);
        } 
        catch (\Exception $e) {
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
