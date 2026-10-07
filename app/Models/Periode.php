<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
class Periode extends Model
{
    use HasFactory;

    protected $fillable = [
        'nama',
        'tahun_ajaran',
        'semester',
        'is_aktif',
    ];

    protected $casts = [
        'is_aktif' => 'boolean',
    ];

    /**
     * Periode memiliki banyak nilai.
     */
    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Periode memiliki banyak hasil ranking.
     */
    public function hasilRankings(): HasMany
    {
        return $this->hasMany(HasilRanking::class);
    }

    /**
     * Scope untuk mengambil periode yang aktif.
     *
     * Penggunaan:
     * Periode::aktif()->first();
     */
    public function scopeAktif(Builder $query): Builder
    {
        return $query->where('is_aktif', true);
    }
}
