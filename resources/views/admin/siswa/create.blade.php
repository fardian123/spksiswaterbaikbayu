@extends('layouts.app')
@section('title', 'Tambah Siswa')
@section('content')
<div class="card" style="max-width:800px"><div class="card-body">
    <form method="POST" action="{{ route('admin.siswa.store') }}">
        @csrf
        @include('admin.siswa._form')
    </form>
</div></div>
@endsection