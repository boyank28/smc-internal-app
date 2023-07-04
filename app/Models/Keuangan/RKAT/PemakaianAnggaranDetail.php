<?php

namespace App\Models\Keuangan\RKAT;

use App\Support\Traits\Eloquent\Searchable;
use App\Support\Traits\Eloquent\Sortable;
use Illuminate\Database\Eloquent\Model;

class PemakaianAnggaranDetail extends Model
{
    use Sortable, Searchable;

    /**
     * The connection name for the model.
     *
     * @var ?string
     */
    protected $connection = 'mysql_smc';

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'pemakaian_anggaran_detail';

    /**
     * The primary key for the model.
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * The "type" of the primary key ID.
     *
     * @var string
     */
    protected $keyType = 'int';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = true;

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    /**
     * The number of models to return for pagination.
     *
     * @var int
     */
    protected $perPage = 25;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'nama',
        'deskripsi',
        'nominal',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'nominal' => 'float',
    ];

    /** 
     * @var string[]
     */
    protected $searchColumns = [
        'nama',
        'deskripsi',
    ];
}