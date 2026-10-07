@extends('layouts.app')
@section('title', 'Edit Jurusan')
@section('content')
    <div class="card" style="max-width:600px">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.jurusan.update', $jurusan) }}">
                @csrf @method('PUT')
                @include('admin.jurusan._form')
            </form>
        </div>
    </div>
@endsection