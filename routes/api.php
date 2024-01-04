<?php

use App\Http\Controllers\ApiAuthController;
use App\Http\Controllers\CommentController;
use App\Http\Controllers\PostController;
use App\Http\Resources\PostResource;
use App\Models\Post;
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

Route::post('/v1/auth/login', [ApiAuthController::class, 'login'])->name('login');
Route::post('/v1/auth/register', [ApiAuthController::class, 'register'])->name('register');
Route::post('/v1/auth/reset-password-link', [ApiAuthController::class, 'resetPasswordLink'])->name('reset.password.link');
Route::post('/v1/auth/reset-password/{token}', [ApiAuthController::class, 'resetPassword'])->name('reset.password');

Route::group(['middleware' => 'api'], function () {
    Route::post('/v1/auth/logout', [ApiAuthController::class, 'logout'])->name('logout');
    Route::post('/v1/post/report', [PostController::class, 'store'])->name('post.report');
    Route::get('/v1/reports',[PostController::class, 'index'])->name('reports');
    Route::get('/v1/reports/latest',[PostController::class, 'indexLatest'] )->name('latest.reports');
    Route::get('/v1/detail-report/{id}', [PostController::class, 'show'])->name('detail.report');
    Route::post('/v1/detail-report/{postId}/post/comment', [CommentController::class, 'store'])->name('post.comment');
    Route::post('/v1/detail-report/{postId}/comment/{commentId}', [CommentController::class, 'replyComment'])->name('reply.comment');
    Route::post('/v1/detail-report/{postId}/liked', [PostController::class,'giveLike'])->name('give.like');
});
