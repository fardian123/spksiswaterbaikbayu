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
        Schema::create('nilais', function (Blueprint $table) {
            $table->id();

            $table->foreignId('siswa_id')
                ->constrained('siswas')
                ->cascadeOnDelete();

            $table->foreignId('periode_id')
                ->constrained('periodes')
                ->cascadeOnDelete();

            $table->foreignId('kriteria_id')
                ->constrained('kriterias')
                ->cascadeOnDelete();

            $table->decimal('nilai', 6, 2);

            $table->unique([
                'siswa_id',
                'periode_id',
                'kriteria_id'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('nilais');
    }
};
