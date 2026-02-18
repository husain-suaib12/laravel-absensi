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
        schema::create('master_jabatan', function (Blueprint $table){
            $table->id('id_jabatan');
            $table->string('nama_jabatan');
            $table->timestamps();
        });

         schema::create('master_pendidikan', function (Blueprint $table){
             $table->id('id_pendidikan');
             $table->string('tingkat', 50);
             $table->timestamps();
        });

         schema::create('master_status_pegawai', function (Blueprint $table){
             $table->id('id_status');
             $table->string('nama_status', 50);
             $table->timestamps();
        });

         schema::create('master_departemen', function (Blueprint $table){
             $table->id('id_departemen');
             $table->string('nama_departemen', 100);
             $table->timestamps();
        });
         schema::create('master_hari_libur', function (Blueprint $table){
             $table->id('id_libur');
             $table->date('tanggal');
             $table->string('keterangan', 150)->nullable();
             $table->timestamps();
        });
         schema::create('master_setting_absensi', function (Blueprint $table){
             $table->id('id_setting');
             $table->time('jam_masuk')->default('08:00:00');
             $table->time('jam_pulang')->default('16:00:00');
             $table->integer('toleransi_telat')->default(20);
             $table->integer('toleransi_pulang_cepat')->default(20);
             $table->timestamps();
        });

        Schema::create('pegawai', function (Blueprint $table) {
                $table->id('id_pegawai');

                $table->string('nik', 20)->unique();
                $table->string('nama', 100);

                // relasi ke master table
                $table->unsignedBigInteger('id_jabatan')->nullable();
                $table->unsignedBigInteger('id_pendidikan')->nullable();
                $table->unsignedBigInteger('id_status')->nullable();
                $table->unsignedBigInteger('id_departemen')->nullable();

                $table->string('no_hp', 20)->nullable();
                $table->text('alamat')->nullable();
                $table->string('foto')->nullable();

                $table->boolean('status_aktif')->default(1);

                $table->foreign('id_jabatan')->references('id_jabatan')->on('master_jabatan')->nullOnDelete();
                $table->foreign('id_pendidikan')->references('id_pendidikan')->on('master_pendidikan')->nullOnDelete();
                $table->foreign('id_status')->references('id_status')->on('master_status_pegawai')->nullOnDelete();
                $table->foreign('id_departemen')->references('id_departemen')->on('master_departemen')->nullOnDelete();

                $table->timestamps();
            });

            schema::create('lokasi_kantor', function (Blueprint $table){
             $table->id('id_lokasi');
             $table->string('nama_lokasi', 100);
             $table->decimal('latitude',10, 7);
             $table->decimal('longitude',10, 7);
             $table->integer('radius_master')->default(50);
             $table->timestamps();
        });
        Schema::create('absensi', function (Blueprint $table) {
        $table->id('id_absensi');
        $table->unsignedBigInteger('id_pegawai');

        $table->date('tanggal');
        $table->time('jam_masuk')->nullable();
        $table->time('jam_pulang')->nullable();

        $table->enum('status_masuk', ['Tepat Waktu','Terlambat','Tanpa Keterangan'])
              ->default('Tanpa Keterangan');
        $table->enum('status_pulang', ['Tepat Waktu','Pulang Cepat','Tanpa Keterangan'])
              ->default('Tanpa Keterangan');

        $table->decimal('lat_masuk', 10, 7)->nullable();
        $table->decimal('long_masuk', 10, 7)->nullable();
        $table->decimal('lat_pulang', 10, 7)->nullable();
        $table->decimal('long_pulang', 10, 7)->nullable();

        $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');

        $table->timestamps();
    });

        schema::create('jenis_potongan', function (Blueprint $table){
         $table->id('id_jenis');
         $table->string('nama_potongan', 100);
         $table->enum('tipe',['tetap', 'presentase']);
         $table->decimal('nilai', 10,2 )->nullable();
         $table->timestamps();
        });

        Schema::create('potongan_gaji', function (Blueprint $table) {
        $table->id('id_potongan');
        $table->unsignedBigInteger('id_pegawai');
        $table->unsignedBigInteger('id_jenis');
        $table->string('bulan', 7); // YYYY-MM
        $table->decimal('jumlah', 10, 2)->default(0);

        $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');
        $table->foreign('id_jenis')->references('id_jenis')->on('jenis_potongan')->onDelete('cascade');

        $table->timestamps();
    });
        Schema::create('rekap_gaji_bulanan', function (Blueprint $table) {
        $table->id('id_rekap');
        $table->unsignedBigInteger('id_pegawai');
        $table->string('bulan', 7);

        $table->integer('jumlah_hadir')->default(0);
        $table->integer('jumlah_terlambat')->default(0);
        $table->integer('jumlah_tanpa_keterangan')->default(0);

        $table->decimal('total_potongan', 10, 2)->default(0);
        $table->decimal('gaji_bersih', 12, 2)->default(0);

        $table->foreign('id_pegawai')->references('id_pegawai')->on('pegawai')->onDelete('cascade');


        $table->timestamps();
    });



    }


    public function down(): void
    {
         Schema::dropIfExists('master_jabatan');
         Schema::dropIfExists('master_pendidikan');
         Schema::dropIfExists('master_status_pegawai');
         Schema::dropIfExists('master_departemen');
         Schema::dropIfExists('master_hari_libur');
         Schema::dropIfExists('master_setting_absensi');
         Schema::dropIfExists('pegawai');
         Schema::dropIfExists('lokasi_kantor');
         Schema::dropIfExists('absensi');
         Schema::dropIfExists('jenis_potongan');
         Schema::dropIfExists('potongan_gaji');
         Schema::dropIfExists('rekap_gaji_bulanan');

    }
};