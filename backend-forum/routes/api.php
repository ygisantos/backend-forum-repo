<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/changePassword', [AuthController::class, 'changePassword']);
    Route::get('/getUser', [AuthController::class, 'getUser']);
    Route::get('/getAllUser', [AuthController::class, 'getAllUser']);
});

Route::group([
    'prefix' => 'v1',
    'as' => 'api.',
    'namespace' => 'Api\v1\Admin',
    'middleware' => ['auth:sanctum']
], function () {
    Route::apiResource('projects','ProjectsApiController');
});
