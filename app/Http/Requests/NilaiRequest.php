<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class NilaiRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'periode_id' => ['required', 'exists:periodes,id'],
            'nilai' => ['required', 'array'],
            'nilai.*' => ['array'],
            'nilai.*.*' => ['nullable', 'numeric', 'between:0,100'],
        ];

    }
    public function messages(): array
    {
        return [
            'nilai.*.*.numeric' => 'Nilai harus berupa angka.',
            'nilai.*.*.between' => 'Nilai harus antara 0 sampai 100.',
        ];
    }
}
