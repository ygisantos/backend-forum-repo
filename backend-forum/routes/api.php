<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForumController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/changePassword', [UserController::class, 'changePassword']);
    Route::post('/changeEmail', [UserController::class,'changeEmail']);
    Route::post('/changeName', [UserController::class,'changeName']);

    Route::get('/getUser', [UserController::class, 'getUser']);
    Route::get('/getAllUser', [UserController::class, 'getAllUser']);
});

Route::group(['prefix'=> 'forum'], function () {
    Route::get('/getAllForum', [ForumController::class,'getAllForum']);
    Route::post('/newForum', [ForumController::class,'newForum']);
    Route::delete('/deleteForum', [ForumController::class,'deleteForum']);
});
