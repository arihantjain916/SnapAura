<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class PollDisplayTransform extends TransformerAbstract
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
                "votes" => $this->voteTransform($item->votes),
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

    protected function voteTransform($vote)
    {
        $data = [];

        foreach ($vote as $item) {
            $data[] = [
                "id" => $item->id,
                "user_id" => $item->user_id,
                "option" => $item->option,
                "created_at" => $item->created_at,
                "voted_by" => $item->user,
            ];
        }

        return $data;
    }
}
