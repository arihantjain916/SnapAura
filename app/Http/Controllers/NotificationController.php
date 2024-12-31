<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Models\Notification;
use App\Models\User;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    public function fetch()
    {
        $notifications = Notification::with(["user", "meta.post","meta.user"])->where("user_id", auth()->user()->id)->get();
        return response()->json([
            "status" => true,
            "data" => $notifications ?? []
        ], 200);
    }
}
