@extends('layouts.app')
@section('title', 'Dashboard')

@section('content')
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Jumlah Siswa</div>
                    <div class="fs-3 fw-bold">{{ $jumlahSiswa }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Jumlah Jurusan</div>
                    <div class="fs-3 fw-bold">{{ $jumlahJurusan }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Periode Aktif</div>
                    <div class="fw-bold">{{ $periode->nama ?? 'Belum ada' }}</div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="text-muted small">Bobot AHP</div>
                    @if ($ahpAktif)
                        <span class="badge text-bg-success">Aktif (CR {{ number_format($ahpAktif->cr, 4, ',', '.') }})</span>
                    @else
                        <span class="badge text-bg-warning">Belum diterapkan</span>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card h-100">
                <div class="card-header">Bobot Kriteria</div>
                <ul class="list-group list-group-flush">
                    @foreach ($kriterias as $k)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $k->kode }} - {{ $k->nama }}</span>
                            <strong>{{ number_format($k->bobot * 100, 2, ',', '.') }}%</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card h-100">
                <div class="card-header">Top 5 Ranking {{ $periode ? '- ' . $periode->nama : '' }}</div>
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama</th>
                                <th>Jurusan</th>
                                <th class="text-end">Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($topRanking as $r)
                                <tr>
                                    <td>{{ $r->ranking_umum }}</td>
                                    <td>{{ $r->siswa->nama }}</td>
                                    <td>{{ $r->siswa->jurusan->kode }}</td>
                                    <td class="text-end">{{ number_format($r->nilai_akhir, 4, ',', '.') }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">Belum ada hasil ranking.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection