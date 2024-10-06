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
                "image" => $value->image,
                "caption" => $value->caption,
                "created_at" => $value->created_at,
                "user" => [
                    "id" => $value->users->id,
                    "name" => $value->users->name,
                    "profile" => $value->users->profile,
                ]
            ];
        }
        return [
            $constData
        ];
    }
}
