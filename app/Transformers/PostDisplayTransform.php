<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PostDisplayTransform extends TransformerAbstract
{

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($data)
    {
        return [
            "id" => $data->id,
            "image" => $this->images($data->images),
            "caption" => $data->caption,
            "created_at" => $data->created_at,
            "user" => [
                "id" => $data->users->id,
                "username" => $data->users->username,
                "profile" => $data->users->profile,
            ],
            "comments" => $this->commentData($data->comments),
            "totalLikes" => $data->likes->count(),
            'isLiked' => (bool) $data->isLiked
        ];
    }

    public function commentData($data)
    {
        $comment = [];
        foreach ($data as $value) {
            $comment[] = [
                "comment" => $value->comment,
                "user" => [
                    "id" => $value->user->id,
                    "username" => $value->user->username,
                    "profile" => $value->user->profile,
                ],
            ];
        }
        return $comment;
    }

    public function images($data)
    {
        $image = [];
        foreach ($data as $value) {
            $image[] = $value->image;
        }
        return $image;
    }
}
