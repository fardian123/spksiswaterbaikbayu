@extends('layouts.app')
@section('title', 'Edit Siswa')
@section('content')
<div class="card" style="max-width:800px"><div class="card-body">
    {{-- pilih periode nilai (GET, memuat ulang halaman) --}}
    <form method="GET" class="d-flex align-items-center gap-2 mb-3">
        <label class="small text-muted">Lihat nilai periode:</label>
        <select name="periode_id" class="form-select form-select-sm" style="width:auto" onchange="this.form.submit()">
            @foreach ($periodes as $p)
                <option value="{{ $p->id }}" @selected($periode?->id == $p->id)>{{ $p->nama }}</option>
            @endforeach
        </select>
    </form>

    <form method="POST" action="{{ route('admin.siswa.update', $siswa) }}">
        @csrf @method('PUT')
        @include('admin.siswa._form')
    </form>
</div></div>
@endsection