<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::post('/register', [\App\Http\Controllers\Api\UserRegistrationController::class, 'register'])
    ->name('api.user-registration.register');

Route::get('/users/me', [\App\Http\Controllers\Api\UserController::class, 'me'])
    ->name('api.users.me');

Route::get('/todos/{todo}', [\App\Http\Controllers\Api\TodoController::class, 'view'])
    ->name('api.todo.view');

Route::patch('/todos/{todo}', [\App\Http\Controllers\Api\TodoController::class, 'update'])
    ->name('api.todo.update');
