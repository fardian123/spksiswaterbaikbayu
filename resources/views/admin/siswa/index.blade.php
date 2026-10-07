@extends('layouts.app')
@section('title', 'Data Siswa')

@section('content')
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
        <form method="GET" class="d-flex flex-wrap gap-2">
            <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama"
                style="width:220px">
            <select name="jurusan_id" class="form-select" style="width:180px">
                <option value="">Semua jurusan</option>
                @foreach ($jurusans as $j)
                    <option value="{{ $j->id }}" @selected(request('jurusan_id') == $j->id)>{{ $j->kode }}</option>
                @endforeach
            </select>
            <button class="btn btn-outline-primary"><i class="bi bi-search"></i></button>
        </form>
        <div class="d-flex gap-2">
            <a href="{{ route('admin.import.siswa.form') }}" class="btn btn-outline-success btn-sm"><i
                    class="bi bi-upload"></i> Import CSV</a>
            <a href="{{ route('admin.siswa.create') }}" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i>
                Tambah</a>
        </div>
    </div>

    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama Siswa</th>
                        <th>Jurusan</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($siswas as $s)
                        <tr>
                            <td>{{ $siswas->firstItem() + $loop->index }}</td>
                            <td>{{ $s->nama }}</td>
                            <td>{{ $s->jurusan->kode }}</td>
                            <td class="text-end">
                                <a href="{{ route('admin.siswa.edit', $s) }}" class="btn btn-sm btn-warning">Edit</a>
                                <form method="POST" action="{{ route('admin.siswa.destroy', $s) }}" class="d-inline"
                                    onsubmit="return confirm('Hapus siswa ini beserta nilainya?')">
                                    @csrf @method('DELETE')
                                    <button class="btn btn-sm btn-danger">Hapus</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Data siswa tidak ditemukan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $siswas->links() }}</div>
@endsection