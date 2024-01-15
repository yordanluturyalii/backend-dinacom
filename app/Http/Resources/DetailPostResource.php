<?php

namespace App\Http\Resources;

use App\Models\PostComment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DetailPostResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => $this->when($this->name_visibility != 0, new UserResource($this->whenLoaded('user'))),
            'id' => $this->id,
            'title' => $this->title,
            'content' => $this->content,
            'image' => PostImageResource::collection($this->whenLoaded('postImages')),
            'status' => $this->status,
            'visibility' => $this->post_visibility,
            'nameVisibility' => $this->name_visibility,
            'publishedAt' => $this->created_at,
            'totalComment' => count($this->PostComments),
            'totalNeedResponse' => count($this->PostLikes),
            'comment' => CommentResource::collection(PostComment::query()->latest()->get()),
            'url' => url()->current()
        ];
    }
}
