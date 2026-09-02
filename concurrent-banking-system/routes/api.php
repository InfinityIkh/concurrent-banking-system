<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

//
Route::post('auth/login', [AuthController::class ,'login']);
Route::post('auth/register', [AuthController::class ,'register']);
//
Route::middleware('auth:api')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
    Route::middleware('admin')->group(function () {
        Route::apiResource('users' , \App\Http\Controllers\UserController::class);
    });
});
