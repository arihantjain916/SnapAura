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
            "image" => $data->image,
            "caption" => $data->caption,
            "created_at" => $data->created_at,
            "user" => [
                "id" => $data->users->id,
                "name" => $data->users->name,
                "profile" => $data->users->profile,
            ]

        ];
    }
}
