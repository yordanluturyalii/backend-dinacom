<?php

use App\Http\Controllers\ApiAuthController;
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

Route::post('/v1/login', [ApiAuthController::class, 'login'])->name('login');
Route::post('/v1/register', [ApiAuthController::class, 'register'])->name('register');

Route::group(['middleware' => 'api'], function () {
    Route::get('/v1/logout', [ApiAuthController::class, 'index']);
});
