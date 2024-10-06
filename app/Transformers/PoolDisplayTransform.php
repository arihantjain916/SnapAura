<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PoolDisplayTransform extends TransformerAbstract
{

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($data)
    {
        $res = [];
        foreach ($data as $item) {
            $res[] = [
                "id" => $item->id,
                "question" => $item->question,
                "options" => json_decode($item->options),
                "created_at" => $item->created_at,
                "user" => [
                    "id" => $item->users->id,
                    "username" => $item->users->username,
                ]
            ];
        }
        return [
            "data" => $res
        ];
    }
}
