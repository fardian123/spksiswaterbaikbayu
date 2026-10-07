<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jurusan;

class JurusanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Jurusan::create([
            'kode' => 'MP',
            'nama' => 'Manajemen Perhotelan',
        ]);

        Jurusan::create([
            'kode' => 'BR',
            'nama' => 'Broadcasting',
        ]);

        Jurusan::create([
            'kode' => 'AK',
            'nama' => 'Akuntansi',
        ]);

        Jurusan::create([
            'kode' => 'TKJ',
            'nama' => 'Teknik Komputer dan Jaringan',
        ]);
    }
}
