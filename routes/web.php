<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\{AuthController, BookController};

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/v1')->group(function() {
    Route::prefix('/auth')->group(function() {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
    });

    Route::middleware(['auth.api'])->group(function () {
        Route::prefix('/book')->group(function() {
            Route::post('/', [BookController::class, 'store']);
            Route::get('/', [BookController::class, 'index']);
            Route::get('/{id}', [BookController::class, 'show']);

            Route::post('/{id}/review', [BookController::class, 'review']);
            Route::post('/{id}/rating', [BookController::class, 'rating']);
        });

        Route::prefix('/auth')->group(function() {
            Route::post('/logout', [AuthController::class, 'logout']);
        });
    });
});
