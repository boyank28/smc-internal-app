<?php

namespace App\Models\Keuangan;

use App\Support\Traits\Eloquent\Searchable;
use App\Support\Traits\Eloquent\Sortable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class RekeningTahun extends Model
{
    use Sortable, Searchable;

    protected $primaryKey = false;

    protected $keyType = false;

    protected $table = 'rekeningtahun';

    public $incrementing = false;

    public $timestamps = false;
}