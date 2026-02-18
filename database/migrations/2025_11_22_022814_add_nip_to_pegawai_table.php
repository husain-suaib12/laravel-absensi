<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
{
    Schema::table('pegawai', function (Blueprint $table) {
        $table->string('nip', 30)->unique()->after('id_pegawai');
    });
}

public function down()
{
    Schema::table('pegawai', function (Blueprint $table) {
        $table->dropColumn('nip');
    });
}

};
