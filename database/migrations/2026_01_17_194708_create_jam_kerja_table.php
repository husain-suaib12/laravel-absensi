<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('jam_kerja', function (Blueprint $table) {
            $table->id('id_jam');
            $table->time('jam_masuk_mulai');
            $table->time('jam_masuk_selesai');
            $table->time('jam_pulang_mulai');
            $table->time('jam_pulang_selesai');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('jam_kerja');
    }
};
