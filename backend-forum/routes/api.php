<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ForumController;

Route::controller(AuthController::class)->group(function () {
    Route::post('/register', 'register');
    Route::post('/login', 'login');
    Route::post('/logout', 'logout');
});

Route::middleware('auth:sanctum')->group(function () {
    Route::controller(UserController::class)->group(function () {
        Route::post('/changePassword', 'changePassword');
        Route::post('/changeEmail', 'changeEmail');
        Route::post('/changeName', 'changeName');

        Route::get('/getUser', 'getUser');
        Route::get('/getAllUser', 'getAllUser');

        Route::post('/uploadProfilePicture', 'uploadProfilePicture');
        Route::get('/getLikedData', 'getLikedData');
    });

    Route::prefix('forum')->controller(ForumController::class)->group(function () {
        Route::post('/addLike', 'addLike');
        Route::post('/removeLike', 'removeLike');
    });
});

Route::prefix('forum')->controller(ForumController::class)->group(function () {
    Route::get('/getAllForum', 'getAllForum');
    Route::post('/newForum', 'newForum');
    Route::delete('/deleteForum', 'deleteForum');
});
