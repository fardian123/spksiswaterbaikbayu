<div class="mb-3">
    <label class="form-label">Kode</label>
    <input type="text" name="kode" value="{{ old('kode', $jurusan->kode ?? '') }}" class="form-control" maxlength="10"
        required>
</div>
<div class="mb-3">
    <label class="form-label">Nama Jurusan</label>
    <input type="text" name="nama" value="{{ old('nama', $jurusan->nama ?? '') }}" class="form-control" required>
</div>
<button class="btn btn-primary">Simpan</button>
<a href="{{ route('admin.jurusan.index') }}" class="btn btn-secondary">Batal</a>