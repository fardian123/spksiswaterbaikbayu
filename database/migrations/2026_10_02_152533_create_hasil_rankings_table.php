<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('hasil_rankings', function (Blueprint $table) {
            $table->id();

            $table->foreignId('periode_id')
                ->constrained('periodes')
                ->cascadeOnDelete();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            $table->foreignId('ahp_hasil_id')
                ->constrained('ahp_hasils')
                ->cascadeOnDelete();

            $table->decimal('nilai_akhir', 10, 6);

            $table->unsignedInteger('ranking_umum');
            $table->unsignedInteger('ranking_jurusan');

            $table->json('detail');

            $table->unique([
                'periode_id',
                'siswa_id'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hasil_rankings');
    }
};
