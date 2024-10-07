<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Tag;
use DB;
class Tagcontroller extends Controller
{
    public function getPostsByTag($tagName)
    {
        try {
            $decodedTagName = urldecode($tagName);
            DB::enableQueryLog();

            $tag = Tag::where('name', $decodedTagName)->first();
            \Log::info(DB::getQueryLog());
            if (!$tag) {
                return response()->json([
                    'success' => false,
                    'message' => 'Tag not found'
                ], 404);
            }

            $posts = $tag->posts()->get();
            \Log::info('Fetched posts:', $posts->toArray());
           

            return response()->json([
                'success' => true,
                'data' => $posts
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function getAllTags()
    {
        $tags = Tag::all();
        return response()->json($tags);
    }

}
