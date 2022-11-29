<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Satuan extends Model
{
    protected $primaryKey = 'kode_sat';

    protected $keyType = 'string';

    protected $table = 'kodesatuan';

    public $incrementing = false;

    public $timestamps = false;
}
