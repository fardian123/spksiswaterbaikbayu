@extends('layouts.app')
@section('title', 'Nilai Siswa')

@section('content')
    @include('admin.nilai._filter', ['action' => route('admin.nilai.index')])

    @if (!$periode)
        <div class="alert alert-warning">Belum ada periode. Buat dan aktifkan periode terlebih dahulu.</div>
    @else
        <div class="d-flex justify-content-between mb-3">
            <h5 class="mb-0">Rekap Nilai - {{ $periode->nama }}</h5>
            <a href="{{ route('admin.nilai.create', request()->query()) }}" class="btn btn-primary btn-sm">
                <i class="bi bi-pencil-square"></i> Input / Ubah Nilai
            </a>
        </div>
        <div class="card">
            <div class="table-responsive">
                <table class="table table-hover mb-0 align-middle">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama Siswa</th>
                            <th>Jurusan</th>
                            @foreach ($kriterias as $k)
                                <th class="text-end" title="{{ $k->nama }}">{{ $k->nama }}</th>
                            @endforeach
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($siswas as $s)
                            @php $n = $s->nilais->pluck('nilai', 'kriteria_id'); @endphp
                            <tr>
                                <td>{{ $siswas->firstItem() + $loop->index }}</td>
                                <td>{{ $s->nama }}</td>
                                <td>{{ $s->jurusan->kode }}</td>
                                @foreach ($kriterias as $k)
                                    <td class="text-end">{{ isset($n[$k->id]) ? number_format($n[$k->id], 2, ',', '.') : '-' }}</td>
                                @endforeach
                                <td>
                                    @if ($kriterias->every(fn($k) => $n->has($k->id)))
                                        <span class="badge text-bg-success">Lengkap</span>
                                    @else
                                        <span class="badge text-bg-warning">Belum lengkap</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 4 + $kriterias->count() }}" class="text-center text-muted">Tidak ada data.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="mt-3">{{ $siswas->links() }}</div>
    @endif
@endsection