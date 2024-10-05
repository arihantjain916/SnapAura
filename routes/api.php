<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get("/", function () {
    return response()->json([
        "status" => "success",
        "message" => "Welcome to SnapAura API",
        "date" => now()
    ]);
});


Route::get("verify/email/{userId}/{token}", [AuthController::class, "verifyEmail"]);

Route::group(["prefix" => "auth"], function () {
    Route::post("register", [AuthController::class, "store"]);
    Route::post("login", [AuthController::class, "login"]);
});

Route::group(["prefix" => "user"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::get("profile", [AuthController::class, "profile"]);
        Route::put("update/profile", [AuthController::class, "updateProfile"]);
        Route::get("logout", [AuthController::class, "logout"]);
        Route::put("reset/password", [AuthController::class, "passwordReset"]);
    });
});



