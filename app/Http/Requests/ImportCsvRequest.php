<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ImportCsvRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }


    public function rules(): array
    {
        return [
            'file' => ['required', 'file', 'mimes:csv,txt,xlsx', 'max:2048'],
            'periode_id' => ['required', 'exists:periodes,id'],
        ];
    }


    /** Deteksi pemisah CSV: ',' atau ';' (Excel Indonesia sering memakai ';'). */
    public function delimiter(): string
    {
        $handle = fopen($this->file('file')->getRealPath(), 'r');
        $baris = $handle ? (fgets($handle) ?: '') : '';
        if ($handle) {
            fclose($handle);
        }
        return substr_count($baris, ';') > substr_count($baris, ',') ? ';' : ',';
    }
}
