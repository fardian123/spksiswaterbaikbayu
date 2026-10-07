@extends('layouts.app')
@section('title', 'Perhitungan SAW')

@section('content')
    @if (!$ahpAktif)
        <div class="alert alert-warning">
            Bobot AHP belum diterapkan. <a href="{{ route('admin.ahp.index') }}">Jalankan AHP</a> terlebih dahulu.
        </div>
    @endif

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card mb-3">
                <div class="card-header">Bobot yang Dipakai</div>
                <ul class="list-group list-group-flush">
                    @foreach ($kriterias as $k)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ $k->kode }} - {{ $k->nama }} <small class="text-muted">({{ $k->tipe }})</small></span>
                            <strong>{{ number_format($k->bobot, 4, ',', '.') }}</strong>
                        </li>
                    @endforeach
                </ul>
            </div>
            <div class="card">
                <div class="card-header">Hitung SAW</div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.saw.hitung') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Periode</label>
                            <select name="periode_id" class="form-select" required>
                                @foreach ($periodes as $p)
                                    <option value="{{ $p->id }}" @selected(old('periode_id', $aktif?->id) == $p->id)>
                                        {{ $p->nama }} {{ $p->is_aktif ? '(aktif)' : '' }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button class="btn btn-primary" @disabled(!$ahpAktif)><i class="bi bi-graph-up"></i> Hitung
                            SAW</button>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7">
            <div class="card">
                <div class="card-header">Status Perhitungan per Periode</div>
                <div class="table-responsive">
                    <table class="table mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>Periode</th>
                                <th>Siswa Terhitung</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($periodes as $p)
                                <tr>
                                    <td>{{ $p->nama }}</td>
                                    <td>{{ $p->hasil_rankings_count }}</td>
                                    <td class="text-end">
                                        @if ($p->hasil_rankings_count)
                                            <a href="{{ route('admin.saw.detail', $p) }}"
                                                class="btn btn-sm btn-outline-primary">Detail</a>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center text-muted">Belum ada periode.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection