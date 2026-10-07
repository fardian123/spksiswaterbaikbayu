<?php

namespace App\Services;

use App\Models\AhpHasil;
use App\Models\AhpPerbandingan;
use App\Models\Kriteria;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class AhpService
{
    // Pilihan dropdown skala Saaty (C_i dibanding C_j)
    public const SKALA_OPSI = [
        '9', '8', '7', '6', '5', '4', '3', '2', '1',
        '1/2', '1/3', '1/4', '1/5', '1/6', '1/7', '1/8', '1/9',
    ];

    // Random Index (Saaty)
    public const RI = [
        1 => 0.00, 2 => 0.00, 3 => 0.58, 4 => 0.90, 5 => 1.12,
        6 => 1.24, 7 => 1.32, 8 => 1.41, 9 => 1.45, 10 => 1.49,
    ];

    public const CR_MAKS = 0.10;

    public function getKriterias(): Collection
    {
        return Kriteria::orderBy('urutan')->get();
    }

    /** "1/3" => 0.3333..., "5" => 5.0 */
    public function parseSkala(string|int|float $nilai): float
    {
        $nilai = (string) $nilai;
        if (str_contains($nilai, '/')) {
            [$a, $b] = explode('/', $nilai);
            return (float) $a / (float) $b;
        }
        return (float) $nilai;
    }

    /** Bulatkan ke nilai skala Saaty terdekat (menghindari error 0.3333 vs 1/3). */
    public function snapSaaty(float $nilai): float
    {
        if ($nilai <= 0) {
            return 1.0;
        }
        $terdekat = 1.0;
        $selisihMin = INF;
        foreach (self::SKALA_OPSI as $opsi) {
            $k = $this->parseSkala($opsi);
            $selisih = abs(log($nilai) - log($k));
            if ($selisih < $selisihMin) {
                $selisihMin = $selisih;
                $terdekat = $k;
            }
        }
        return $terdekat;
    }

    /** Ubah angka tersimpan kembali ke label dropdown ("0.3333" => "1/3"). */
    public function labelSkala(float $nilai): string
    {
        $snap = $this->snapSaaty($nilai);
        foreach (self::SKALA_OPSI as $opsi) {
            if (abs($this->parseSkala($opsi) - $snap) < 1e-9) {
                return $opsi;
            }
        }
        return '1';
    }

    /**
     * Nilai tersimpan untuk form: [kriteria_1_id][kriteria_2_id] => label dropdown
     */
    public function nilaiForm(Collection $kriterias): array
    {
        $hasil = [];
        foreach (AhpPerbandingan::all() as $p) {
            $hasil[$p->kriteria_1_id][$p->kriteria_2_id] = $this->labelSkala((float) $p->nilai);
        }
        return $hasil;
    }

    /** Simpan segitiga atas: input[kriteria_i_id][kriteria_j_id] = "3" / "1/3" */
    public function simpanPerbandingan(array $input, Collection $kriterias): void
    {
        $ids = $kriterias->pluck('id')->values()->all();
        $n = count($ids);

        DB::transaction(function () use ($input, $ids, $n) {
            for ($i = 0; $i < $n; $i++) {
                for ($j = $i + 1; $j < $n; $j++) {
                    $nilai = $this->parseSkala($input[$ids[$i]][$ids[$j]] ?? '1');
                    AhpPerbandingan::updateOrCreate(
                        ['kriteria_1_id' => $ids[$i], 'kriteria_2_id' => $ids[$j]],
                        ['nilai' => round($nilai, 4)]
                    );
                }
            }
        });
    }

    /** Bangun matriks n x n dari DB (diagonal 1, segitiga bawah = kebalikan). */
    public function buildMatrix(Collection $kriterias): array
    {
        $ids = $kriterias->pluck('id')->values()->all();
        $idx = array_flip($ids);
        $n = count($ids);
        $matrix = array_fill(0, $n, array_fill(0, $n, 1.0));

        foreach (AhpPerbandingan::all() as $p) {
            if (!isset($idx[$p->kriteria_1_id], $idx[$p->kriteria_2_id])) {
                continue;
            }
            $i = $idx[$p->kriteria_1_id];
            $j = $idx[$p->kriteria_2_id];
            $v = $this->snapSaaty((float) $p->nilai);
            $matrix[$i][$j] = $v;
            $matrix[$j][$i] = 1 / $v;
        }

        return $matrix;
    }

    /** Inti perhitungan AHP. */
   public function hitung(array $matrix): array
    {
        $n = count($matrix);

        // 1. jumlah tiap kolom
        $jumlahKolom = array_fill(0, $n, 0.0);
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $jumlahKolom[$j] += $matrix[$i][$j];
            }
        }

        // 2. normalisasi
        $normalisasi = [];
        for ($i = 0; $i < $n; $i++) {
            for ($j = 0; $j < $n; $j++) {
                $normalisasi[$i][$j] = $matrix[$i][$j] / $jumlahKolom[$j];
            }
        }

        // 3. bobot prioritas = rata-rata baris
        $bobot = [];
        for ($i = 0; $i < $n; $i++) {
            $bobot[$i] = array_sum($normalisasi[$i]) / $n;
        }

        // 4. lambda max = rata-rata dari ((A x w)_i / w_i)
        $aw = [];
        $lambdaI = [];
        for ($i = 0; $i < $n; $i++) {
            $aw[$i] = 0.0;
            for ($j = 0; $j < $n; $j++) {
                $aw[$i] += $matrix[$i][$j] * $bobot[$j];
            }
            $lambdaI[$i] = $bobot[$i] > 0 ? $aw[$i] / $bobot[$i] : 0.0;
        }
        $lambdaMax = array_sum($lambdaI) / $n;

        // 5-8. CI, RI, CR
        $ci = $n > 1 ? max(0.0, ($lambdaMax - $n) / ($n - 1)) : 0.0;
        $ri = self::RI[$n] ?? 1.49;
        $cr = $ri > 0 ? $ci / $ri : 0.0;

        return [
            'n'            => $n,
            'matrix'       => $matrix,
            'jumlah_kolom' => $jumlahKolom,
            'normalisasi'  => $normalisasi,
            'bobot'        => $bobot,
            'aw'           => $aw,
            'lambda_i'     => $lambdaI,
            'lambda_max'   => $lambdaMax,
            'ci'           => $ci,
            'ri'           => $ri,
            'cr'           => $cr,
            'is_konsisten' => round($cr, 6) <= self::CR_MAKS,
        ];
    }

    /** Hitung dari data DB lalu simpan sebagai riwayat (belum aktif). */
    public function jalankan(): AhpHasil
    {
        $kriterias = $this->getKriterias();
        if ($kriterias->count() < 2) {
            throw new RuntimeException('Minimal harus ada 2 kriteria untuk AHP.');
        }

        $hasil = $this->hitung($this->buildMatrix($kriterias));

        $hasil['kriteria'] = $kriterias->map(fn ($k) => [
            'id' => $k->id, 'kode' => $k->kode, 'nama' => $k->nama,
        ])->values()->all();

        return AhpHasil::create([
            'lambda_max'   => round($hasil['lambda_max'], 6),
            'ci'           => round($hasil['ci'], 6),
            'ri'           => $hasil['ri'],
            'cr'           => round($hasil['cr'], 6),
            'is_konsisten' => $hasil['is_konsisten'],
            'detail'       => $hasil,
            'is_aktif'     => false,
        ]);
    }

    /** Terapkan bobot ke kriterias.bobot, hanya jika konsisten. */
    public function terapkan(AhpHasil $ahpHasil): void
    {
        if (!$ahpHasil->is_konsisten) {
            throw new RuntimeException(
                'Hasil AHP tidak konsisten (CR > 0,1). Revisi perbandingan terlebih dahulu.'
            );
        }

        $detail = $ahpHasil->detail;

        DB::transaction(function () use ($ahpHasil, $detail) {
            foreach ($detail['kriteria'] as $i => $k) {
                Kriteria::where('id', $k['id'])->update([
                    'bobot' => round($detail['bobot'][$i], 6),
                ]);
            }
            AhpHasil::where('is_aktif', true)->update(['is_aktif' => false]);
            $ahpHasil->update(['is_aktif' => true]);
        });
    }
}

