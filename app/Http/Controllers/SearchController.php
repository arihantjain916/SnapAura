<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search($name)
    {
        $user = User::where('username', 'like', '%' . $name . '%')->get();
        return response()->json([
            "success" => true,
            "data" => $user
        ]);
    }
}
