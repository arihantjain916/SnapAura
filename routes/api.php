<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PollController;

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

Route::group(["prefix" => "post"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::post("/", [PostController::class, "store"]);
        // Route::pos
    });
    Route::get("/", [PostController::class, "display"]);
    Route::get("/{id}", [PostController::class, "specificPost"]);
});

Route::group(["prefix" => "comment"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::post("/", [CommentController::class, "store"]);
        Route::post("reply", [CommentController::class, "storeReply"]);

    });
    Route::get("/", [CommentController::class, "display"]);
});

Route::group(["prefix" => "pool"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::post("/", [PollController::class, "store"]);
        Route::post("/vote/{id}/{option}", [PollController::class, "storeUserVote"]);

    });
    Route::get("/", [PollController::class, "display"]);
});




