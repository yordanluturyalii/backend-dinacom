<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardUserResource;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardUserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function detailDashboardUser(): JsonResponse
    {
        $user = Auth::user();
        $report = Post::query()->where('user_id', $user->id)->get();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil Data Laporan',
            'data' => DashboardUserResource::collection($report),
            'totalStatusNotYetHandled' => count(Post::query()->where('status', 0)->get()),
            'totalStatusHandled' => count(Post::query()->where('status', 1)->where('user_id', $user->id)->get()),
            'totalStatusFinished' => count(Post::query()->where('status', 2)->where('user_id', $user->id)->get()),
            'totalStatusCanceled' => count(Post::query()->where('status', 3)->where('user_id', $user->id)->get()),
        ]);
    }
}
