<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AhpPerbandinganRequest;
use App\Models\AhpHasil;
use App\Services\AhpService;
use RuntimeException;
class AhpController extends Controller
{
    public function __construct(private AhpService $ahp)
    {
    }

    /** Form matriks perbandingan + hasil terakhir. */
    public function index()
    {
        $kriterias = $this->ahp->getKriterias();

        return view('admin.ahp.index', [
            'kriterias'   => $kriterias,
            'nilaiForm'   => $this->ahp->nilaiForm($kriterias), // [id1][id2] => "1/3"
            'skalaOpsi'   => AhpService::SKALA_OPSI,
            'hasilTerakhir' => AhpHasil::latest()->first(),
            'ahpAktif'    => AhpHasil::aktif()->first(),
        ]);
    }

    public function hitung(AhpPerbandinganRequest $request)
    {
        try {
            $kriterias = $this->ahp->getKriterias();
            $this->ahp->simpanPerbandingan($request->input('perbandingan'), $kriterias);
            $hasil = $this->ahp->jalankan();
        } catch (RuntimeException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }

        $pesan = $hasil->is_konsisten
            ? 'Perhitungan AHP selesai. Hasil KONSISTEN, silakan terapkan bobot.'
            : 'Perhitungan AHP selesai, tetapi hasil TIDAK konsisten (CR > 0,1). Revisi perbandingan.';

        return redirect()->route('admin.ahp.hasil', $hasil)
            ->with($hasil->is_konsisten ? 'success' : 'error', $pesan);
    }

    /** Detail: matriks, normalisasi, bobot, lambda max, CI, RI, CR. */
    public function hasil(AhpHasil $ahpHasil)
    {
        return view('admin.ahp.hasil', ['hasil' => $ahpHasil, 'detail' => $ahpHasil->detail]);
    }

    public function terapkan(AhpHasil $ahpHasil)
    {
        try {
            $this->ahp->terapkan($ahpHasil);
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.kriteria.index')
            ->with('success', 'Bobot AHP berhasil diterapkan ke kriteria.');
    }

    public function riwayat()
    {
        return view('admin.ahp.riwayat', [
            'riwayat' => AhpHasil::latest()->paginate(15),
        ]);
    }
}