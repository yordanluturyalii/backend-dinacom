<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            'nama_lengkap' => 'Hayo Siapaaa??',
            'avatar' => 'images/avatar/avatar-dummy.jpg',
            'tanggal_lahir' => '2009-12-31',
            'tempat_tinggal' => 'Jl. Hmm..... Lupa wwkkwkw',
            'username' => 'hayo.siapa_123',
            'email' => 'user@gmail.com',
            'password' => bcrypt('User1234'),
            'password_konfirmasi' => bcrypt('User1234')
        ]);
    }
}
