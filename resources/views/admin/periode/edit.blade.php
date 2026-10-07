@extends('layouts.app')
@section('title', 'Edit Periode')
@section('content')
    <div class="card" style="max-width:600px">
        <div class="card-body">
            <form method="POST" action="{{ route('admin.periode.update', $periode) }}">
                @csrf @method('PUT')
                @include('admin.periode._form')
            </form>
        </div>
    </div>
@endsection