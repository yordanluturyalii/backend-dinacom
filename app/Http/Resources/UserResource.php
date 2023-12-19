<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * Summary of UserResource
 * @author Yordan
 * @copyright (c) 2023
 */
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
            'namaLengkap' => $this->nama_lengkap,
            'tanggalLahir' => $this->tanggal_lahir,
            'tempatTinggal' => $this->tempat_tinggal,
            'username' => $this->username,
            'email' => $this->email,
            'password' => $this->password
        ];
    }
}
