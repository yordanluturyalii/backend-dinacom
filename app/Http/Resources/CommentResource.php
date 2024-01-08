<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'user' => $this->when($this->name_visibility ==  1,new UserResource($this->user)),
            'id' => $this->id,
            'contentComment' => $this->content,
            'visibility' => $this->name_visibility,
            'parentId' => $this->parent_id,
            'publishedAt' => $this->created_at
        ];
    }
}
