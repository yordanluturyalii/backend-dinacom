<?php

namespace App\Http\Controllers;

use App\Http\Resources\CommentResource;
use App\Models\Post;
use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
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
    public function store(Request $request, $postId)
    {
        try {
            $rules = [
                'content' => ['required'],
                'name_visibility' => ['required']
            ];
            $message = [
                'required' => 'Maaf, input :attribute tidak boleh kosong. Silakan isi kolom yang diperlukan sebelum melanjutkan.',
            ];
            $validator = Validator::make($request->all(), $rules, $message);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }
            $post = Post::query()->findOrFail($postId);
            $user = Auth::user();
            // dd($user);
            if ($post->query()->where('post_visibility', "!=", 0)) {
                DB::beginTransaction();
                $comment = new PostComment();
                $comment->content = $request->content;
                $comment->name_visibility = $request->name_visibility;
                $comment->user_id = $user->id;
                $comment->post_id = $post->id;
                $comment->save();
                DB::commit();

                return response()->json([
                    'status' => 200,
                    'message' => 'Berhasil memberikan komentar',
                    'data' => new CommentResource($comment->load(['user']))
                ]);
            } else {
                throw new \Exception();
            }

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
            $json = [
                'status' => 404,
                'message' => 'Postingan tidak ditemukan',
                'error' => $e->getMessage()
            ];

            return response()->json($json, 404);
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

    public function replyComment(Request $request, $postId, $commentId)
    {
        try {
            $rules = [
                'content' => ['required'],
                'name_visibility' => ['required']
            ];
            $message = [
                'required' => 'Maaf, input :attribute tidak boleh kosong. Silakan isi kolom yang diperlukan sebelum melanjutkan.',
            ];
            $validator = Validator::make($request->all(), $rules, $message);

            if ($validator->fails()) {
                throw new ValidationException($validator);
            }

            $user = Auth::user();
            DB::beginTransaction();
            $comment = new PostComment();
            $comment->content = $request->content;
            $comment->name_visibility = $request->name_visibility;
            $comment->user_id = $user->id;
            $comment->post_id = $postId;
            $comment->parent_id = $commentId;
            $comment->save();
            DB::commit();

            return response()->json([
                'status' => 201,
                'message' => 'Berhasil membuat comment',
                'data' => new CommentResource($comment->load(['user']))
            ]);
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
            $json = [
                'status' => 400,
                'message' => $e->getMessage()
            ];
            return response()->json($json, 400);
        }
    }
}
