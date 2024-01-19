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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap')->nullable(false);
            $table->string('avatar')->default('images/avatar/avatar-placeholder.jpg');
            $table->date('tanggal_lahir')->nullable(false);
            $table->text('tempat_tinggal')->nullable(false);
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('password')->nullable(false);
            $table->string('password_konfirmasi')->nullable(false);
            $table->boolean('status')->default(1)->comment('1 = akun aktif, 0 = akun nonaktif');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
