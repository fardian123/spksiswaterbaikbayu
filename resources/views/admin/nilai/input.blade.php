@extends('layouts.app')
@section('title', 'Input Nilai Siswa')

@section('content')
    @include('admin.nilai._filter', ['action' => route('admin.nilai.create')])

    @if (!$periode)
        <div class="alert alert-warning">Belum ada periode. Buat dan aktifkan periode terlebih dahulu.</div>
    @elseif ($siswas->isEmpty())
        <div class="alert alert-info">Tidak ada siswa untuk filter ini.</div>
    @else
        <form method="POST" action="{{ route('admin.nilai.store') }}">
            @csrf
            <input type="hidden" name="periode_id" value="{{ $periode->id }}">
            <input type="hidden" name="jurusan_id" value="{{ request('jurusan_id') }}">

            <div class="d-flex justify-content-between mb-3">
                <h5 class="mb-0">Input Nilai - {{ $periode->nama }}</h5>
                <button class="btn btn-primary btn-sm"><i class="bi bi-save"></i> Simpan Semua</button>
            </div>
            <div class="card">
                <div class="table-responsive">
                    <table class="table table-sm mb-0 align-middle">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nama Siswa</th>
                                <th>Jurusan</th>
                                @foreach ($kriterias as $k)
                                    <th style="min-width:110px">{{ $k->nama }}</th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($siswas as $s)
                                @php $n = $s->nilais->pluck('nilai', 'kriteria_id'); @endphp
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $s->nama }}</td>
                                    <td>{{ $s->jurusan->kode }}</td>
                                    @foreach ($kriterias as $k)
                                        <td>
                                            <input type="number" step="0.01" min="0" max="100" name="nilai[{{ $s->id }}][{{ $k->id }}]"
                                                value="{{ old('nilai.' . $s->id . '.' . $k->id, $n[$k->id] ?? '') }}"
                                                class="form-control form-control-sm">
                                        </td>
                                    @endforeach
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
            <div class="form-text mt-2">Skala nilai 0-100. Kolom kosong diabaikan (tidak menghapus nilai lama).</div>
            <button class="btn btn-primary mt-3"><i class="bi bi-save"></i> Simpan Semua</button>
        </form>
    @endif
@endsection