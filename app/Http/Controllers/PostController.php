<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostStoreRequest;
use App\Http\Resources\DetailPostResource;
use App\Http\Resources\PostCollection;
use App\Http\Resources\PostResource;
use App\Models\Post;
use App\Models\PostImage;
use App\Models\PostLike;
use App\Models\PostReport;
use http\Env\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
    public function index(): PostCollection
    {
        $report = Post::query()->where('post_visibility', '!=', 0)->paginate(8);
        return new PostCollection($report);
    }

    public function indexLatest(): PostCollection
    {
        $report = Post::query()->where('post_visibility', '!=', 0)->latest()->paginate(8);
        return new PostCollection($report);
    }

    public function indexLiked(): PostCollection
    {
        $report = Post::query()->select(['posts.id', 'posts.title', 'posts.content', 'posts.user_id', 'posts.name_visibility', 'posts.post_visibility', 'posts.status', 'posts.created_at', DB::raw('COUNT(post_likes.post_id) as total_likes')])
            ->join('post_likes', 'posts.id', '=', 'post_likes.post_id')
            ->where('posts.name_visibility', '!=', 0)
            ->groupBy('posts.id')
            ->orderByDesc('total_likes')
            ->get();
        return new PostCollection($report);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): JsonResponse
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
                    $file->move(public_path('/images/post/'), $fileName);
                    $files[] = $fileName;
                }
            }

            $postImage = new PostImage();
            $postImage->post_id = $post->id;
            foreach ($files as $fileName) {
                $postImage = new PostImage();
                $postImage->post_id = $post->id;
                $postImage->path = '/images/post/' . $fileName;
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
        $report = Post::query()->where('post_visibility', '!=', 0)->findOrFail($id);
        return response()->json([
            'status' => 200,
            'message' => 'Berhasil mengambil data',
            'data' => new DetailPostResource($report->load(['user', 'postImages', 'PostComments']))
        ]);
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

    public function giveLike($postId): JsonResponse
    {
        $post = Post::query()->findOrFail($postId);
        $user = Auth::user();
//       dd($user);

        $likedPost = new PostLike();
        $likedPost->user_id = $user->id;
        $likedPost->post_id = $post->id;
        $likedPost->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil Memberikan Tanggapan Cepat'
        ]);
    }

    public function reportingReport($postId)
    {
        $post = Post::query()->findOrFail($postId);
        $user = Auth::user();

        $reportPost = new PostReport();
        $reportPost->user_id = $user->id;
        $reportPost->post_id = $post->id;
        $reportPost->save();

        return response()->json([
            'status' => 200,
            'message' => 'Berhasil Melapor                                                                                                                                                                          kan Laporan'
        ]);
    }
}
