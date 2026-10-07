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
        Schema::create('kriterias', function (Blueprint $table) {
            $table->id();

            $table->string('kode', 5)->unique();
            $table->string('nama', 100);

            $table->enum('tipe', ['benefit', 'cost'])
                ->default('benefit');

            $table->decimal('bobot', 10, 6)
                ->default(0);

            $table->unsignedTinyInteger('urutan');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kriterias');
    }
};
