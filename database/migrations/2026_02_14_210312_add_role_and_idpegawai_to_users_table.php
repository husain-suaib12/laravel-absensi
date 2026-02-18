<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->unsignedBigInteger('id_pegawai')->nullable()->after('id');
            $table->string('role')->default('pegawai')->after('password');

            $table->foreign('id_pegawai')
                ->references('id_pegawai') // <- sesuaikan dengan primary key pegawai
                ->on('pegawai')
                ->onDelete('cascade');
        });

    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropForeign(['id_pegawai']);
            $table->dropColumn(['id_pegawai', 'role']);
        });
    }
};
