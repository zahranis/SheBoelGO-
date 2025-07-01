<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MainController;

Route::get('/category/{category}', [MainController::class, 'getByCategory']);

Route::get('/ping', function () {
    return response()->json(['message' => 'pong']);
});
