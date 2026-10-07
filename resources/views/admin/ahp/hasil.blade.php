@extends('layouts.app')
@section('title', 'Hasil AHP #' . $hasil->id)

@section('content')
    @php
        $kr = $detail['kriteria'];
        $n = $detail['n'];
        $f = fn($v) => number_format($v, 4, ',', '.');
    @endphp

    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
        <a href="{{ route('admin.ahp.index') }}" class="btn btn-outline-secondary btn-sm"><i class="bi bi-arrow-left"></i>
            Kembali</a>
        <div>
            @if ($hasil->is_aktif)
                <span class="badge text-bg-success fs-6">Bobot aktif</span>
            @elseif ($hasil->is_konsisten)
                <form method="POST" action="{{ route('admin.ahp.terapkan', $hasil) }}"
                    onsubmit="return confirm('Terapkan bobot ini ke kriteria?')">
                    @csrf <button class="btn btn-success btn-sm"><i class="bi bi-check-lg"></i> Terapkan Bobot</button>
                </form>
            @else
                <span class="badge text-bg-danger fs-6">Tidak konsisten - revisi perbandingan</span>
            @endif
        </div>
    </div>

    {{-- 1. Matriks perbandingan --}}
    <div class="card mb-3">
        <div class="card-header">1. Matriks Perbandingan Berpasangan</div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th></th>@foreach ($kr as $k)
                        <th>{{ $k['kode'] }}</th>@endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detail['matrix'] as $i => $row)
                        <tr>
                            <th class="table-light">{{ $kr[$i]['kode'] }}</th>
                            @foreach ($row as $v)
                            <td>{{ $f($v) }}</td>@endforeach
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Jumlah</th>
                        @foreach ($detail['jumlah_kolom'] as $v)
                        <th>{{ $f($v) }}</th>@endforeach
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- 2. Normalisasi + bobot --}}
    <div class="card mb-3">
        <div class="card-header">2. Normalisasi Matriks & Bobot Prioritas</div>
        <div class="table-responsive">
            <table class="table table-sm table-bordered text-center mb-0">
                <thead class="table-light">
                    <tr>
                        <th></th>@foreach ($kr as $k)
                        <th>{{ $k['kode'] }}</th>@endforeach<th>Bobot (rata-rata baris)</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($detail['normalisasi'] as $i => $row)
                        <tr>
                            <th class="table-light">{{ $kr[$i]['kode'] }}</th>
                            @foreach ($row as $v)
                            <td>{{ $f($v) }}</td>@endforeach
                            <td class="fw-bold">{{ $f($detail['bobot'][$i]) }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @isset($detail['aw'])
        <div class="card mb-3">
            <div class="card-header">3. Menghitung λ maks = rata-rata ( (A × w)ᵢ / wᵢ )</div>
            <div class="table-responsive">
                <table class="table table-sm table-bordered text-center mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Kriteria</th>
                            <th>(A × w)</th>
                            <th>w</th>
                            <th>(A × w) / w</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($kr as $i => $k)
                            <tr>
                                <th class="table-light">{{ $k['kode'] }}</th>
                                <td>{{ $f($detail['aw'][$i]) }}</td>
                                <td>{{ $f($detail['bobot'][$i]) }}</td>
                                <td>{{ $f($detail['lambda_i'][$i]) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="3" class="text-end">λ maks (rata-rata)</th>
                            <th>{{ number_format($hasil->lambda_max, 4, ',', '.') }}</th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    @endisset

    {{-- 4. bobot kriteria --}}
    <div class="card mb-3">
        <div class="card-header">4. Bobot Kriteria</div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Kriteria</th>
                        <th class="text-end">Bobot</th>
                        <th class="text-end">Persen</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kr as $i => $k)
                        <tr>
                            <td>{{ $k['kode'] }}</td>
                            <td>{{ $k['nama'] }}</td>
                            <td class="text-end">{{ $f($detail['bobot'][$i]) }}</td>
                            <td class="text-end">{{ number_format($detail['bobot'][$i] * 100, 2, ',', '.') }}%</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    {{-- 4. Uji konsistensi --}}
    <div class="card">
        <div class="card-header">5. Uji Konsistensi</div>
        <div class="card-body">
            <div class="row text-center g-3">
                <div class="col-6 col-md-2">
                    <div class="text-muted small">n</div><strong>{{ $n }}</strong>
                </div>
                <div class="col-6 col-md-2">
                    <div class="text-muted small">λ maks</div>
                    <strong>{{ number_format($hasil->lambda_max, 4, ',', '.') }}</strong>
                </div>
                <div class="col-6 col-md-2">
                    <div class="text-muted small">CI</div><strong>{{ number_format($hasil->ci, 4, ',', '.') }}</strong>
                </div>
                <div class="col-6 col-md-2">
                    <div class="text-muted small">RI</div><strong>{{ number_format($hasil->ri, 2, ',', '.') }}</strong>
                </div>
                <div class="col-6 col-md-2">
                    <div class="text-muted small">CR</div><strong>{{ number_format($hasil->cr, 4, ',', '.') }}</strong>
                </div>
                <div class="col-6 col-md-2">
                    <div class="text-muted small">Status</div>
                    <span class="badge text-bg-{{ $hasil->is_konsisten ? 'success' : 'danger' }}">
                        {{ $hasil->is_konsisten ? 'Konsisten (CR ≤ 0,1)' : 'Tidak konsisten' }}
                    </span>
                </div>
            </div>
            <hr>
            <div class="small text-muted">
                CI = (λ maks − n) / (n − 1) &nbsp;|&nbsp; CR = CI / RI &nbsp;|&nbsp; Konsisten jika CR ≤ 0,1
            </div>
        </div>
    </div>
@endsection