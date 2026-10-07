<div class="mb-3">
    <label class="form-label">Nama Periode</label>
    <input type="text" name="nama" value="{{ old('nama', $periode->nama ?? '') }}" class="form-control"
        placeholder="Semester Ganjil 2025/2026" required>
</div>
<div class="mb-3">
    <label class="form-label">Tahun Ajaran</label>
    <input type="text" name="tahun_ajaran" value="{{ old('tahun_ajaran', $periode->tahun_ajaran ?? '') }}"
        class="form-control" placeholder="2025/2026" required>
</div>
<div class="mb-3">
    <label class="form-label">Semester</label>
    <select name="semester" class="form-select" required>
        @foreach (['Ganjil', 'Genap'] as $s)
            <option value="{{ $s }}" @selected(old('semester', $periode->semester ?? '') === $s)>{{ $s }}</option>
        @endforeach
    </select>
</div>
<button class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.periode.index') }}" class="btn btn-secondary">Batal</a>