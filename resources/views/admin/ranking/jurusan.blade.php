@extends('layouts.app')
@section('title', 'Ranking Jurusan ' . $jurusan->kode)

@section('content')
    <div class="d-flex flex-wrap justify-content-between gap-2 mb-3">
        <form method="GET" class="d-flex gap-2">
            <select name="periode_id" class="form-select" onchange="this.form.submit()">
                @foreach ($periodes as $p)
                    <option value="{{ $p->id }}" @selected($periode?->id == $p->id)>{{ $p->nama }}</option>
                @endforeach
            </select>
        </form>
        @if ($periode)
            <a href="{{ route('admin.ranking.export', ['periode_id' => $periode->id, 'jurusan_id' => $jurusan->id]) }}"
                class="btn btn-outline-success btn-sm"><i class="bi bi-file-earmark-excel"></i> Export Excel</a>
        @endif
    </div>

    <ul class="nav nav-tabs mb-3">
        <li class="nav-item">
            <a class="nav-link" href="{{ route('admin.ranking.umum', ['periode_id' => $periode?->id]) }}">Ranking Umum</a>
        </li>
        @foreach ($jurusans as $j)
            <li class="nav-item">
                <a class="nav-link {{ $j->id === $jurusan->id ? 'active' : '' }}"
                    href="{{ route('admin.ranking.jurusan', [$j, 'periode_id' => $periode?->id]) }}">{{ $j->kode }}</a>
            </li>
        @endforeach
    </ul>

    <h6 class="mb-3">{{ $jurusan->nama }}</h6>
    @include('admin.ranking._tabel', ['rankings' => $rankings, 'mode' => 'jurusan'])
@endsection