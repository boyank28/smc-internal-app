<?php

namespace App\Models\Perawatan;

use Illuminate\Database\Eloquent\Relations\Pivot;

class Diagnosa extends Pivot
{
    protected $table = 'diagnosa_pasien';

    public $pivotColumns = [
        'status',
        'prioritas',
        'status_penyakit',
    ];
}