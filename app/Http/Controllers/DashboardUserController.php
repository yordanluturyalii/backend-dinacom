<?php

namespace App\Http\Controllers;

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
        $report = Post::all();
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil Data Laporan',
            'data' => PostResource::collection($report),
            'totalStatusNotYetHandled' => count(Post::query()->where('status', 0)->get()),
            'totalStatusHandled' => count(Post::query()->where('status', 1)->get()),
            'totalStatusFinished' => count(Post::query()->where('status', 2)->get()),
            'totalStatusCanceled' => count(Post::query()->where('status', 3)->get()),
        ]);
    }
}
