<?php

namespace App\Http\Requests;
use App\Services\AhpService;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;


class AhpPerbandinganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'perbandingan'     => ['required', 'array'],
            'perbandingan.*'   => ['array'],
            'perbandingan.*.*' => ['required', Rule::in(AhpService::SKALA_OPSI)],
        ];
    }

    public function messages(): array
    {
        return [
            'perbandingan.*.*.in'       => 'Nilai perbandingan harus berasal dari skala Saaty (1-9 atau 1/2-1/9).',
            'perbandingan.*.*.required' => 'Semua pasangan kriteria harus diisi.',
        ];
    }

    // Pastikan SEMUA pasangan (i<j) terisi
    public function withValidator(Validator $validator): void
    {
        $validator->after(function (Validator $v) {
            $ids = app(AhpService::class)->getKriterias()->pluck('id')->values()->all();
            $input = $this->input('perbandingan', []);

            for ($i = 0; $i < count($ids); $i++) {
                for ($j = $i + 1; $j < count($ids); $j++) {
                    if (!isset($input[$ids[$i]][$ids[$j]])) {
                        $v->errors()->add('perbandingan', 'Masih ada pasangan kriteria yang belum diisi.');
                        return;
                    }
                }
            }
        });
    }
}