<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DashboardUserResource extends JsonResource
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
            'image' => PostImageResource::collection($this->postImages),
            'status' => $this->status,
            'visibility' => $this->post_visibility,
            'publishedAt' => $this->created_at,
            'totalComment' => count($this->PostComments),
            'totalNeedResponse' => count($this->PostLikes),
        ];
    }
}
