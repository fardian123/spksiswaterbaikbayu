<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\ImportCsvRequest;
use App\Imports\SiswaImport;
use App\Models\Periode;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use RuntimeException;


class SiswaImportController extends Controller
{
    public function form()
    {
        return view('admin.import.siswa', [
            'periodes' => Periode::orderByDesc('id')->get(),
            'aktif' => Periode::aktif()->first(),
        ]);
    }

    public function store(ImportCsvRequest $request)
    {
        $periode = Periode::findOrFail($request->periode_id);

        try {
            $import = new SiswaImport($periode, $request->delimiter());
            DB::transaction(fn() => Excel::import($import, $request->file('file')));
        } catch (RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        $errors = $import->failures()->map(fn($f) => [
            'baris' => $f->row(),
            'alasan' => implode(', ', $f->errors()),
        ])->values()->all();

        $pesan = "{$import->berhasil} siswa beserta nilainya berhasil diimpor ke periode {$periode->nama}.";
        if (!empty($import->jurusanBaru)) {
            $pesan .= ' Jurusan baru dibuat: ' . implode(', ', array_unique($import->jurusanBaru)) . '.';
        }

        return redirect()->route('admin.import.siswa.form')
            ->with('success', $pesan)
            ->with('import_errors', $errors);
    }

    public function template()
    {
        $csv = "Jurusan;Nama Siswa;Akademik;Kehadiran;Sikap;Keaktifan\n"
            . "TKJ;ALI PASYA PUTERA NUGROHO;90,32;91;89;97\n";

        return response()->streamDownload(function () use ($csv) {
            echo $csv;
        }, 'template_siswa_nilai.csv', ['Content-Type' => 'text/csv']);
    }
}
