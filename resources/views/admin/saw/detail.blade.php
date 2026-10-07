@extends('layouts.app')
@section('title', 'Detail SAW - ' . $periode->nama)

@section('content')
<div class="d-flex justify-content-between mb-3">
    <a href="{{ route('admin.saw.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i> Kembali</a>
    <a href="{{ route('admin.ranking.umum', ['periode_id' => $periode->id]) }}" class="btn btn-primary btn-sm">
        <i class="bi bi-trophy"></i> Lihat Ranking
    </a>
</div>

@if (session('dilewati'))
    <div class="alert alert-warning">
        <strong>{{ count(session('dilewati')) }} siswa dilewati:</strong>
        <ul class="mb-0">
            @foreach (session('dilewati') as $d)
                <li>{{ $d['nama'] }} ({{ $d['jurusan'] }}) - {{ $d['alasan'] }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if ($rankings->isEmpty())
    <div class="alert alert-info">Belum ada hasil perhitungan untuk periode ini.</div>
@else
    <div class="card mb-3">
        <div class="card-header">Bobot Kriteria (AHP #{{ $rankings->first()->ahp_hasil_id }})</div>
        <div class="table-responsive"><table class="table table-sm text-center mb-0">
            <thead><tr>@foreach ($kriterias as $k)<th>{{ $k->nama }} ({{ $k->tipe }})</th>@endforeach</tr></thead>
            <tbody><tr>
                @foreach ($kriterias as $k)
                    <td>{{ number_format($rankings->first()->detail['bobot'][$k->id] ?? $k->bobot, 4, ',', '.') }}</td>
                @endforeach
            </tr></tbody>
        </table></div>
    </div>

    @foreach (['x' => '1. Matriks Keputusan (X)', 'r' => '2. Normalisasi (R)', 'vw' => '3. Nilai Terbobot (W × R)'] as $kunci => $judul)
        <div class="card mb-3">
            <div class="card-header">{{ $judul }}</div>
            <div class="table-responsive"><table class="table table-sm table-striped mb-0">
                <thead>
                    <tr><th>Nama Siswa</th><th>Jurusan</th>
                        @foreach ($kriterias as $k)<th class="text-end">{{ $k->nama }}</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                @foreach ($rankings as $r)
                    <tr>
                        <td>{{ $r->siswa->nama }}</td>
                        <td>{{ $r->siswa->jurusan->kode }}</td>
                        @foreach ($kriterias as $k)
                            <td class="text-end">
                                {{ number_format($r->detail[$kunci][$k->id] ?? 0, $kunci === 'x' ? 2 : 4, ',', '.') }}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
                </tbody>
            </table></div>
        </div>
    @endforeach

    <div class="card">
        <div class="card-header">4. Nilai Preferensi (V) & Peringkat</div>
        <div class="table-responsive"><table class="table table-sm table-striped mb-0">
            <thead>
                <tr><th>Rank Umum</th><th>Nama Siswa</th><th>Jurusan</th><th>Rank Jurusan</th><th class="text-end">Nilai V</th></tr>
            </thead>
            <tbody>
            @foreach ($rankings as $r)
                <tr>
                    <td><strong>{{ $r->ranking_umum }}</strong></td>
                    <td>{{ $r->siswa->nama }}</td>
                    <td>{{ $r->siswa->jurusan->kode }}</td>
                    <td>{{ $r->ranking_jurusan }}</td>
                    <td class="text-end">{{ number_format($r->nilai_akhir, 6, ',', '.') }}</td>
                </tr>
            @endforeach
            </tbody>
        </table></div>
    </div>
@endif
@endsection

