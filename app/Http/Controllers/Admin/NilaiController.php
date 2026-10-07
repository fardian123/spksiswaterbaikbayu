<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\NilaiRequest;
use App\Models\Jurusan;
use App\Models\Kriteria;
use App\Models\Nilai;
use App\Models\Periode;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class NilaiController extends Controller
{
    public function index(Request $request)
    {
        [$periode, $periodes] = $this->periode($request);

        $siswas = $this->querySiswa($request, $periode)->paginate(20)->withQueryString();

        return view('admin.nilai.index', [
            'periode' => $periode,
            'periodes' => $periodes,
            'jurusans' => Jurusan::orderBy('kode')->get(),
            'kriterias' => Kriteria::orderBy('urutan')->get(),
            'siswas' => $siswas,
        ]);
    }

    public function create(Request $request)
    {
        [$periode, $periodes] = $this->periode($request);

        $siswas = $periode ? $this->querySiswa($request, $periode)->get() : collect();

        return view('admin.nilai.input', [
            'periode' => $periode,
            'periodes' => $periodes,
            'jurusans' => Jurusan::orderBy('kode')->get(),
            'kriterias' => Kriteria::orderBy('urutan')->get(),
            'siswas' => $siswas,
        ]);
    }

    /** Input: nilai[siswa_id][kriteria_id]. Kolom kosong diabaikan. */
    public function store(NilaiRequest $request)
    {
        $periode = Periode::findOrFail($request->periode_id);
        $siswaValid = array_flip(Siswa::pluck('id')->all());
        $kriteriaValid = array_flip(Kriteria::pluck('id')->all());
        $tersimpan = 0;

        DB::transaction(function () use ($request, $periode, $siswaValid, $kriteriaValid, &$tersimpan) {
            foreach ($request->input('nilai', []) as $siswaId => $perKriteria) {
                if (!isset($siswaValid[$siswaId])) {
                    continue;
                }
                foreach ($perKriteria as $kriteriaId => $nilai) {
                    if ($nilai === null || $nilai === '' || !isset($kriteriaValid[$kriteriaId])) {
                        continue;
                    }
                    Nilai::updateOrCreate(
                        ['siswa_id' => $siswaId, 'periode_id' => $periode->id, 'kriteria_id' => $kriteriaId],
                        ['nilai' => $nilai]
                    );
                    $tersimpan++;
                }
            }
        });

        return redirect()
            ->route('admin.nilai.create', $request->only('periode_id', 'jurusan_id'))
            ->with('success', "{$tersimpan} nilai berhasil disimpan.");
    }

    public function destroy(Nilai $nilai)
    {
        $nilai->delete();
        return back()->with('success', 'Nilai berhasil dihapus.');
    }

    // ---------- helper ----------
    private function periode(Request $request): array
    {
        $periodes = Periode::orderByDesc('id')->get();
        $periode = $request->filled('periode_id')
            ? Periode::find($request->periode_id)
            : Periode::aktif()->first();

        return [$periode, $periodes];
    }

    private function querySiswa(Request $request, ?Periode $periode)
    {
        return Siswa::with([
            'jurusan',
            'nilais' => fn($q) => $q->where('periode_id', $periode?->id),
        ])
            ->when($request->jurusan_id, fn($q, $v) => $q->where('jurusan_id', $v))
            ->when($request->q, fn($q, $v) => $q->where('nama', 'like', "%{$v}%"))
            ->orderBy('nama');
    }
}