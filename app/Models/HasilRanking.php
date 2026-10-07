<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HasilRanking extends Model
{
    use HasFactory;

    protected $fillable = [
        'periode_id',
        'siswa_id',
        'ahp_hasil_id',
        'nilai_akhir',
        'ranking_umum',
        'ranking_jurusan',
        'detail',
    ];

    protected $casts = [
        'detail' => 'array',
    ];

    /**
     * Hasil ranking dimiliki oleh satu siswa.
     */
    public function siswa(): BelongsTo
    {
        return $this->belongsTo(Siswa::class);
    }

    /**
     * Hasil ranking dimiliki oleh satu periode.
     */
    public function periode(): BelongsTo
    {
        return $this->belongsTo(Periode::class);
    }

    /**
     * Hasil ranking menggunakan satu hasil AHP.
     */
    public function ahpHasil(): BelongsTo
    {
        return $this->belongsTo(AhpHasil::class);
    }

}
