<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\Jurusan;

use Illuminate\Validation\Rule;

class JurusanController extends Controller
{
    public function index()
    {
        $jurusans = Jurusan::withCount('siswas')->orderBy('kode')->get();
        return view('admin.jurusan.index', compact('jurusans'));
    }

    public function create()
    {
        return view('admin.jurusan.create');
    }

    public function store(Request $request)
    {
        Jurusan::create($this->validated($request));
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil ditambahkan.');
    }

    public function edit(Jurusan $jurusan)
    {
        return view('admin.jurusan.edit', compact('jurusan'));
    }

    public function update(Request $request, Jurusan $jurusan)
    {
        $jurusan->update($this->validated($request, $jurusan));
        return redirect()->route('admin.jurusan.index')->with('success', 'Jurusan berhasil diperbarui.');
    }

    public function destroy(Jurusan $jurusan)
    {
        if ($jurusan->siswas()->exists()) {
            return back()->with('error', 'Jurusan tidak dapat dihapus karena masih memiliki siswa.');
        }
        $jurusan->delete();
        return back()->with('success', 'Jurusan berhasil dihapus.');
    }

    private function validated(Request $request, ?Jurusan $jurusan = null): array
    {
        $data = $request->validate([
            'kode' => ['required', 'string', 'max:10', Rule::unique('jurusans', 'kode')->ignore($jurusan)],
            'nama' => ['required', 'string', 'max:100'],
        ]);
        $data['kode'] = strtoupper($data['kode']);
        return $data;
    }
}
