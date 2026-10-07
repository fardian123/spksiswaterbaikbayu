@extends('layouts.app')
@section('title', 'Import Siswa & Nilai (CSV)')

@section('content')
    <div class="card" style="max-width:700px">
        <div class="card-body">
            <p class="mb-1">Format kolom (baris pertama sebagai header):</p>
            <code>Jurusan; Nama Siswa; Akademik; Kehadiran; Sikap; Keaktifan</code>
            <ul class="small text-muted mt-2 mb-3">
                <li>Satu baris = satu siswa beserta 4 nilainya untuk periode yang dipilih.</li>
                <li>Pemisah titik koma (;) atau koma (,) dikenali otomatis. Gunakan titik koma jika nilai memakai koma
                    desimal (90,32).</li>
                <li>Kolom Jurusan berisi kode jurusan (mis. TKJ). Jika belum ada, jurusan dibuat otomatis.</li>
                <li>Siswa dengan nama dan jurusan yang sama akan diperbarui nilainya.</li>
                <li>Nilai harus 0-100.</li>
            </ul>
            <a href="{{ route('admin.import.siswa.template') }}" class="btn btn-outline-secondary btn-sm mb-3">
                <i class="bi bi-download"></i> Unduh Template CSV
            </a>

            <form method="POST" action="{{ route('admin.import.siswa.store') }}" enctype="multipart/form-data">
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
                    <label class="form-label">File CSV / XLSX (maks 2 MB)</label>
                    <input type="file" name="file" class="form-control" accept=".csv,.txt,.xlsx" required>
                </div>
                <button class="btn btn-primary"><i class="bi bi-upload"></i> Import</button>
            </form>
        </div>
    </div>
    @include('admin.import._errors')
@endsection