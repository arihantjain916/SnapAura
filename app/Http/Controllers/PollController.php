<?php

namespace App\Http\Controllers;

use App\Http\Requests\PollRequest;
use App\Models\Pool;
use App\Models\PoolVote;
use DB;
use App\Transformers\PoolDisplayTransform;

class PollController extends Controller
{

    public function display()
    {
        $post = Pool::with(["users:id,username", "votes.user:id,username"])->get();
       

        // return response()->json([
        //     "success" => true,
        //     "data" => $post
        //     // "data" => $post["data"][0]["data"]
        // ], 200);
        $post = fractal([$post], new PoolDisplayTransform())->toArray();

        return response()->json([
            "success" => true,
            // "data" => $post
            "data" => $post["data"][0]["data"]
        ], 200);
    }

    public function store(PollRequest $request)
    {
        try {
            $data = [
                "question" => $request->question,
                "options" => json_encode($request->options),
            ];

            DB::beginTransaction();

            $pool = Pool::create($data);
            DB::commit();

            if (!$pool) {
                return response()->json([
                    "error" => "Poll not created",
                ], 500);
            }

            return response()->json([
                "success" => true,
                "pool" => $pool
            ], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "success" => false,
                "error" => $e->getMessage()
            ], 500);
        }
    }

    public function storeUserVote($id, $option)
    {
        try {
            $isPoolExist = Pool::where("id", $id)->with('votes')->first();
            if (!$isPoolExist) {
                return response()->json([
                    "error" => "Poll not found",
                    "success" => false
                ], 404);
            }

            $options = json_decode($isPoolExist->options, true);
            if (!in_array($option, $options)) {
                return response()->json(['error' => 'Invalid option'], 400);
            }

            $data = [
                "pool_id" => $id,
                "option" => $option
            ];

            DB::beginTransaction();

            PoolVote::create($data);

            DB::commit();

            $totalVotes = $isPoolExist->votes->count();

            $optionVotes = $isPoolExist->votes->groupBy('option')
                ->map(fn($votes) => $votes->count());

            $results = $optionVotes->map(function ($count) use ($totalVotes) {
                return ($totalVotes > 0) ? round(($count / $totalVotes) * 100, 2) . " %" : 0;
            });

            return response()->json([
                'success' => true,
                "message" => "Vote submitted successfully",
                'results' => json_decode($results),
                'totalVotes' => $totalVotes
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                "error" => $e->getMessage(),
                "success" => false
            ], 500);
        }
    }

}
