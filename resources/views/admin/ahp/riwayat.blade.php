@extends('layouts.app')
@section('title', 'Riwayat Perhitungan AHP')

@section('content')
    <a href="{{ route('admin.ahp.index') }}" class="btn btn-outline-secondary btn-sm mb-3"><i class="bi bi-arrow-left"></i>
        Kembali</a>
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Waktu</th>
                        <th>λ maks</th>
                        <th>CI</th>
                        <th>CR</th>
                        <th>Konsisten</th>
                        <th>Aktif</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($riwayat as $r)
                        <tr>
                            <td>{{ $r->id }}</td>
                            <td>{{ $r->created_at->format('d/m/Y H:i') }}</td>
                            <td>{{ number_format($r->lambda_max, 4, ',', '.') }}</td>
                            <td>{{ number_format($r->ci, 4, ',', '.') }}</td>
                            <td>{{ number_format($r->cr, 4, ',', '.') }}</td>
                            <td><span
                                    class="badge text-bg-{{ $r->is_konsisten ? 'success' : 'danger' }}">{{ $r->is_konsisten ? 'Ya' : 'Tidak' }}</span>
                            </td>
                            <td>@if ($r->is_aktif)<span class="badge text-bg-primary">Aktif</span>@endif</td>
                            <td class="text-end"><a href="{{ route('admin.ahp.hasil', $r) }}"
                                    class="btn btn-sm btn-outline-primary">Detail</a></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted">Belum ada riwayat.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $riwayat->links() }}</div>
@endsection