@if (is_null($rankings))
    <div class="alert alert-warning">Belum ada periode yang dipilih/aktif.</div>
@elseif ($rankings->isEmpty())
    <div class="alert alert-info">
        Belum ada hasil ranking. Jalankan <a href="{{ route('admin.saw.index') }}">perhitungan SAW</a> terlebih dahulu.
    </div>
@else
    <div class="card">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead>
                    <tr>
                        <th>{{ $mode === 'umum' ? 'Ranking Umum' : 'Ranking Jurusan' }}</th>
                        <th>Nama Siswa</th>
                        <th>Jurusan</th>
                        <th>{{ $mode === 'umum' ? 'Rank Jurusan' : 'Rank Umum' }}</th>
                        <th class="text-end">Nilai Akhir</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($rankings as $r)
                        @php $rank = $mode === 'umum' ? $r->ranking_umum : $r->ranking_jurusan; @endphp
                        <tr>
                            <td>
                                <span
                                    class="badge {{ $rank <= 3 ? 'text-bg-warning' : 'text-bg-light border' }} fs-6">{{ $rank }}</span>
                            </td>
                            <td>{{ $r->siswa->nama }}</td>
                            <td>{{ $r->siswa->jurusan->kode }}</td>
                            <td>{{ $mode === 'umum' ? $r->ranking_jurusan : $r->ranking_umum }}</td>
                            <td class="text-end">{{ number_format($r->nilai_akhir, 4, ',', '.') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-3">{{ $rankings->links() }}</div>
@endif