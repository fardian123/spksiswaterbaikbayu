<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Jurusan extends Model
{
    use HasFactory;

    protected $fillable = [
        'kode',
        'nama',
    ];

    /**
     * Jurusan memiliki banyak siswa.
     */
    public function siswas(): HasMany
    {
        return $this->hasMany(Siswa::class);
    }
}
