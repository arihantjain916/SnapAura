<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use Storage;

class PostController extends Controller
{
    public function store(PostRequest $request)
    {
        $data = [
            "image" => $request->image,
            "title" => $request->title,
        ];
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
