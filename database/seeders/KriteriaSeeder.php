<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Kriteria;

class KriteriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Kriteria::create([
            'kode' => 'C1',
            'nama' => 'Nilai Akademik',
            'tipe' => 'benefit',
            'bobot' => 0,
            'urutan' => 1,
        ]);

        Kriteria::create([
            'kode' => 'C2',
            'nama' => 'Kehadiran',
            'tipe' => 'benefit',
            'bobot' => 0,
            'urutan' => 2,
        ]);

        Kriteria::create([
            'kode' => 'C3',
            'nama' => 'Sikap',
            'tipe' => 'benefit',
            'bobot' => 0,
            'urutan' => 3,
        ]);

        Kriteria::create([
            'kode' => 'C4',
            'nama' => 'Keaktifan',
            'tipe' => 'benefit',
            'bobot' => 0,
            'urutan' => 4,
        ]);
    }
}
