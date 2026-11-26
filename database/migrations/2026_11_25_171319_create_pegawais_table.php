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
        Schema::create('pegawais', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained('users')->restrictOnDelete()->restrictOnUpdate();
            $table->string('nip')->unique();
            $table->string('nama');
            $table->foreignId('bidang_id')->constrained('bidangs')->restrictOnDelete()->restrictOnUpdate();
            $table->foreignId('pangkat_golongan_id')->nullable()->constrained('pangkat_golongans')->restrictOnDelete()->restrictOnUpdate();
            $table->enum('status', ['aktif', 'non aktif'])->default('aktif');
            $table->string('jabatan')->nullable();
            $table->enum('kategori_jabatan', [
                'struktural',
                'fungsional auditor',
                'fungsional tertentu',
            ])->nullable();
            $table->string('peran')->nullable();
            $table->enum('kategori_kebutuhan_jam_pelatihan', [
                'admin',
                'pejabat',
                'auditor',
            ])->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('kategori_jabatan');
            $table->index('kategori_kebutuhan_jam_pelatihan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pegawais');
    }
};
