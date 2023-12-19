<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }

    public function login()
    {
        # code...
    }

    /**
     * Summary of register
     * @param \Illuminate\Http\Request $request
     * @throws \Illuminate\Validation\ValidationException
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request): JsonResponse
    {
        $rules = [
            'nama_lengkap' => ['required'],
            'tanggal_lahir' => ['required', 'date'],
            'tempat_tinggal' => ['required'],
            'username' => ['required', 'min:6', 'unique:users,username'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'min:8'],
            'password_konfirmasi' => ['required','min:8','same:password']
        ];

        $message = [
            'required' => 'Mohon maaf, input tidak boleh kosong. Silakan isi nilai yang diperlukan.',
            'tanggal_lahir.date' => 'Mohon masukkan format tanggal yang valid.',
            'username.min' => 'Mohon masukkan :attribute setidaknya 6 karakter untuk.',
            'username.unique' => ':attribute telah digunakan. Mohon masukkan :attribute yang berbeda untuk melanjutkan.',
            'email.email' => 'Format email yang Anda masukkan tidak valid. Mohon masukkan alamat email yang benar.',
            'email.unique' => ':attribute telah digunakan. Mohon masukkan :attribute yang berbeda untuk melanjutkan.',
            'password.min' => 'Mohon masukkan password setidaknya 8 karakter.',
            'password_konfirmasi.min' => 'Mohon masukkan password setidaknya 8 karakter.',
            'password_konfirmasi.same' => 'Maaf, password tidak sesuai.'
        ];

        $validator = Validator::make($request->all(), $rules, $message);

        try {
            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            DB::beginTransaction();
            $user = User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir,
                'tempat_tinggal' => $request->tempat_tinggal,
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'password_konfirmasi' => $request->password_konfirmasi
            ]);

            DB::commit();

            $token = JWTAuth::fromUser($user);

            $json = [
                'status' => 201,
                'message' => 'Registrasi Berhasil',
                'response' => new UserResource($user),
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer'
                ]
            ];
            return response()->json($json, 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->all();

            $json = [
                'status' => 422,
                'message' => $errors
            ];

            return response()->json($json, 422);
        } catch (\Exception $e) {
            $json = [
                'status' => 400,
                'message' => $e->getMessage()
            ];
            return response()->json($json, 400);
        }
    }
}
