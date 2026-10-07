@extends('layouts.guest')
@section('title', 'Login Guru')

@section('content')
    <div class="card shadow-sm">
        <div class="card-body p-4">
            <h4 class="mb-1">Login Guru</h4>
            <p class="text-muted">SPK Pemilihan Siswa Terbaik</p>

            <form method="POST" action="{{ route('login.proses') }}">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="form-control" required autofocus>
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control" required>
                </div>
                <div class="form-check mb-3">
                    <input type="checkbox" name="remember" id="remember" class="form-check-input">
                    <label for="remember" class="form-check-label">Ingat saya</label>
                </div>
                <button class="btn btn-primary w-100">Masuk</button>
            </form>
            <div class="text-center mt-3">
                <a href="{{ route('public.cek') }}" class="small">Cek ranking siswa (tanpa login)</a>
            </div>
        </div>
    </div>
@endsection