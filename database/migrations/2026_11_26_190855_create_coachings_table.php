<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('coachings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pegawai_id')->index()->constrained('pegawais')->restrictOnDelete()->restrictOnUpdate();
            $table->text('materi_pengembangan');
            $table->date('tanggal_pelaksanaan');
            $table->integer('jumlah_jam');
            $table->string('berkas')->nullable();
            $table->string('filename')->nullable();
            $table->timestampsTz();

            $table->index('tanggal_pelaksanaan');
        });

        DB::statement('alter table coachings add constraint coachings_jumlah_jam_check check (jumlah_jam > 0)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('coachings');
    }
};
