@extends('layouts.app')
@section('title', 'Data Periode')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h5 class="mb-0">Daftar Periode</h5>
        <a href="{{ route('admin.periode.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i>
            Tambah</a>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Tahun Ajaran</th>
                        <th>Semester</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($periodes as $p)
                        <tr>
                            <td>{{ $p->nama }}</td>
                            <td>{{ $p->tahun_ajaran }}</td>
                            <td>{{ $p->semester }}</td>
                            <td>
                                @if ($p->is_aktif) <span class="badge text-bg-success">Aktif</span>
                                @else <span class="badge text-bg-secondary">Nonaktif</span> @endif
                            </td>
                            <td class="text-end">
                                @unless ($p->is_aktif)
                                    <form method="POST" action="{{ route('admin.periode.aktifkan', $p) }}" class="d-inline">
                                        @csrf <button class="btn btn-sm btn-success">Aktifkan</button>
                                    </form>
                                @endunless
                                <a href="{{ route('admin.periode.edit', $p) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" action="{{ route('admin.periode.destroy', $p) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus periode beserta nilai dan rankingnya?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center text-muted">Belum ada periode.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection