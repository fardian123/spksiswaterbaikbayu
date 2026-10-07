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
        Schema::create('ahp_hasils', function (Blueprint $table) {
            $table->id();

            $table->decimal('lambda_max', 10, 6);
            $table->decimal('ci', 10, 6);
            $table->decimal('ri', 6, 2);
            $table->decimal('cr', 10, 6);

            $table->boolean('is_konsisten');

            $table->json('detail');

            $table->boolean('is_aktif')
                ->default(false);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ahp_hasils');
    }
};
