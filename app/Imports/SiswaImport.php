<?php

namespace App\Imports;

use App\Models\Jurusan;
use App\Models\Kriteria;
use App\Models\Nilai;
use App\Models\Periode;
use App\Models\Siswa;
use Illuminate\Database\Eloquent\Model;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use RuntimeException;

class SiswaImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithCustomCsvSettings, SkipsEmptyRows
{
    use Importable, SkipsFailures;

    // kode kriteria => nama kolom CSV
    public const KOLOM = [
        'C1' => 'akademik',
        'C2' => 'kehadiran',
        'C3' => 'sikap',
        'C4' => 'keaktifan',
    ];

    public int $berhasil = 0;
    public array $jurusanBaru = [];

    private array $jurusanMap = [];   // KODE => id
    private array $kriteriaMap = [];  // kolom => kriteria_id

    public function __construct(private Periode $periode, private string $delimiter = ',')
    {
        $this->jurusanMap = Jurusan::pluck('id', 'kode')
            ->mapWithKeys(fn($id, $kode) => [strtoupper($kode) => $id])->all();

        $kriteria = Kriteria::pluck('id', 'kode');
        foreach (self::KOLOM as $kode => $kolom) {
            if (isset($kriteria[$kode])) {
                $this->kriteriaMap[$kolom] = $kriteria[$kode];
            }
        }

        if (count($this->kriteriaMap) < count(self::KOLOM)) {
            throw new RuntimeException('Kriteria C1-C4 belum lengkap. Jalankan: php artisan db:seed --class=KriteriaSeeder');
        }
    }

    public function getCsvSettings(): array
    {
        return ['delimiter' => $this->delimiter];
    }

    public function prepareForValidation($data, int $index)
    {
        return $this->normalize($data);
    }

    public function model(array $row): ?Model
    {
        $row = $this->normalize($row);

        // jurusan belum ada -> dibuat otomatis (nama = kode, bisa diubah di menu Jurusan)
        if (!isset($this->jurusanMap[$row['jurusan']])) {
            $jurusan = Jurusan::create(['kode' => $row['jurusan'], 'nama' => $row['jurusan']]);
            $this->jurusanMap[$row['jurusan']] = $jurusan->id;
            $this->jurusanBaru[] = $row['jurusan'];
        }

        $siswa = Siswa::firstOrCreate([
            'jurusan_id' => $this->jurusanMap[$row['jurusan']],
            'nama' => $row['nama_siswa'],
        ]);

        foreach ($this->kriteriaMap as $kolom => $kriteriaId) {
            Nilai::updateOrCreate(
                ['siswa_id' => $siswa->id, 'periode_id' => $this->periode->id, 'kriteria_id' => $kriteriaId],
                ['nilai' => $row[$kolom]]
            );
        }

        $this->berhasil++;
        return null; // sudah disimpan manual
    }

    public function rules(): array
    {
        $rules = [
            'jurusan' => ['required', 'string', 'max:10'],
            'nama_siswa' => ['required', 'string', 'max:150'],
        ];
        foreach (self::KOLOM as $kolom) {
            $rules[$kolom] = ['required', 'numeric', 'between:0,100'];
        }
        return $rules;
    }

    public function customValidationAttributes(): array
    {
        return [
            'nama_siswa' => 'Nama Siswa',
            'jurusan' => 'Jurusan',
            'akademik' => 'Akademik',
            'kehadiran' => 'Kehadiran',
            'sikap' => 'Sikap',
            'keaktifan' => 'Keaktifan',
        ];
    }

    /** trim, rapikan spasi, jurusan uppercase, koma desimal -> titik */
    private function normalize(array $row): array
    {
        $nama = $row['nama_siswa'] ?? $row['nama'] ?? null;
        $row['nama_siswa'] = $nama !== null
            ? preg_replace('/\s+/u', ' ', trim((string) $nama))
            : null;

        $row['jurusan'] = isset($row['jurusan'])
            ? strtoupper(trim((string) $row['jurusan']))
            : null;

        foreach (self::KOLOM as $kolom) {
            if (isset($row[$kolom])) {
                $row[$kolom] = str_replace(',', '.', trim((string) $row[$kolom]));
            }
        }
        return $row;
    }
}