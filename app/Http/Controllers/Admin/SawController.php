<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AhpHasil;
use App\Models\HasilRanking;
use App\Models\Kriteria;
use App\Models\Periode;
use App\Services\SawService;
use Illuminate\Http\Request;
use RuntimeException;

class SawController extends Controller
{
    public function __construct(private SawService $saw)
    {
    }

    public function index()
    {
        return view('admin.saw.index', [
            'periodes' => Periode::withCount('hasilRankings')->orderByDesc('id')->get(),
            'aktif' => Periode::aktif()->first(),
            'ahpAktif' => AhpHasil::aktif()->first(),
            'kriterias' => Kriteria::orderBy('urutan')->get(),
        ]);
    }

    public function hitung(Request $request)
    {
        $data = $request->validate(['periode_id' => ['required', 'exists:periodes,id']]);
        $periode = Periode::findOrFail($data['periode_id']);

        try {
            $hasil = $this->saw->hitung($periode);
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $redirect = redirect()->route('admin.saw.detail', $periode)
            ->with('success', "Perhitungan SAW selesai untuk {$hasil['jumlah']} siswa.");

        if (!empty($hasil['dilewati'])) {
            $redirect->with('dilewati', $hasil['dilewati']);
        }
        return $redirect;
    }

    /** Tahapan: matriks keputusan -> normalisasi -> terbobot -> total. */
    public function detail(Periode $periode)
    {
        return view('admin.saw.detail', [
            'periode' => $periode,
            'kriterias' => Kriteria::orderBy('urutan')->get(),
            'rankings' => HasilRanking::with(['siswa.jurusan', 'ahpHasil'])
                ->where('periode_id', $periode->id)
                ->orderBy('ranking_umum')->orderByDesc('nilai_akhir')->get(),
        ]);
    }
}