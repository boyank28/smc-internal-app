<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Petugas extends Model
{
    protected $primaryKey = 'nip';

    protected $keyType = 'string';

    protected $table = 'petugas';

    public $incrementing = false;

    public $timestamps = false;
}