@php
    $menu = [
        ['Dashboard',        'bi-speedometer2',  'admin.dashboard',         'admin.dashboard'],
        ['Jurusan',          'bi-diagram-3',     'admin.jurusan.index',     'admin.jurusan.*'],
        ['Periode',          'bi-calendar-event','admin.periode.index',     'admin.periode.*'],
        ['Data Siswa',       'bi-people',        'admin.siswa.index',       'admin.siswa.*'],
        ['Nilai Siswa',      'bi-pencil-square', 'admin.nilai.index',       'admin.nilai.*'],
        ['Import CSV',       'bi-upload',        'admin.import.siswa.form', 'admin.import.*'],
        ['Kriteria & Bobot', 'bi-sliders',       'admin.kriteria.index',    'admin.kriteria.*'],
        ['Perhitungan AHP',  'bi-calculator',    'admin.ahp.index',         'admin.ahp.*'],
        ['Perhitungan SAW',  'bi-graph-up',      'admin.saw.index',         'admin.saw.*'],
        ['Ranking',          'bi-trophy',        'admin.ranking.umum',      'admin.ranking.*'],
    ];
@endphp

<div class="sidebar-wrap">
    <aside class="sidebar offcanvas-md offcanvas-start" tabindex="-1" id="sidebarMenu" aria-labelledby="sidebarMenuLabel">
        <div class="offcanvas-header d-md-none border-bottom border-secondary">
            <span class="text-white fw-bold" id="sidebarMenuLabel">
                <i class="bi bi-mortarboard-fill"></i> SPK Siswa Terbaik
            </span>
            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="offcanvas"
                    data-bs-target="#sidebarMenu" aria-label="Tutup"></button>
        </div>
        <div class="offcanvas-body d-block p-3">
            <a href="{{ route('admin.dashboard') }}"
               class="d-none d-md-block text-white fw-bold fs-5 text-decoration-none mb-3">
                <i class="bi bi-mortarboard-fill"></i> SPK Siswa Terbaik
            </a>
            <ul class="nav nav-pills flex-column gap-1">
                @foreach ($menu as [$label, $ikon, $rute, $pola])
                    <li class="nav-item">
                        <a href="{{ route($rute) }}" class="nav-link {{ request()->routeIs($pola) ? 'active' : '' }}">
                            <i class="bi {{ $ikon }} me-2"></i>{{ $label }}
                        </a>
                    </li>
                @endforeach
                <li class="nav-item mt-2">
                    <a href="{{ route('public.cek') }}" target="_blank" class="nav-link">
                        <i class="bi bi-box-arrow-up-right me-2"></i>Halaman Publik
                    </a>
                </li>
            </ul>
        </div>
    </aside>
</div>