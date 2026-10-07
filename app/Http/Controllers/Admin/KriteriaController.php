<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\KriteriaRequest;
use App\Models\AhpHasil;
use App\Models\Kriteria;
class KriteriaController extends Controller
{
    public function index()
    {
        return view('admin.kriteria.index', [
            'kriterias' => Kriteria::orderBy('urutan')->get(),
            'ahpAktif'  => AhpHasil::aktif()->first(),
        ]);
    }

    public function edit(Kriteria $kriteria)
    {
        return view('admin.kriteria.edit', compact('kriteria'));
    }

    // Bobot TIDAK diubah manual; hanya lewat AHP.
    public function update(KriteriaRequest $request, Kriteria $kriteria)
    {
        $kriteria->update($request->validated());
        return redirect()->route('admin.kriteria.index')->with('success', 'Kriteria berhasil diperbarui.');
    }
}
