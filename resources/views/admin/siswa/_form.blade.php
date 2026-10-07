<div class="row g-3">
    <div class="col-md-8">
        <label class="form-label">Nama Siswa</label>
        <input type="text" name="nama" value="{{ old('nama', $siswa->nama ?? '') }}" class="form-control" required>
    </div>
    <div class="col-md-4">
        <label class="form-label">Jurusan</label>
        <select name="jurusan_id" class="form-select" required>
            <option value="">- pilih -</option>
            @foreach ($jurusans as $j)
                <option value="{{ $j->id }}" @selected(old('jurusan_id', $siswa->jurusan_id ?? '') == $j->id)>
                    {{ $j->kode }} - {{ $j->nama }}
                </option>
            @endforeach
        </select>
    </div>
</div>

<hr class="my-4">

<div class="d-flex flex-wrap justify-content-between align-items-center mb-2">
    <h6 class="mb-0">Nilai Kriteria</h6>
    @if (isset($siswa))
        <input type="hidden" name="periode_id" value="{{ $periode?->id }}">
        <span class="text-muted small">Periode: {{ $periode?->nama ?? '-' }}</span>
    @else
        <select name="periode_id" class="form-select form-select-sm" style="width:auto">
            <option value="">- tidak isi nilai sekarang -</option>
            @foreach ($periodes as $p)
                <option value="{{ $p->id }}" @selected(old('periode_id', $periode?->id) == $p->id)>{{ $p->nama }}</option>
            @endforeach
        </select>
    @endif
</div>

<div class="row g-3">
    @foreach ($kriterias as $k)
        <div class="col-6 col-md-3">
            <label class="form-label">{{ $k->nama }}</label>
            <input type="number" step="0.01" min="0" max="100" name="nilai[{{ $k->id }}]"
                value="{{ old('nilai.' . $k->id, $nilaiLama[$k->id] ?? '') }}" class="form-control">
        </div>
    @endforeach
</div>
<div class="form-text">Skala 0-100. Kosongkan jika nilai diisi nanti lewat menu Nilai Siswa / Import CSV.</div>

<div class="mt-4">
    <button class="btn btn-primary">Simpan</button>
    <a href="{{ route('admin.siswa.index') }}" class="btn btn-secondary">Batal</a>
</div>