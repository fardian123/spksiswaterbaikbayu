<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class SiswaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }
    protected function prepareForValidation(): void
    {
        if ($this->has('nama')) {
            $this->merge([
                'nama' => preg_replace('/\s+/u', ' ', trim((string) $this->input('nama'))),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'nama' => [
                'required',
                'string',
                'max:150',
                Rule::unique('siswas', 'nama')
                    ->where('jurusan_id', $this->input('jurusan_id'))
                    ->ignore($this->route('siswa')),
            ],
            'jurusan_id' => ['required', 'exists:jurusans,id'],
            'periode_id' => ['nullable', 'exists:periodes,id'],
            'nilai' => ['nullable', 'array'],
            'nilai.*' => ['nullable', 'numeric', 'between:0,100'],
        ];
    }
    public function messages(): array
    {
        return [
            'nama.unique' => 'Siswa dengan nama tersebut sudah ada di jurusan ini.',
            'nilai.*.numeric' => 'Nilai harus berupa angka.',
            'nilai.*.between' => 'Nilai harus antara 0 sampai 100.',
        ];
    }
}