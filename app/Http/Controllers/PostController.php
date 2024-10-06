<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Storage;
use DB;

class PostController extends Controller
{
    public function store(PostRequest $request)
    {
        try {
            $data = [
                "image" => $this->uploadImage($request->file('image')),
                "title" => $request->title,
            ];

            DB::beginTransaction();

            $post = Post::create($data);

            DB::commit();

            if (!$post) {
                return response()->json([
                    'success' => false,
                    'message' => 'Post not created',
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
            ]);
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
