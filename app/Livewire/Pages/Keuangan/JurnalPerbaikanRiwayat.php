<?php

namespace App\Livewire\Pages\Keuangan;

use App\Models\Keuangan\Jurnal\JurnalBackup;
use App\Livewire\Concerns\DeferredLoading;
use App\Livewire\Concerns\ExcelExportable;
use App\Livewire\Concerns\Filterable;
use App\Livewire\Concerns\FlashComponent;
use App\Livewire\Concerns\LiveTable;
use App\Livewire\Concerns\MenuTracker;
use App\View\Components\BaseLayout;
use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\View\View;
use Livewire\Component;

class JurnalPerbaikanRiwayat extends Component
{
    use FlashComponent, Filterable, ExcelExportable, LiveTable, MenuTracker, DeferredLoading;

    /** @var string */
    public $tglAwal;

    /** @var string */
    public $tglAkhir;

    protected function queryString(): array
    {
        return [
            'tglAwal' => ['except' => now()->startOfMonth()->format('Y-m-d'), 'as' => 'tgl_awal'],
            'tglAkhir' => ['except' => now()->endOfMonth()->format('Y-m-d'), 'as' => 'tgl_akhir'],
        ];
    }

    public function mount(): void
    {
        $this->defaultValues();
    }

    public function getDataRiwayatJurnalPerbaikanProperty(): Paginator
    {
        return JurnalBackup::query()
            ->with([
                'pegawai',
                'jurnal' => fn (BelongsTo $query): BelongsTo => $query
                    ->withSum('detail as total_debet', 'debet')
                    ->withSum('detail as total_kredit', 'kredit'),
            ])
            ->whereBetween('tgl_jurnal_diubah', [$this->tglAwal, $this->tglAkhir])
            ->paginate($this->perpage);
    }

    public function render(): View
    {
        return view('livewire.pages.keuangan.jurnal-perbaikan-riwayat')
            ->layout(BaseLayout::class, ['title' => 'Riwayat Jurnal Perbaikan']);
    }

    protected function defaultValues(): void
    {
        $this->tglAwal = now()->startOfMonth()->format('Y-m-d');
        $this->tglAkhir = now()->endOfMonth()->format('Y-m-d');
    }

    protected function dataPerSheet(): array
    {
        return [
            //
        ];
    }

    protected function columnHeaders(): array
    {
        return [
            //
        ];
    }

    protected function pageHeaders(): array
    {
        return [
            //
        ];
    }
}