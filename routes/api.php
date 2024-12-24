<?php

use App\Http\Controllers\LikeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\TagController;
use App\Http\Controllers\PollController;

Route::get("/", function () {
    return response()->json([
        "status" => "success",
        "message" => "Welcome to SnapAura API",
        "date" => now()
    ]);
});


Route::get("verify/email/{userId}/{token}", [AuthController::class, "verifyEmail"]);
Route::get("resend-email/{email}", [AuthController::class, "resendEmail"]);

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

        Route::get("/post", [PostController::class, "fetchPostofUser"]);
    });
});

Route::group(["prefix" => "post"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::post("/", [PostController::class, "store"]);
        Route::post("/like/{post_id}", [LikeController::class, 'like']);
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

Route::group(["prefix" => "poll"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::post("/", [PollController::class, "store"]);
        Route::post("/vote/{id}/{option}", [PollController::class, "storeUserVote"]);

    });
    Route::get("/", [PollController::class, "display"]);
});


Route::group(["prefix" => "tag"], function () {
    Route::group(["middleware" => "auth:api"], function () {
        Route::get('{tagName}', [TagController::class, 'getPostsByTag']);
        Route::get('/', [TagController::class, 'getAllTags']);
    });
});

Route::group(["prefix" => "oauth"], function () {
    Route::get("/google", [AuthController::class, "handleGoogleLogin"]);
    Route::get("/google/callback", [AuthController::class, "handleGoogleCallback"]);
    Route::get("/github", [AuthController::class, "handleGitHubLogin"]);
    Route::get("/github/callback", [AuthController::class, "handleGitHubCallback"]);
});
