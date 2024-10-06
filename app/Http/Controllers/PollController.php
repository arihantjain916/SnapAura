<?php

namespace App\Http\Controllers;

use App\Http\Requests\PollRequest;
use App\Models\Pool;
use DB;
use App\Transformers\PoolDisplayTransform;

class PollController extends Controller
{

    public function display()
    {
        $post = Pool::with(["users:id,username", "votes"])->get();

        $post = fractal([$post], new PoolDisplayTransform())->toArray();

        return response()->json([
            "success" => true,
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
}
