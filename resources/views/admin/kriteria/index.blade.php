@extends('layouts.app')
@section('title', 'Kriteria & Bobot')

@section('content')
    <div class="alert {{ $ahpAktif ? 'alert-success' : 'alert-warning' }}">
        @if ($ahpAktif)
            Bobot berasal dari hasil AHP #{{ $ahpAktif->id }} (CR {{ number_format($ahpAktif->cr, 4, ',', '.') }}, konsisten).
        @else
            Bobot belum ditentukan. Jalankan dan terapkan
            <a href="{{ route('admin.ahp.index') }}">perhitungan AHP</a> terlebih dahulu.
        @endif
    </div>
    <div class="card">
        <div class="table-responsive">
            <table class="table mb-0 align-middle">
                <thead>
                    <tr>
                        <th>Kode</th>
                        <th>Nama Kriteria</th>
                        <th>Tipe</th>
                        <th class="text-end">Bobot</th>
                        <th class="text-end">Persen</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($kriterias as $k)
                        <tr>
                            <td>{{ $k->kode }}</td>
                            <td>{{ $k->nama }}</td>
                            <td><span
                                    class="badge text-bg-{{ $k->tipe === 'benefit' ? 'primary' : 'secondary' }}">{{ ucfirst($k->tipe) }}</span>
                            </td>
                            <td class="text-end">{{ number_format($k->bobot, 4, ',', '.') }}</td>
                            <td class="text-end">{{ number_format($k->bobot * 100, 2, ',', '.') }}%</td>
                            <td class="text-end"><a href="{{ route('admin.kriteria.edit', $k) }}"
                                    class="btn btn-sm btn-warning">Edit</a></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot>
                    <tr class="fw-bold">
                        <td colspan="3">Total</td>
                        <td class="text-end">{{ number_format($kriterias->sum('bobot'), 4, ',', '.') }}</td>
                        <td></td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
    <div class="form-text mt-2">Bobot hanya dapat diubah melalui perhitungan AHP.</div>
@endsection