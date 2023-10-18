<?php

namespace App\Livewire\Concerns;

trait WithDateRange
{
    /** @var \Carbon\Carbon|string */
    public $tglAwal;

    /** @var \Carbon\Carbon|string */
    public $tglAkhir;

    protected function queryStringWithDateRange(): array
    {
        return [
            'tglAwal' => ['except' => now()->startOfMonth()->format('Y-m-d'), 'as' => 'tgl_awal'],
            'tglAkhir' => ['except' => now()->endOfMonth()->format('Y-m-d'), 'as' => 'tgl_akhir'],
        ];
    }

    public function mountWithDateRange(): void
    {
        $this->defaultValueWithDateRange();
    }

    protected function defaultValueWithDateRange(): void
    {
        $this->tglAwal = now()->startOfMonth()->format('Y-m-d');
        $this->tglAkhir = now()->endOfMonth()->format('Y-m-d');
    }
}
