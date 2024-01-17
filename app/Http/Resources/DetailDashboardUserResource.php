<?php

namespace App\Http\Resources;

use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class DetailDashboardUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => new UserResource($this->user),
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => PostImageResource::collection($this->whenLoaded('postImages')),
            'status' => $this->status,
            'visibility' => $this->post_visibility,
            'nameVisibility' => $this->name_visibility,
            'publishedAt' => Carbon::createFromFormat("d-m-Y H:i:s", Carbon::parse($this->created_at), 'Asia/Jakarta'),
            'totalComment' => count($this->PostComments),
            'totalNeedResponse' => count($this->PostLikes),
            'comment' => CommentResource::collection(PostComment::query()->where('post_id', $this->id)->latest()->get()),
            'url' => url()->current()
        ];
    }
}
