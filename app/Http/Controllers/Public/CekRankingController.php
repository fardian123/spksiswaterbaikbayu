<?php

namespace App\Http\Controllers\Public;
use App\Http\Controllers\Controller;
use App\Models\HasilRanking;
use App\Models\Jurusan;
use App\Models\Periode;
use App\Models\Siswa;
use Illuminate\Http\Request;

class CekRankingController extends Controller
{
    public function index()
    {
        $periodes = Periode::whereHas('hasilRankings')->orderByDesc('id')->get();

        return view('public.cek-ranking', [
            'periodes' => $periodes,
            'jurusans' => Jurusan::orderBy('kode')->get(),
            'aktif' => $periodes->firstWhere('is_aktif', true) ?? $periodes->first(),
        ]);
    }

    public function cek(Request $request)
    {
        $data = $request->validate([
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'nama' => ['required', 'string', 'max:150'],
            'periode_id' => ['required', 'exists:periodes,id'],
        ]);

        $nama = preg_replace('/\s+/u', ' ', trim($data['nama']));

        $siswa = Siswa::with('jurusan')
            ->where('jurusan_id', $data['jurusan_id'])
            ->where('nama', $nama)   // collation MySQL default: tidak peka huruf besar/kecil
            ->first();

        if (!$siswa) {
            return back()->withInput()
                ->withErrors(['nama' => 'Data tidak ditemukan. Periksa jurusan dan penulisan nama lengkap.']);
        }

        $hasil = HasilRanking::where('periode_id', $data['periode_id'])
            ->where('siswa_id', $siswa->id)->first();

        if (!$hasil) {
            return back()->withInput()
                ->withErrors(['nama' => 'Ranking untuk periode ini belum tersedia.']);
        }

        return view('public.hasil-ranking', [
            'siswa' => $siswa,
            'hasil' => $hasil,
            'periode' => Periode::find($data['periode_id']),
            'totalUmum' => HasilRanking::where('periode_id', $data['periode_id'])->count(),
            'totalJurusan' => HasilRanking::where('periode_id', $data['periode_id'])
                ->whereHas('siswa', fn($q) => $q->where('jurusan_id', $siswa->jurusan_id))->count(),
        ]);
    }
}