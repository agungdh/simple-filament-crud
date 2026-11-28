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
        Schema::create('materi_ppms', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_surat')->nullable();
            $table->string('nama_pemateri')->nullable();
            $table->text('materi_pengembangan');
            $table->date('tanggal_pelaksanaan');
            $table->string('link_materi')->nullable();
            $table->string('link_dokumentasi')->nullable();
            $table->timestamps();

            $table->index('nomor_surat');
            $table->index('tanggal_pelaksanaan');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('materi_ppms');
    }
};
