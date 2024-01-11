<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use App\Mail\ResetPassword;
use App\Models\User;
use Aws\Exception\AwsException;
use Aws\Ses\SesClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Laravolt\Avatar\Avatar;
use PHLAK\StrGen\CharSet;
use PHLAK\StrGen\Generator;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class ApiAuthController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'register', 'resetPasswordLink', 'resetPassword']]);
    }

    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(['halo' => 'jojo']);
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

    /**
     * Summary of login
     * @param \Illuminate\Http\Request $request
     */
    public function login(Request $request)
    {
        try {
            $credentials = $request->only(['username', 'password']);

            $rules = [
                'username' => ['required', 'min:6'],
                'password' => ['required', 'min:8']
            ];

            $message = [
                'required' => 'Mohon maaf, input tidak boleh kosong. Silakan isi nilai yang diperlukan.',
                'username.min' => 'Mohon masukkan :attribute setidaknya 6 karakter untuk.',
                'password.min' => 'Mohon masukkan :attribute setidaknya 8 karakter.'
            ];

            $validator = Validator::make($credentials, $rules, $message);
            $token = JWTAuth::attempt($credentials);

            if (Auth::user()->status == 0) {
                $json = [
                    'status' => 403,
                    'message' => 'Maaf, anda sudah di blokir'
                ];

                return  response()->json($json, 403);
            }

            if (!$token) {
                $json = [
                    'status' => 401,
                    'message' => 'Gagal, Silahkan coba lagi'
                ];

                return response()->json($json, 401);
            }

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = Auth::user();
            JWTAuth::factory()->setTTL(43200);
            $json = [
                'status' => 200,
                'message' => 'Login Berhasil',
                'data' => new UserResource($user),
                'authorization' => [
                    'token' => $token,
                    'type' => 'bearer',
                    'expires_in' => JWTAuth::factory()->getTTL()
                ]
            ];

            return response()->json($json);
        } catch (ValidationException $e) {
            $errors = $e->validator->errors();

            $json = [
                'status' => 422,
                'message' => 'Validasi Error',
                'errors' => $errors
            ];

            return response()->json($json, 422);
        } catch (JWTException $e) {
            $json = [
                'status' => 500,
                'message' => 'Tidak bisa membuat token'
            ];
            return response()->json($json);
        } catch (\Exception $e) {
            $json = [
                'status' => 500,
                'message' => $e->getMessage()
            ];

            return response()->json($json);
        }
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
            'password_konfirmasi' => ['required', 'min:8', 'same:password']
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

            $rand_color = '#' . dechex(mt_rand(0, 16777215));
            $avatar = new Avatar();
            $avatar->create($request->username)->setBackground($rand_color)->save(public_path('/images/avatar/avatar-'.$request->username.'.png'), 100);
            DB::beginTransaction();
            $user = User::create([
                'nama_lengkap' => $request->nama_lengkap,
                'tanggal_lahir' => $request->tanggal_lahir,
                'tempat_tinggal' => $request->tempat_tinggal,
                'username' => $request->username,
                'email' => $request->email,
                'password' => bcrypt($request->password),
                'password_konfirmasi' => $request->password_konfirmasi,
                'avatar' => url('/images/avatar/avatar-'.$request->username.'.png')
            ]);

            DB::commit();

            $json = [
                'status' => 201,
                'message' => 'Registrasi Berhasil',
                'data' => new UserResource($user),
            ];
            return response()->json($json, 201);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->all();

            $json = [
                'status' => 422,
                'message' => 'Validasi Error',
                'errors' => $errors
            ];

            return response()->json($json, 422);
        } catch (\Exception $e) {
            $json = [
                'status' => 500,
                'message' => $e->getMessage()
            ];
            return response()->json($json, 400);
        } 
    }

    public function logout()
    {
        auth()->logout();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil untuk Keluar'
        ]);
    }

    public function resetPasswordLink(Request $request)
    {
        try {
            $validate = User::query()->where('email', '=', $request->email)->first();
            if (!$validate) {
                return response()->json([
                    'message' => 'Email tidak ditemukan'
                ]);
            }

            $str = new Generator();
            $token = $str->charset(CharSet::LOWER_ALPHA)->length(40)->generate();

            DB::table('password_resets')->insert([
                'email' => $request->email,
                'token' => $token,
                'created_at' => Carbon::now()
            ]);

            Mail::to($request->email)->send(new ResetPassword($token, $request->email));

            $json = [
                'status' => 200,
                'message' => "Link ganti kata sandi telah kami kirimkan ke $request->email",
                'token' => $token
            ];

            return response()->json($json, 200);
        } catch (\Exception $e) {
            $json = [
                'status' => 500,
                'message' => $e->getMessage()
            ];
            return response()->json($json, 500);
        }
    }

    public function resetPassword($token, Request $request)
    {
        // return $token;
        try {
            $rules = [
                'password' => ['required', 'min:8'],
                'password_konfirmasi' => ['required', 'min:8', 'same:password']
            ];

            $message = [
                'required' => 'Mohon maaf, input tidak boleh kosong. Silakan isi nilai yang diperlukan.',
                'min' => 'Mohon masukkan password setidaknya 8 karakter.',
                'password_konfirmasi.same' => 'Maaf, password tidak sesuai.'
            ];

            $validate = Validator::make($request->all(), $rules, $message);

            if ($validate->fails()) {
                throw new ValidationException($validate);
            }

            $tokenIsValid = DB::table('password_resets')->where('token', $token)->first();
            if ($tokenIsValid) {
                $email = $tokenIsValid->email;

                DB::beginTransaction();
                User::where('email', $email)
                    ->update([
                        'password' => bcrypt($request->password),
                        'password_konfirmasi' => $request->password_konfirmasi
                    ]);
                DB::commit();

                $json = [
                    'status' => 201,
                    'message' => 'Kata Sandi Berhasil Diubah',
                ];

                return response()->json($json, 201);
            }

            return response()->json([
                'status' => 498,
                'message' => 'Maaf, token tidak valid'
            ]);
        } catch (ValidationException $e) {
            DB::rollBack();
            $errors = $e->validator->errors()->all();

            $json = [
                'status' => 422,
                'message' => 'Validasi Error',
                'errors' => $errors
            ];

            return response()->json($json, 422);
        } catch (\Exception $e) {
            $json = [
                'status' => 500,
                'message' => $e->getMessage()
            ];
            return response()->json($json, 500);
        }
    }
}
