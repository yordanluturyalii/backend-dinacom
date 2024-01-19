<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PostCommentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('post_comments')->insert([
            'content' => 'Kamu gak jelas postingannya kamuuuuuuu',
            'name_visibility' => 1,
            'user_id' => 1,
            'post_id' => 1,
        ]);

        DB::table('post_comments')->insert([
            'content' => 'Laporin aja mas diaa',
            'name_visibility' => 1,
            'user_id' => 1,
            'post_id' => 1,
            'parent_id' => 1
        ]);

        DB::table('post_comments')->insert([
            'content' => 'Postingan rak ceto ii, mau diban kah?',
            'name_visibility' => 1,
            'admin_id' => 1,
            'post_id' => 1,
        ]);
    }
}
