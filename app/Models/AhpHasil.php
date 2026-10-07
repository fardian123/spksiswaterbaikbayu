<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AhpHasil extends Model
{
    use HasFactory;

    protected $fillable = [
        'lambda_max',
        'ci',
        'ri',
        'cr',
        'is_konsisten',
        'detail',
        'is_aktif',
    ];

    protected $casts = [
        'detail' => 'array',
        'is_konsisten' => 'boolean',
        'is_aktif' => 'boolean',
    ];

    /**
     * AHP Hasil memiliki banyak hasil ranking.
     */
    public function hasilRankings(): HasMany
    {
        return $this->hasMany(HasilRanking::class);
    }

    /**
     * Scope untuk mengambil hasil AHP yang aktif.
     *
     * Penggunaan:
     * AhpHasil::aktif()->first();
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }

}
