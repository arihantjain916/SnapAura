<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;
use Storage;
use DB;
use App\Transformers\PostTransformer;
use App\Transformers\PostDisplayTransform;
use App\Models\Tag;
use App\Transformers\PostStoreTransform;
use Str;

class PostController extends Controller
{
    public function display()
    {
        $post = Post::with(['users', 'comments.user'])->orderBy("created_at", 'desc')->get();
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

            $hashtags = $this->extractHashtags($request->caption);

            $this->syncHashtags($post, $hashtags);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Post created successfully',
                'data' => $post,
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

    private function extractHashtags($content)
    {
        preg_match_all('/#(\w+)/', $content, $matches);
        return $matches[1];
    }

    private function syncHashtags(Post $post, array $hashtags)
    {
        $hashtagIds = [];

        foreach ($hashtags as $tag) {
            $hashtag = Tag::firstOrCreate(['name' => '#' . $tag, 'user_id' => auth()->user()->id]);
            $hashtagIds[] = $hashtag->id;
        }

        $post->tags()->sync(
            collect($hashtagIds)->mapWithKeys(fn($id) => [$id => ['id' => Str::uuid()]])->toArray()
        );

    }

}
