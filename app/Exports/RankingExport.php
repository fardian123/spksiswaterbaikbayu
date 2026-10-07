<?php

namespace App\Exports;

use App\Models\HasilRanking;
use App\Models\Periode;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Illuminate\Support\Collection;
class RankingExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(private Periode $periode, private ?int $jurusanId = null)
    {
    }

    public function collection(): Collection
    {
        return HasilRanking::with('siswa.jurusan')
            ->where('periode_id', $this->periode->id)
            ->when($this->jurusanId, fn($q, $id) => $q->whereHas(
                'siswa',
                fn($s) => $s->where('jurusan_id', $id)
            ))
            ->orderBy($this->jurusanId ? 'ranking_jurusan' : 'ranking_umum')
            ->get();
    }

    public function headings(): array
    {
        return ['Ranking Umum', 'Ranking Jurusan', 'Nama', 'Jurusan', 'Nilai Akhir'];
    }

    public function map($row): array
    {
        return [
            $row->ranking_umum,
            $row->ranking_jurusan,
            $row->siswa->nama,
            $row->siswa->jurusan->nama,
            (float) $row->nilai_akhir,
        ];
    }
}
