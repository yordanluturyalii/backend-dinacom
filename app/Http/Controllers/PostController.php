<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class PostController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth:api');
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
        try {
            $rules = [
                'title' => ['required'],
                'content' => ['required'],
                'post_id' => ['exists:posts,id'],
                'files' => ['required', 'max:20480'],
                'name_visibility' => ['required'],
                'post_visibility' => ['required']
            ];
            $message = [
                'required' => 'Maaf, input :attribute tidak boleh kosong. Silakan isi kolom yang diperlukan sebelum melanjutkan.',
                'files.max' => 'Maaf, ukuran file melebihi batas maksimal yang diizinkan. Mohon unggah file dengan ukuran yang lebih kecil dari 20 Mb.'
            ];
            $validator = Validator::make($request->all(), $rules, $message);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = auth()->user();

            DB::beginTransaction();
            $post = new Post();
            $post->title = $request->title;
            $post->content = $request->content;
            $post->name_visibility = $request->name_visibility;
            $post->post_visibility = $request->post_visibility;
            $post->user_id = $user->id;
            $post->status = 0;
            $post->save();

            $files = [];
            if ($request->hasFile('files')) {
                foreach ($request->file('files') as $file) {
                    $fileName = time() . rand(1, 99) . '.' . $file->extension();
                    $file->move(public_path('/storage/post/image'), $fileName);
                    $files[] = $fileName;
                }
            }

            $postImage = new PostImage();
            $postImage->post_id = $post->id;
            foreach ($files as $fileName) {
                $postImage = new PostImage();
                $postImage->post_id = $post->id;
                $postImage->path = $fileName;
                $postImage->save();
            }
            DB::commit();

            $json = [
                'status' => 201,
                'message' => 'Berhasil Membuat Laporan',
                'data' => new PostResource($post->load(['user', 'postImages']))
            ];

            return response()->json($json, 201);
        } catch (ValidationException $e) {
            DB::rollback();

            $errors = $e->validator->errors()->all();
            $json = [
                'status' => 422,
                'message' => 'Validasi Error',
                'error' => $errors
            ];

            return response()->json($json, 422);
        } catch (\Exception $e) {
            DB::rollback();
            $json = [
                'message' => $e->getMessage()
            ];
            return response()->json($json);
        }
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
}