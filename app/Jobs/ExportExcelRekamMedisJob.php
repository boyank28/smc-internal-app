<?php

namespace App\Jobs;

use App\Exports\RekamMedisExport;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ExportExcelRekamMedisJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $periodeAwal;
    private $periodeAkhir;
    private $timestamp;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($periodeAwal = null, $periodeAkhir = null, $timestamp = null)
    {
        $this->periodeAwal = $periodeAwal ?? now()->startOfMonth()->format('Y-m-d');
        $this->periodeAkhir = $periodeAkhir ?? now()->endOfMonth()->format('Y-m-d');

        $this->timestamp = $timestamp ?? now()->format('Ymd_His');
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        (new RekamMedisExport($this->periodeAwal, $this->periodeAkhir, $this->timestamp))
            ->store("excel/{$this->timestamp}_rekammedis.xlsx", 'public');
    }
}