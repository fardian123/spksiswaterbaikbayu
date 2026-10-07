<?php

namespace App\Imports;

use Illuminate\Database\Eloquent\Model;

use App\Models\Kriteria;
use App\Models\Nilai;
use App\Models\Periode;
use App\Models\Siswa;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Maatwebsite\Excel\Concerns\SkipsOnFailure;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithCustomCsvSettings;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;

class NilaiImport implements ToModel, WithHeadingRow, WithValidation, SkipsOnFailure, WithCustomCsvSettings
{
    use Importable, SkipsFailures;

    // kode kriteria => nama kolom CSV
    public const KOLOM = [
        'C1' => 'nilai_akademik',
        'C2' => 'kehadiran',
        'C3' => 'sikap',
        'C4' => 'keaktifan',
    ];

    public int $berhasil = 0;
    private array $siswaMap;
    private array $kriteriaMap; // kolom => kriteria_id

    public function __construct(private Periode $periode, private string $delimiter = ',')
    {
        $this->siswaMap = Siswa::pluck('id', 'nis')->all();

        $kriteria = Kriteria::pluck('id', 'kode');
        foreach (self::KOLOM as $kode => $kolom) {
            if (isset($kriteria[$kode])) {
                $this->kriteriaMap[$kolom] = $kriteria[$kode];
            }
        }
    }

    public function getCsvSettings(): array
    {
        return ['delimiter' => $this->delimiter];
    }

    public function prepareForValidation($data, int $index)
    {
        $data['nis'] = isset($data['nis']) ? trim((string) $data['nis']) : null;

        foreach (self::KOLOM as $kolom) {
            if (isset($data[$kolom])) {
                $data[$kolom] = str_replace(',', '.', trim((string) $data[$kolom]));
            }
        }
        return $data;
    }

    public function model(array $row): ?Model
    {
        $siswaId = $this->siswaMap[trim((string) $row['nis'])];

        foreach ($this->kriteriaMap as $kolom => $kriteriaId) {
            Nilai::updateOrCreate(
                ['siswa_id' => $siswaId, 'periode_id' => $this->periode->id, 'kriteria_id' => $kriteriaId],
                ['nilai' => str_replace(',', '.', (string) $row[$kolom])]
            );
        }

        $this->berhasil++;
        return null;
    }

    public function rules(): array
    {
        $rules = [
            'nis' => [
                'required',
                'string',
                function ($attr, $value, $fail) {
                    if (!isset($this->siswaMap[trim((string) $value)])) {
                        $fail('NIS tidak ditemukan di data siswa.');
                    }
                }
            ],
        ];
        foreach (self::KOLOM as $kolom) {
            $rules[$kolom] = ['required', 'numeric', 'between:0,100'];
        }
        return $rules;
    }
}