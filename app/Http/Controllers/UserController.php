<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
    }

    public function userProfile(): \Illuminate\Http\JsonResponse
    {
        $user = Auth::user();

        $json = [
            'status' => 200,
            'message' => 'Berhasil Mengambil Data User',
            'data' => new UserResource($user)
        ];

        return response()->json($json, 200);
    }
}
