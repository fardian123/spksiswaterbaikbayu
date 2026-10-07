@extends('layouts.app')
@section('title', 'Edit Kriteria')

@section('content')
    <div class="card" style="max-width:600px">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.kriteria.update', $kriteria) }}">
                @csrf @method('PUT')
                <div class="mb-3">
                    <label class="form-label">Kode</label>
                    <input type="text" value="{{ $kriteria->kode }}" class="form-control" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Kriteria</label>
                    <input type="text" name="nama" value="{{ old('nama', $kriteria->nama) }}" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-select" required>
                        @foreach (['benefit' => 'Benefit (semakin besar semakin baik)', 'cost' => 'Cost (semakin kecil semakin baik)'] as $v => $l)
                            <option value="{{ $v }}" @selected(old('tipe', $kriteria->tipe) === $v)>{{ $l }}</option>
                        @endforeach
                    </select>
                </div>
                <button class="btn btn-primary">Simpan</button>
                <a href="{{ route('admin.kriteria.index') }}" class="btn btn-secondary">Batal</a>
            </form>
        </div>
    </div>
@endsection