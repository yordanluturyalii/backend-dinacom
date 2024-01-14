<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardUserResource;
use App\Http\Resources\DetailDashboardUserResource;
use App\Http\Resources\DetailPostUserResource;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use function PHPSTORM_META\map;

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
            'totalStatusNotYetHandled' => count(Post::query()->where('status', 0)->where('user_id', $user->id)->get()),
            'totalStatusHandled' => count(Post::query()->where('status', 1)->where('user_id', $user->id)->get()),
            'totalStatusFinished' => count(Post::query()->where('status', 2)->where('user_id', $user->id)->get()),
            'totalStatusCanceled' => count(Post::query()->where('status', 3)->where('user_id', $user->id)->get()),
        ]);
    }

    public function detailReportDashboardUser($postId)
    {
        try {
            $user = Auth::user();
            $report =  Post::query()->where('user_id', $user->id)->findOrFail($postId);

            return response()->json([
                'status' => 200,
                'message' => 'Berhasil mengambil data',
                'data' => new DetailDashboardUserResource($report->load(['postImages', 'PostComments']))
            ]);
            
        } catch (\Exception $e) {
            $json = [
                'status' => 404,
                'message' => 'Postingan tidak ditemukan',
                'error' => $e->getMessage()
            ];

            return response()->json($json, 404);
        }
    }

    public function getStatusByNewest()
    {
        $user = Auth::user();
        $report = Post::query()->where('user_id', $user->id)->latest();

        $json = [
            'status' => 200,
            'message' => 'Berhasil Memfilter Data',
            'data' => new DashboardUserResource($report)
        ];

        return response()->json($json, 200);
    }
}
