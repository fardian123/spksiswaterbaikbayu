<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Kriteria extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
        'tipe',
        'bobot',
        'urutan',
    ];

    /**
     * Kriteria memiliki banyak nilai.
     */
    public function nilais(): HasMany
    {
        return $this->hasMany(Nilai::class);
    }

    /**
     * Kriteria sebagai kriteria pertama dalam perbandingan AHP.
     */
    public function ahpPerbandinganSebagaiKriteria1(): HasMany
    {
        return $this->hasMany(
            AhpPerbandingan::class,
            'kriteria_1_id'
        );
    }

    /**
     * Kriteria sebagai kriteria kedua dalam perbandingan AHP.
     */
    public function ahpPerbandinganSebagaiKriteria2(): HasMany
    {
        return $this->hasMany(
            AhpPerbandingan::class,
            'kriteria_2_id'
        );
    }
}
