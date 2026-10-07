<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
class AhpPerbandingan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kriteria_1_id',
        'kriteria_2_id',
        'nilai',
    ];

    /**
     * Relasi ke kriteria pertama.
     */
    public function kriteria1(): BelongsTo
    {
        return $this->belongsTo(
            Kriteria::class,
            'kriteria_1_id'
        );
    }

    /**
     * Relasi ke kriteria kedua.
     */
    public function kriteria2(): BelongsTo
    {
        return $this->belongsTo(
            Kriteria::class,
            'kriteria_2_id'
        );
    }
}
