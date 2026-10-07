<?php

use Illuminate\Support\Facades\Route;

// Public
use App\Http\Controllers\Public\CekRankingController;

// Auth
use App\Http\Controllers\Auth\LoginController;

// Admin
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SiswaController;
use App\Http\Controllers\Admin\JurusanController;
use App\Http\Controllers\Admin\PeriodeController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\Admin\SiswaImportController;
use App\Http\Controllers\Admin\NilaiImportController;
use App\Http\Controllers\Admin\KriteriaController;
use App\Http\Controllers\Admin\AhpController;
use App\Http\Controllers\Admin\SawController;
use App\Http\Controllers\Admin\RankingController;
use App\Http\Controllers\Admin\HasilRankingController;



Route::get('/', [CekRankingController::class, 'index'])->name('public.cek');
Route::post('/cek-ranking', [CekRankingController::class, 'cek'])
    ->middleware('throttle:10,1')->name('public.cek.proses');


/*
|--------------------------------------------------------------------------
| AUTH
|--------------------------------------------------------------------------
| Use Case 1 - Login
|--------------------------------------------------------------------------
*/
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login'])
        ->middleware('throttle:5,1')->name('login.proses');
});
Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

// ---------- ADMIN ----------
Route::prefix('admin')->name('admin.')->middleware('auth')->group(function () {

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('jurusan', JurusanController::class)->except('show');
    Route::resource('periode', PeriodeController::class)->except('show');
    Route::post('periode/{periode}/aktifkan', [PeriodeController::class, 'aktifkan'])
        ->name('periode.aktifkan');
    Route::resource('siswa', SiswaController::class)->except('show');

    // Nilai
    Route::get('nilai', [NilaiController::class, 'index'])->name('nilai.index');
    Route::get('nilai/input', [NilaiController::class, 'create'])->name('nilai.create');
    Route::post('nilai', [NilaiController::class, 'store'])->name('nilai.store');
    Route::delete('nilai/{nilai}', [NilaiController::class, 'destroy'])->name('nilai.destroy');

    // Import CSV (siswa + nilai dalam satu file)
    Route::get('import/siswa', [SiswaImportController::class, 'form'])->name('import.siswa.form');
    Route::post('import/siswa', [SiswaImportController::class, 'store'])->name('import.siswa.store');
    Route::get('import/siswa/template', [SiswaImportController::class, 'template'])->name('import.siswa.template');

    // Kriteria
    Route::resource('kriteria', KriteriaController::class)->only(['index', 'edit', 'update']);

    // AHP
    Route::get('ahp', [AhpController::class, 'index'])->name('ahp.index');
    Route::post('ahp/hitung', [AhpController::class, 'hitung'])->name('ahp.hitung');
    Route::get('ahp/riwayat', [AhpController::class, 'riwayat'])->name('ahp.riwayat');
    Route::get('ahp/{ahpHasil}', [AhpController::class, 'hasil'])->whereNumber('ahpHasil')->name('ahp.hasil');
    Route::post('ahp/{ahpHasil}/terapkan', [AhpController::class, 'terapkan'])->name('ahp.terapkan');

    // SAW
    Route::get('saw', [SawController::class, 'index'])->name('saw.index');
    Route::post('saw/hitung', [SawController::class, 'hitung'])->name('saw.hitung');
    Route::get('saw/detail/{periode}', [SawController::class, 'detail'])->name('saw.detail');

    // Ranking
    Route::get('ranking', [RankingController::class, 'umum'])->name('ranking.umum');
    Route::get('ranking/export', [RankingController::class, 'export'])->name('ranking.export');
    Route::get('ranking/jurusan/{jurusan}', [RankingController::class, 'perJurusan'])->name('ranking.jurusan');
});
