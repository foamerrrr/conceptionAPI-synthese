<?php

use App\Http\Controllers\Api\ItemController;
use App\Http\Controllers\Api\ShoppingListController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/ping', function () {
    return response()->json([
        'message' => 'pong',
    ]);
});

Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login'])->middleware('throttle:10,1');

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::post('/logout', [UserController::class, 'logout']);

    Route::get('/list', [ShoppingListController::class, 'show'])->name('list.show');

    Route::get('/items/{item}', [ItemController::class, 'show'])->name('items.show');
    Route::post('/items', [ItemController::class, 'store'])->name('items.store');
    Route::put('/items/{item}', [ItemController::class, 'update'])->name('items.update');
    Route::patch('/items/{item}', [ItemController::class, 'update']);
    Route::delete('/items/{item}', [ItemController::class, 'destroy'])->name('items.destroy');
});
