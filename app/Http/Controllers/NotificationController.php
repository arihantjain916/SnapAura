<?php

namespace App\Http\Controllers;

use App\Events\NotificationEvent;
use App\Models\User;
use Illuminate\Http\Request;
use Notification;

class NotificationController extends Controller
{
    public function fetch()
    {
        $notifications = auth()->user()->notifications()->get();
        return response()->json([
            "status" => true,
            "data" => $notifications ?? []
        ], 200);
    }


    public function temp()
    {
        $notification = Notification::first();
        $user = User::first();
        $second = User::where("username", "arihant_getgrahak")->first();
        event(new NotificationEvent($notification, $user, $second));
    }
}
