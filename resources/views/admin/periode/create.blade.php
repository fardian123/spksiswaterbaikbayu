@extends('layouts.app')
@section('title', 'Tambah Periode')
@section('content')
    <div class="card" style="max-width:600px">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.periode.store') }}">
                @csrf
                @include('admin.periode._form')
            </form>
        </div>
    </div>
@endsection