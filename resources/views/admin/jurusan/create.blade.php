@extends('layouts.app')
@section('title', 'Tambah Jurusan')
@section('content')
    <div class="card" style="max-width:600px">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.jurusan.store') }}">
                @csrf
                @include('admin.jurusan._form')
            </form>
        </div>
    </div>
@endsection