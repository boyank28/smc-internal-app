<?php

namespace App\Models\Perawatan;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Poliklinik extends Model
{
    protected $primaryKey = 'kd_poli';

    protected $keyType = 'string';

    protected $table = 'poliklinik';

    public $incrementing = false;

    public $timestamps = false;

    public function registrasi(): HasMany
    {
        return $this->hasMany(Registrasi::class, 'kd_poli', 'kd_poli');
    }
}