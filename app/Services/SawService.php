<?php

namespace App\Services;

use App\Models\AhpHasil;
use App\Models\HasilRanking;
use App\Models\Kriteria;
use App\Models\Periode;
use App\Models\Siswa;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class SawService
{
    public function hitung(Periode $periode): array
    {
        $ahp = AhpHasil::aktif()->first();
        if (!$ahp || !$ahp->is_konsisten) {
            throw new RuntimeException('Belum ada bobot AHP aktif yang konsisten. Jalankan dan terapkan AHP terlebih dahulu.');
        }

        $kriterias = Kriteria::orderBy('urutan')->get();
        if ($kriterias->sum('bobot') <= 0) {
            throw new RuntimeException('Bobot kriteria masih 0. Terapkan hasil AHP terlebih dahulu.');
        }

        $siswas = Siswa::with([
            'jurusan',
            'nilais' => fn ($q) => $q->where('periode_id', $periode->id),
        ])->get();

        // ---- 1. Matriks keputusan (hanya siswa dengan nilai lengkap) ----
        $lengkap = [];
        $dilewati = [];
        $tanpaNilai = 0;

        foreach ($siswas as $s) {
            $nilai = $s->nilais->pluck('nilai', 'kriteria_id');

            if ($nilai->isEmpty()) {
                $tanpaNilai++;
                continue;
            }
            if (!$kriterias->every(fn ($k) => $nilai->has($k->id))) {
                $dilewati[] = ['nama' => $s->nama, 'jurusan' => $s->jurusan->kode, 'alasan' => 'Nilai kriteria belum lengkap'];;
                continue;
            }

            $x = [];
            foreach ($kriterias as $k) {
                $x[$k->id] = (float) $nilai[$k->id];
            }
            $lengkap[$s->id] = ['jurusan_id' => $s->jurusan_id, 'x' => $x];
        }

        if (empty($lengkap)) {
            throw new RuntimeException('Tidak ada siswa dengan nilai lengkap pada periode ini.');
        }

        // ---- 2. Max / min tiap kriteria ----
        $semuaX = array_column($lengkap, 'x');
        $maks = [];
        $min = [];
        foreach ($kriterias as $k) {
            $kolom = array_column($semuaX, $k->id);
            $maks[$k->id] = max($kolom);
            $min[$k->id] = min($kolom);
        }

        $kriteriaPertama = $kriterias->first()->id; // untuk tie-break

        // ---- 3-4. Normalisasi, bobot, total ----
        $rows = [];
        foreach ($lengkap as $siswaId => $data) {
            $r = [];
            $vw = [];
            $total = 0.0;

            foreach ($kriterias as $k) {
                $x = $data['x'][$k->id];

                if ($k->tipe === 'cost') {
                    $r[$k->id] = $x > 0 ? $min[$k->id] / $x : 1.0;
                } else {
                    $r[$k->id] = $maks[$k->id] > 0 ? $x / $maks[$k->id] : 0.0;
                }

                $vw[$k->id] = $r[$k->id] * (float) $k->bobot;
                $total += $vw[$k->id];
            }

            $rows[] = [
                'siswa_id'   => $siswaId,
                'jurusan_id' => $data['jurusan_id'],
                'v'          => round($total, 6),
                'tie'        => $data['x'][$kriteriaPertama],
                'x'          => $data['x'],
                'r'          => $r,
                'vw'         => $vw,
            ];
        }

        // ---- 5. Ranking umum & per jurusan ----
        $rankUmum = $this->beriRanking($rows);

        $rankJurusan = [];
        foreach (collect($rows)->groupBy('jurusan_id') as $grup) {
            $rankJurusan += $this->beriRanking($grup->all());
        }

        // ---- 6. Simpan ----
        DB::transaction(function () use ($rows, $rankUmum, $rankJurusan, $periode, $ahp, $kriterias) {
            // hapus ranking lama yang siswanya tidak ikut dihitung lagi
            HasilRanking::where('periode_id', $periode->id)
                ->whereNotIn('siswa_id', array_column($rows, 'siswa_id'))
                ->delete();

            foreach ($rows as $row) {
                HasilRanking::updateOrCreate(
                    ['periode_id' => $periode->id, 'siswa_id' => $row['siswa_id']],
                    [
                        'ahp_hasil_id'    => $ahp->id,
                        'nilai_akhir'     => $row['v'],
                        'ranking_umum'    => $rankUmum[$row['siswa_id']],
                        'ranking_jurusan' => $rankJurusan[$row['siswa_id']],
                        'detail'          => [
                            'bobot' => $kriterias->pluck('bobot', 'id')->map(fn ($b) => (float) $b)->all(),
                            'x'     => $row['x'],
                            'r'     => $row['r'],
                            'vw'    => $row['vw'],
                        ],
                    ]
                );
            }
        });

        return [
            'jumlah'       => count($rows),
            'dilewati'     => $dilewati,
            'tanpa_nilai'  => $tanpaNilai,
            'ahp_hasil_id' => $ahp->id,
        ];
    }

    /**
     * Competition ranking (1,2,2,4). Urut: nilai akhir desc, lalu nilai C1 desc.
     * @return array<int,int> siswa_id => ranking
     */
    private function beriRanking(array $rows): array
    {
        usort($rows, fn ($a, $b) => [$b['v'], $b['tie']] <=> [$a['v'], $a['tie']]);

        $hasil = [];
        $prev = null;
        $rank = 0;
        foreach ($rows as $i => $row) {
            $key = [$row['v'], $row['tie']];
            if ($key !== $prev) {
                $rank = $i + 1;
                $prev = $key;
            }
            $hasil[$row['siswa_id']] = $rank;
        }
        return $hasil;
    }
}

