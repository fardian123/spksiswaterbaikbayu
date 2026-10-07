@if (session('import_errors') && count(session('import_errors')))
    <div class="card border-danger mt-3">
        <div class="card-header text-bg-danger">
            Baris yang gagal diimpor ({{ count(session('import_errors')) }})
        </div>
        <div class="table-responsive">
            <table class="table table-sm mb-0">
                <thead><tr><th style="width:100px">Baris</th><th>Alasan</th></tr></thead>
                <tbody>
                @foreach (session('import_errors') as $e)
                    <tr><td>{{ $e['baris'] }}</td><td>{{ $e['alasan'] }}</td></tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endif
