<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\BookController;
use App\Http\Controllers\CategoryController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group(['prefix' => 'books'], function () {
    Route::get('/', [BookController::class, 'list']);
    Route::get('{id}', [BookController::class, 'detail']);

    Route::post('/', [BookController::class, 'create']);
    Route::post('{id}/edit', [BookController::class, 'edit']);
});

Route::group(['prefix' => 'authors'], function () {
    Route::post('/', [AuthorController::class, 'create']);
    Route::post('{id}/edit', [AuthorController::class, 'edit']);
});

Route::group(['prefix' => 'categories'], function () {
   Route::post('/', [CategoryController::class, 'create']);
   Route::post('{id}/edit', [CategoryController::class, 'edit']);
   Route::delete('{id}', [CategoryController::class, 'delete']);
});
