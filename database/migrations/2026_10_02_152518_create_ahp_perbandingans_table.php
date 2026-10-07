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
        Schema::create('ahp_perbandingans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('kriteria_1_id')
                ->constrained('kriterias')
                ->cascadeOnDelete();

            $table->foreignId('kriteria_2_id')
                ->constrained('kriterias')
                ->cascadeOnDelete();

            $table->decimal('nilai', 8, 4);

            $table->unique([
                'kriteria_1_id',
                'kriteria_2_id'
            ]);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahp_perbandingans');
    }
};
