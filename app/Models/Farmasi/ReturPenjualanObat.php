<?php

namespace App\Models\Farmasi;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ReturPenjualanObat extends Model
{
    protected $primaryKey = 'no_retur_jual';

    protected $keyType = 'string';

    protected $table = 'returjual';

    public $incrementing = false;

    public $timestamps = false;

    public function detail(): BelongsToMany
    {
        return $this->belongsToMany(Obat::class, 'detreturjual', 'no_retur_jual', 'kode_brng');
    }

    public function scopeReturObatPasien(Builder $query, string $year = '2022'): Builder
    {
        return $query->selectRaw("
            round(sum(detreturjual.subtotal)) jumlah,
            month(returjual.tgl_retur) bulan
        ")
            ->join('detreturjual', 'returjual.no_retur_jual', '=', 'detreturjual.no_retur_jual')
            ->whereBetween('returjual.tgl_retur', ["{$year}-01-01", "{$year}-12-31"])
            ->groupByRaw('month(returjual.tgl_retur)');
    }

    public static function totalReturObat(string $year = '2022'): array
    {
        $data = (new static)::returObatPasien($year)->get()
            ->mapWithKeys(function ($value, $key) {
                return [$value->bulan => $value->jumlah];
            })->toArray();
        
        return map_bulan($data);
    }
}
