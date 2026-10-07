<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\SiswaRequest;
use App\Models\Jurusan;
use App\Models\Kriteria;
use App\Models\Nilai;
use App\Models\Periode;
use App\Models\Siswa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SiswaController extends Controller
{
    public function index(Request $request)
    {
        $siswas = Siswa::with('jurusan')
            ->when($request->q, fn($q, $v) => $q->where('nama', 'like', "%{$v}%"))
            ->when($request->jurusan_id, fn($q, $v) => $q->where('jurusan_id', $v))
            ->orderBy('nama')
            ->paginate(15)->withQueryString();

        return view('admin.siswa.index', [
            'siswas' => $siswas,
            'jurusans' => Jurusan::orderBy('kode')->get(),
        ]);
    }

    public function create(Request $request)
    {
        return view('admin.siswa.create', $this->formData($request));
    }

    public function store(SiswaRequest $request)
    {
        DB::transaction(function () use ($request) {
            $siswa = Siswa::create($request->only('nama', 'jurusan_id'));
            $this->simpanNilai($siswa, $request);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Siswa berhasil ditambahkan.');
    }

    public function edit(Request $request, Siswa $siswa)
    {
        return view('admin.siswa.edit', $this->formData($request, $siswa));
    }

    public function update(SiswaRequest $request, Siswa $siswa)
    {
        DB::transaction(function () use ($request, $siswa) {
            $siswa->update($request->only('nama', 'jurusan_id'));
            $this->simpanNilai($siswa, $request);
        });

        return redirect()->route('admin.siswa.index')->with('success', 'Data siswa berhasil diperbarui.');
    }

    public function destroy(Siswa $siswa)
    {
        $siswa->delete(); // nilai & ranking ikut terhapus (cascade)
        return back()->with('success', 'Siswa berhasil dihapus.');
    }

    // ---------- helper ----------
    private function formData(Request $request, ?Siswa $siswa = null): array
    {
        $periode = $request->filled('periode_id')
            ? Periode::find($request->periode_id)
            : Periode::aktif()->first();

        $data = [
            'jurusans' => Jurusan::orderBy('kode')->get(),
            'kriterias' => Kriteria::orderBy('urutan')->get(),
            'periodes' => Periode::orderByDesc('id')->get(),
            'periode' => $periode,
            'nilaiLama' => collect(),
        ];

        if ($siswa) {
            $data['siswa'] = $siswa;
            if ($periode) {
                $data['nilaiLama'] = $siswa->nilais()
                    ->where('periode_id', $periode->id)
                    ->pluck('nilai', 'kriteria_id');
            }
        }
        return $data;
    }

    private function simpanNilai(Siswa $siswa, SiswaRequest $request): void
    {
        if (!$request->filled('periode_id')) {
            return;
        }

        $kriteriaValid = array_flip(Kriteria::pluck('id')->all());

        foreach ($request->input('nilai', []) as $kriteriaId => $nilai) {
            if ($nilai === null || $nilai === '' || !isset($kriteriaValid[$kriteriaId])) {
                continue;
            }
            Nilai::updateOrCreate(
                [
                    'siswa_id' => $siswa->id,
                    'periode_id' => $request->periode_id,
                    'kriteria_id' => $kriteriaId,
                ],
                ['nilai' => $nilai]
            );
        }
    }
}
