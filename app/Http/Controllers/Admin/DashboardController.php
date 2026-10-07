<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AhpHasil;
use App\Models\HasilRanking;
use App\Models\Jurusan;
use App\Models\Kriteria;
use App\Models\Periode;
use App\Models\Siswa;

class DashboardController extends Controller
{
    public function index()
    {
        $periode = Periode::aktif()->first();

        return view('admin.dashboard', [
            'jumlahSiswa' => Siswa::count(),
            'jumlahJurusan' => Jurusan::count(),
            'periode' => $periode,
            'ahpAktif' => AhpHasil::aktif()->first(),
            'kriterias' => Kriteria::orderBy('urutan')->get(),
            'topRanking' => $periode
                ? HasilRanking::with('siswa.jurusan')
                    ->where('periode_id', $periode->id)
                    ->orderBy('ranking_umum')->limit(5)->get()
                : collect(),
        ]);
    }
}