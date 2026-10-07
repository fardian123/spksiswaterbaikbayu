<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportCsvRequest;
use App\Imports\NilaiImport;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class NilaiImportController extends Controller
{
    public function form()
    {
        return view('admin.import.nilai', [
            'periodes' => Periode::orderByDesc('id')->get(),
            'aktif'    => Periode::aktif()->first(),
        ]);
    }

    public function store(ImportCsvRequest $request)
    {
        $periode = Periode::findOrFail($request->periode_id);
        $import = new NilaiImport($periode, $request->delimiter());

        DB::transaction(fn () => Excel::import($import, $request->file('file')));

        $errors = $import->failures()->map(fn ($f) => [
            'baris'  => $f->row(),
            'alasan' => implode(', ', $f->errors()),
        ])->values()->all();

        return redirect()->route('admin.import.nilai.form')
            ->with('success', "Nilai {$import->berhasil} siswa berhasil diimpor ke periode {$periode->nama}.")
            ->with('import_errors', $errors);
    }

    public function template()
    {
        $csv = "nis,nilai_akademik,kehadiran,sikap,keaktifan\n"
             . "2024001,85,95,90,80\n";

        return response()->streamDownload(fn () => print($csv), 'template_nilai.csv', [
            'Content-Type' => 'text/csv',
        ]);
    }
}