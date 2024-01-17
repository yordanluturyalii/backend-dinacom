<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

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
            'admin' => $this->when($this->admin_id != 0, new AdminResource($this->admin)),
            'post' => $this->post,
            'id' => $this->id,
            'contentComment' => $this->content,
            'visibility' => $this->name_visibility,
            'parentId' => $this->parent_id,
            'publishedAt' => Carbon::createFromFormat("d-m-Y H:i:s", $this->created_at, 'Asia/Jakarta')
        ];
    }
}
