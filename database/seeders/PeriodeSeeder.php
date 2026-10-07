<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Periode;

class PeriodeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Periode::create([
            'nama' => 'Semester Ganjil 2025/2026',
            'tahun_ajaran' => '2025/2026',
            'semester' => 'Ganjil',
            'is_aktif' => true,
        ]);
    }
}
