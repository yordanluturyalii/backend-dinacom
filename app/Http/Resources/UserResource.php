<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'avatar' => $this->avatar,
            'namaLengkap' => $this->nama_lengkap,
            'tanggalLahir' => $this->tanggal_lahir,
            'tempatTinggal' => $this->tempat_tinggal,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}
