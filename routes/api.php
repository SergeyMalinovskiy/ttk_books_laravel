<?php

use App\Http\Controllers\AuthController;
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

Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {

    Route::post('login',    [AuthController::class, 'login']);
    Route::post('logout',   [AuthController::class, 'logout']);
    Route::post('refresh',  [AuthController::class, 'refresh']);
    Route::post('me',       [AuthController::class, 'me']);

});

Route::group(['middleware' => 'jwt.auth'], function ($router) {
    Route::group(['prefix' => 'books'], function () {
        Route::get('/', [BookController::class, 'list']);
        Route::get('{id}', [BookController::class, 'detail']);

        Route::post('/', [BookController::class, 'create']);
        Route::post('{id}/edit', [BookController::class, 'edit']);
    });

    Route::group(['prefix' => 'authors', 'middleware' => 'checkUserRole:admin'], function () {
        Route::post('/', [AuthorController::class, 'create']);
        Route::post('{id}/edit', [AuthorController::class, 'edit']);
    });

    Route::group(['prefix' => 'categories', 'middleware' => 'checkUserRole:admin'], function () {
        Route::post('/', [CategoryController::class, 'create']);
        Route::post('{id}/edit', [CategoryController::class, 'edit']);
        Route::delete('{id}', [CategoryController::class, 'delete']);
    });
});

