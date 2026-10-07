@extends('layouts.app')
@section('title', 'Import Nilai Siswa')

@section('content')
    <div class="card" style="max-width:700px">
        <div class="card-body">
            <p class="mb-1">Format kolom (baris pertama sebagai header):</p>
            <code>nis, nilai_akademik, kehadiran, sikap, keaktifan</code>
            <p class="small text-muted mt-2 mb-3">
                Nilai 0-100, boleh memakai koma desimal (84,55). NIS harus sudah terdaftar di data siswa.
                Nilai lama pada periode yang sama akan diperbarui.
            </p>
            <a href="{{ route('admin.import.nilai.template') }}" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="bi bi-download"></i> Unduh Template CSV
            </a>

            <form method="POST" action="{{ route('admin.import.nilai.store') }}" enctype="multipart/form-data">
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
                <div class="mb-3">
                    <label class="form-label">File CSV (maks 2 MB)</label>
                    <input type="file" name="file" class="form-control" accept=".csv,.txt" required>
                </div>
                <button class="btn btn-primary"><i class="bi bi-upload"></i> Import</button>
            </form>
        </div>
    </div>
    @include('admin.import._errors')
@endsection