<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->boolean('name_visibility')->comment('1 = display name/username, 0 = hide name/username');
            $table->boolean('post_visibility')->comment('1 = other user and institution can view the post, 0 = only institution can view the post');
            $table->integer('status')->default(0)->comment('0 = laporan belum ditangani, 1 = laporan sedang ditangani, 2 = laporan sudah ditangani, 3 = laporan ditolak');
            $table->tinyText('status_message')->nullable();
            $table->foreignId('user_id')->constrained();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
