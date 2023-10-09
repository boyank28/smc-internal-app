<?php

namespace App\Livewire\Pages\Farmasi;

use App\Models\Farmasi\ResepObat;
use App\Livewire\Concerns\ExcelExportable;
use App\Livewire\Concerns\Filterable;
use App\Livewire\Concerns\FlashComponent;
use App\Livewire\Concerns\LiveTable;
use App\Livewire\Concerns\MenuTracker;
use App\View\Components\BaseLayout;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Casts\AsStringable;
use Illuminate\View\View;
use Livewire\Component;

class ObatPerDokter extends Component
{
    use FlashComponent, Filterable, ExcelExportable, LiveTable, MenuTracker;

    /** @var string */
    public $tglAwal;

    /** @var string */
    public $tglAkhir;

    protected function queryString(): array
    {
        return [
            'tglAwal'  => ['except' => now()->startOfMonth()->format('Y-m-d'), 'as' => 'tgl_awal'],
            'tglAkhir' => ['except' => now()->endOfMonth()->format('Y-m-d'), 'as' => 'tgl_akhir'],
        ];
    }

    public function mount(): void
    {
        $this->defaultValues();
    }

    public function getObatPerDokterProperty(): Paginator
    {
        return ResepObat::query()
            ->penggunaanObatPerDokter($this->tglAwal, $this->tglAkhir)
            ->search($this->cari, [
                'resep_obat.no_resep',
                'databarang.nama_brng',
                'kategori_barang.nama',
                'dokter.kd_dokter',
                'dokter.nm_dokter',
                'resep_obat.status',
                'poliklinik.nm_poli',
                'reg_periksa.kd_pj',
                'penjab.png_jawab',
            ])
            ->sortWithColumns($this->sortColumns)
            ->paginate($this->perpage);
    }

    public function render(): View
    {
        return view('livewire.pages.farmasi.obat-per-dokter')
            ->layout(BaseLayout::class, ['title' => 'Penggunaan Obat Per Dokter Peresep']);
    }

    protected function defaultValues(): void
    {
        $this->cari = '';
        $this->perpage = 25;
        $this->sortColumns = [];
        $this->tglAwal = now()->startOfMonth()->format('Y-m-d');
        $this->tglAkhir = now()->endOfMonth()->format('Y-m-d');
    }

    protected function dataPerSheet(): array
    {
        return [
            ResepObat::query()
                ->penggunaanObatPerDokter($this->tglAwal, $this->tglAkhir)
                ->withCasts([
                    'status' => AsStringable::class,
                ])
                ->get()
                ->map(fn (ResepObat $model): array => [
                    'no_resep'      => $model->no_resep,
                    'tgl_perawatan' => $model->tgl_perawatan,
                    'jam'           => $model->jam,
                    'nama_brng'     => $model->nama_brng,
                    'nama'          => $model->nama,
                    'jml'           => floatval($model->jml),
                    'nm_dokter'     => $model->nm_dokter,
                    'status'        => (string) $model->status->title(),
                    'nm_poli'       => $model->nm_poli,
                    'png_jawab'     => $model->png_jawab,
                ]),
        ];
    }

    protected function columnHeaders(): array
    {
        return [
            'No. Resep',
            'Tgl. Validasi',
            'Jam',
            'Nama Obat',
            'Kategori',
            'Jumlah',
            'Dokter Peresep',
            'Jenis Perawatan',
            'Asal Poli',
            'Jenis Bayar',
        ];
    }

    protected function pageHeaders(): array
    {
        $periodeAwal = carbon($this->tglAwal);
        $periodeAkhir = carbon($this->tglAkhir);

        $periode = 'Periode ' . $periodeAwal->translatedFormat('d F Y') . ' s.d. ' . $periodeAkhir->translatedFormat('d F Y');

        if ($periodeAwal->isSameDay($periodeAkhir)) {
            $periode = $periodeAwal->translatedFormat('d F Y');
        }

        return [
            'RS Samarinda Medika Citra',
            'Laporan Penggunaan Obat Per Dokter Peresep',
            now()->translatedFormat('d F Y'),
            $periode,
        ];
    }
}