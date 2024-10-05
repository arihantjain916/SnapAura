<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post("register", [AuthController::class, "store"]);
Route::post("reset", [AuthController::class, "passwordReset"]);
Route::post("login", [AuthController::class, "login"]);
Route::get("verify/email/{userId}/{token}", [AuthController::class, "verifyEmail"]);


