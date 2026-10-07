@extends('layouts.app')
@section('title', 'Data Jurusan')

@section('content')
    <div class="d-flex justify-content-between mb-3">
        <h5 class="mb-0">Daftar Jurusan</h5>
        <a href="{{ route('admin.jurusan.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i>
            Tambah</a>
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Jumlah Siswa</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($jurusans as $j)
                        <tr>
                            <td>{{ $j->kode }}</td>
                            <td>{{ $j->nama }}</td>
                            <td>{{ $j->siswas_count }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.jurusan.edit', $j) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" action="{{ route('admin.jurusan.destroy', $j) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus jurusan ini?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada jurusan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection