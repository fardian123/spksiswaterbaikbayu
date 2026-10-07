@extends('layouts.app')
@section('title', 'Perhitungan AHP')

@section('content')
<div class="row g-3">
    <div class="col-xl-8">
        <div class="card">
            <div class="card-header">Matriks Perbandingan Berpasangan</div>
            <div class="card-body">
                <p class="small text-muted">
                    Pilih seberapa penting kriteria <strong>baris</strong> dibanding kriteria <strong>kolom</strong>
                    (skala Saaty 1-9). Nilai kebalikan di segitiga bawah terisi otomatis.
                    Nilai sebaiknya berasal dari judgment guru/ahli.
                </p>
                <form method="POST" action="{{ route('admin.ahp.hitung') }}">
                    @csrf
                    <div class="table-responsive">
                        <table class="table table-bordered text-center align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th></th>
                                    @foreach ($kriterias as $k)
                                        <th>{{ $k->kode }}<br><small class="fw-normal">{{ $k->nama }}</small></th>
                                    @endforeach
                                </tr>
                            </thead>
                            <tbody>
                            @foreach ($kriterias as $i => $a)
                                <tr>
                                    <th class="text-start table-light">{{ $a->kode }} - {{ $a->nama }}</th>
                                    @foreach ($kriterias as $j => $b)
                                        @if ($i === $j)
                                            <td class="table-secondary">1</td>
                                        @elseif ($i < $j)
                                            <td style="min-width:110px">
                                                <select name="perbandingan[{{ $a->id }}][{{ $b->id }}]"
                                                        class="form-select form-select-sm ahp-select"
                                                        data-target="inv-{{ $b->id }}-{{ $a->id }}">
                                                    @foreach ($skalaOpsi as $opsi)
                                                        <option value="{{ $opsi }}"
                                                            @selected(old('perbandingan.' . $a->id . '.' . $b->id, $nilaiForm[$a->id][$b->id] ?? '1') === $opsi)>
                                                            {{ $opsi }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                            </td>
                                        @else
                                            <td class="table-light text-muted">
                                                <span id="inv-{{ $a->id }}-{{ $b->id }}">1</span>
                                            </td>
                                        @endif
                                    @endforeach
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                    <button class="btn btn-primary"><i class="bi bi-calculator"></i> Simpan & Hitung AHP</button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="card mb-3">
            <div class="card-header">Status</div>
            <div class="card-body small">
                <div class="mb-2">Bobot aktif:
                    @if ($ahpAktif)
                        <span class="badge text-bg-success">AHP #{{ $ahpAktif->id }}</span>
                    @else
                        <span class="badge text-bg-warning">Belum ada</span>
                    @endif
                </div>
                @if ($hasilTerakhir)
                    <div>Hitungan terakhir: #{{ $hasilTerakhir->id }}
                        (CR {{ number_format($hasilTerakhir->cr, 4, ',', '.') }},
                        {{ $hasilTerakhir->is_konsisten ? 'konsisten' : 'tidak konsisten' }})</div>
                    <a href="{{ route('admin.ahp.hasil', $hasilTerakhir) }}" class="btn btn-sm btn-outline-primary mt-2">Lihat detail</a>
                @endif
                <a href="{{ route('admin.ahp.riwayat') }}" class="btn btn-sm btn-outline-secondary mt-2">Riwayat</a>
            </div>
        </div>
        <div class="card">
            <div class="card-header">Skala Saaty</div>
            <table class="table table-sm mb-0 small">
                <tr><td>1</td><td>Sama penting</td></tr>
                <tr><td>3</td><td>Sedikit lebih penting</td></tr>
                <tr><td>5</td><td>Lebih penting</td></tr>
                <tr><td>7</td><td>Sangat penting</td></tr>
                <tr><td>9</td><td>Mutlak lebih penting</td></tr>
                <tr><td>2,4,6,8</td><td>Nilai tengah</td></tr>
                <tr><td>1/x</td><td>Kebalikan (kolom lebih penting)</td></tr>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    const kebalikan = v => v.includes('/') ? v.split('/')[1] : (v === '1' ? '1' : '1/' + v);
    document.querySelectorAll('.ahp-select').forEach(sel => {
        const update = () => {
            const el = document.getElementById(sel.dataset.target);
            if (el) el.textContent = kebalikan(sel.value);
        };
        sel.addEventListener('change', update);
        update();
    });
</script>
@endpush
