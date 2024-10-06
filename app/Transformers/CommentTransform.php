<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class CommentTransform extends TransformerAbstract
{

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform($data)
    {
        $returnData = [];

        foreach ($data as $key => $value) {
            $returnData[] = [
                "id" => $value->id,
                "comment" => $value->comment,
                "created_at" => $value->created_at,
                "totalreplies" => count($value->replies),
                "replies" => $this->transformReplies($value->replies),
                "user" => [
                    "id" => $value->user->id,
                    "name" => $value->user->name,
                    "email" => $value->user->email
                ],
            ];
        }

        return [
            $returnData,
        ];
    }
    private function transformReplies($replies)
    {
        $replyData = [];

        foreach ($replies as $reply) {
            $replyData[] = [
                "id" => $reply->id,
                "comment" => $reply->comment,
                "created_at" => $reply->created_at
            ];
        }

        return $replyData;
    }
}
