<?php

namespace App\Http\Controllers\Admin;

use App\Exports\RankingExport;
use App\Http\Controllers\Controller;
use App\Models\HasilRanking;
use App\Models\Jurusan;
use App\Models\Periode;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class RankingController extends Controller
{
    public function umum(Request $request)
    {
        $periode = $this->periode($request);

        $rankings = $periode
            ? HasilRanking::with('siswa.jurusan')
                ->where('periode_id', $periode->id)
                ->orderBy('ranking_umum')->orderByDesc('nilai_akhir')
                ->paginate(25)->withQueryString()
            : null;

        return view('admin.ranking.umum', [
            'periode'  => $periode,
            'periodes' => Periode::orderByDesc('id')->get(),
            'jurusans' => Jurusan::orderBy('kode')->get(),
            'rankings' => $rankings,
        ]);
    }

    public function perJurusan(Request $request, Jurusan $jurusan)
    {
        $periode = $this->periode($request);

        $rankings = $periode
            ? HasilRanking::with('siswa.jurusan')
                ->where('periode_id', $periode->id)
                ->whereHas('siswa', fn ($q) => $q->where('jurusan_id', $jurusan->id))
                ->orderBy('ranking_jurusan')->orderByDesc('nilai_akhir')
                ->paginate(25)->withQueryString()
            : null;

        return view('admin.ranking.jurusan', [
            'periode'  => $periode,
            'periodes' => Periode::orderByDesc('id')->get(),
            'jurusan'  => $jurusan,
            'jurusans' => Jurusan::orderBy('kode')->get(),
            'rankings' => $rankings,
        ]);
    }

    public function export(Request $request)
    {
        $periode = $this->periode($request);
        abort_if(!$periode, 404, 'Periode tidak ditemukan.');

        return Excel::download(
            new RankingExport($periode, $request->integer('jurusan_id') ?: null),
            'ranking-' . str($periode->nama)->slug() . '.xlsx'
        );
    }

    private function periode(Request $request): ?Periode
    {
        return $request->filled('periode_id')
            ? Periode::find($request->periode_id)
            : Periode::aktif()->first();
    }
}