<?php

namespace App\Http\Livewire;

use App\Exports\Farmasi\PenggunaanObatPerdokterExport;
use App\Jobs\ExportPenggunaanObatPerdokterJob;
use App\Models\Farmasi\Resep;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithPagination;
use Vtiful\Kernel\Excel;

class PenggunaanObatPerDokterPeresep extends Component
{
    use WithPagination;

    public $periodeAwal;

    public $periodeAkhir;

    public $cari;

    public $perpage;

    public $timestamp;

    protected $paginationTheme = 'bootstrap';

    protected $listeners = [
        'refreshFilter' => '$refresh',
        'processExcelExport',
    ];
    
    protected function queryString()
    {
        return [
            'cari' => [
                'except' => '',
            ],
            'periodeAwal' => [
                'except' => now()->startOfMonth()->format('Y-m-d'),
                'as' => 'periode_awal',
            ],
            'periodeAkhir' => [
                'except' => now()->endOfMonth()->format('Y-m-d'),
                'as' => 'periode_akhir',
            ],
            'perpage' => [
                'except' => 25,
            ],
        ];
    }

    public function mount()
    {
        $this->periodeAwal = now()->startOfMonth()->format('Y-m-d');
        $this->periodeAkhir = now()->endOfMonth()->format('Y-m-d');
        $this->perpage = 25;
    }

    public function processExcelExport()
    {
        $this->timestamp = now()->format('Ymd_His');

        $filename = "excel/{$this->timestamp}_obat_perdokter.xlsx";

        $config = [
            'path' => storage_path('app/public'),
        ];

        $data = Resep::penggunaanObatPerDokter($this->periodeAwal, $this->periodeAkhir)
            ->get()
            ->toArray();

        (new Excel($config))
            ->fileName($filename)
            ->data($data)
            ->output();

        return Storage::disk('public')->download($filename);
    }

    public function exportToExcel()
    {
        session()->flash('excel.exporting', 'Proses ekspor laporan dimulai! Silahkan tunggu beberapa saat. Mohon untuk tidak menutup halaman agar proses ekspor dapat berlanjut.');

        $this->emit('processExcelExport');
    }

    public function render()
    {
        $obatPerdokter = Resep::penggunaanObatPerDokter(
            $this->periodeAwal,
            $this->periodeAkhir
        )->paginate($this->perpage);
        
        return view('livewire.penggunaan-obat-per-dokter-peresep', [
            'obatPerDokter' => $obatPerdokter,
        ]);
    }
}
