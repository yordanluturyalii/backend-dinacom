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
use Illuminate\Support\Facades\DB;

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

    public function filterLatest()
    {
        $user = Auth::user();
        $report = Post::query()->where('user_id', $user->id)->latest()->get();

        $json = [
            'status' => 200,
            'message' => 'Berhasil Memfilter Data',
            'data' => DashboardUserResource::collection($report)
        ];

        return response()->json($json, 200);
    }

    public function filterLongest()
    {
        $user = Auth::user();
        $report = Post::query()->where('user_id', $user->id)->oldest()->get();

        $json = [
            'status' => 200,
            'message' => 'Berhasil Memfilter Data',
            'data' => DashboardUserResource::collection($report)
        ];

        return response()->json($json, 200);
    }

    public function filterNotYetHandled()
    {
        $user = Auth::user();
        $report = Post::query()->where('user_id', $user->id)->where('status', 0)->get();

        $json = [
            'status' => 200,
            'message' => 'Berhasil Memfilter Data',
            'data' => DashboardUserResource::collection($report)
        ];

        return response()->json($json, 200);
    }

    public function filterHandled()
    {
        $user =  Auth::user();
        $report = Post::query()->where('user_id', $user->id)->where('status', 1)->get();

        $json = [
            'status' => 200,
            'message' => 'Berhasil Memfilter Data',
            'data' => DashboardUserResource::collection($report)
        ];

        return response()->json($json, 200);
    }

    public function filterFinish()
    {
        $user =  Auth::user();
        $report = Post::query()->where('user_id', $user->id)->where('status', 2)->get();

        $json = [
            'status' => 200,
            'message' => 'Berhasil Memfilter Data',
            'data' => DashboardUserResource::collection($report)
        ];

        return response()->json($json, 200);
    }

    public function changeStatusNotyethandled($postId)
    {
        try {
            $user = Auth::user();
            $report = Post::query()->where('user_id', $user->id)->findOrFail($postId);


            DB::beginTransaction();
            $report->status = 0;
            $report->save();
            DB::commit();

            $json = [
                'status' => 200,
                'message' => 'Berhasil mengubah status'
            ];

            return response()->json($json, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $json = [
                'status' => 404,
                'message' => 'Postingan tidak ditemukan',
                'error' => $e->getMessage()
            ];

            return response()->json($json, 404);
        }
    }

    public function changeStatusHandled($postId)
    {
        try {
            $user = Auth::user();
            $report = Post::query()->where('user_id', $user->id)->findOrFail($postId);


            DB::beginTransaction();
            $report->status = 1;
            $report->save();
            DB::commit();

            $json = [
                'status' => 200,
                'message' => 'Berhasil mengubah status'
            ];

            return response()->json($json, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $json = [
                'status' => 404,
                'message' => 'Postingan tidak ditemukan',
                'error' => $e->getMessage()
            ];

            return response()->json($json, 404);
        }
    }

    public function changeStatusFinish($postId)
    {
        try {
            $user = Auth::user();
            $report = Post::query()->where('user_id', $user->id)->findOrFail($postId);


            DB::beginTransaction();
            $report->status = 2;
            $report->save();
            DB::commit();

            $json = [
                'status' => 200,
                'message' => 'Berhasil mengubah status'
            ];

            return response()->json($json, 200);
        } catch (\Exception $e) {
            DB::rollBack();
            $json = [
                'status' => 404,
                'message' => 'Postingan tidak ditemukan',
                'error' => $e->getMessage()
            ];

            return response()->json($json, 404);
        }
    }

    public function deleteReport($postId)
    {
        try {
            $user = Auth::user();
            $report = Post::query()->where('user_id', $user->id)->findOrFail($postId);


            DB::beginTransaction();
            $report->delete();
            DB::commit();

            $json = [
                'status' => 204,
                'message' => 'Berhasil menghapus postingan'
            ];

            return response()->json($json, 204);
        } catch (\Exception $e) {
            DB::rollBack();
            $json = [
                'status' => 404,
                'message' => 'Postingan tidak ditemukan',
                'error' => $e->getMessage()
            ];

            return response()->json($json, 404);
        }
    }
}
