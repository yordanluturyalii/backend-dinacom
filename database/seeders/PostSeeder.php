<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('posts')->insert([
            'title' => 'Pembunuhan secara langsung',
            'content' => 'Saya ingin melaporkan adanya kasus pembunuhan. Pelaku merupakan seekor kucing yang dapat membunuh seseorang karena terlalu LUCUUU KYAAA >///<',
            'name_visibility' => 1,
            'post_visibility' => 1,
            'user_id' => 1
        ]);
    }
}
