<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PostTransformer extends TransformerAbstract
{

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($data)
    {
        $constData = [];
        foreach ($data as $key => $value) {
            $constData[] = [
                "id" => $value->id,
                "image" => $this->images($value->images),
                "caption" => $value->caption,
                "created_at" => $value->created_at,
                "user" => [
                    "id" => $value->users->id,
                    "username" => $value->users->username,
                    "profile" => $value->users->profile,
                ],
                "comments" => $this->commentData($value->comments),
                "totalLikes" => $value->likes->count(),
                'isLiked' => (bool) $value->isLiked
            ];
        }
        return [
            $constData
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

    public function images($data){
        $image = [];
        foreach ($data as $value) {
            $image[] = $value->image;
        }
        return $image;
    }
}
