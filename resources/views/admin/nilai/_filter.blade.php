<form method="GET" action="{{ $action }}" class="row g-2 mb-3">
    <div class="col-md-4">
        <select name="periode_id" class="form-select">
            @foreach ($periodes as $p)
                <option value="{{ $p->id }}" @selected($periode?->id == $p->id)>
                    {{ $p->nama }} {{ $p->is_aktif ? '(aktif)' : '' }}
                </option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <select name="jurusan_id" class="form-select">
            <option value="">Semua jurusan</option>
            @foreach ($jurusans as $j)
                <option value="{{ $j->id }}" @selected(request('jurusan_id') == $j->id)>{{ $j->kode }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-3">
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Cari nama">
    </div>
    <div class="col-md-2">
        <button class="btn btn-outline-primary w-100"><i class="bi bi-funnel"></i> Filter</button>
    </div>
</form>