<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PeriodeController extends Controller
{
    public function index()
    {
        $periodes = Periode::orderByDesc('id')->get();
        return view('admin.periode.index', compact('periodes'));
    }

    public function create()
    {
        return view('admin.periode.create');
    }

    public function store(Request $request)
    {
        Periode::create($this->validated($request) + ['is_aktif' => false]);
        return redirect()->route('admin.periode.index')->with('success', 'Periode berhasil ditambahkan.');
    }

    public function edit(Periode $periode)
    {
        return view('admin.periode.edit', compact('periode'));
    }

    public function update(Request $request, Periode $periode)
    {
        $periode->update($this->validated($request));
        return redirect()->route('admin.periode.index')->with('success', 'Periode berhasil diperbarui.');
    }

    public function destroy(Periode $periode)
    {
        // nilai & hasil ranking ikut terhapus (cascade)
        $periode->delete();
        return back()->with('success', 'Periode berhasil dihapus.');
    }

    public function aktifkan(Periode $periode)
    {
        DB::transaction(function () use ($periode) {
            Periode::where('is_aktif', true)->update(['is_aktif' => false]);
            $periode->update(['is_aktif' => true]);
        });
        return back()->with('success', "Periode {$periode->nama} diaktifkan.");
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'nama'         => ['required', 'string', 'max:100'],
            'tahun_ajaran' => ['required', 'string', 'max:20'],
            'semester'     => ['required', 'in:Ganjil,Genap'],
        ]);
    }
}