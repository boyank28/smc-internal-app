<?php

namespace App\Http\Livewire\Keuangan;

use App\Models\Perawatan\RawatInap;
use App\Models\RekamMedis\Penjamin;
use App\Support\Traits\Livewire\DeferredLoading;
use App\Support\Traits\Livewire\ExcelExportable;
use App\Support\Traits\Livewire\Filterable;
use App\Support\Traits\Livewire\FlashComponent;
use App\Support\Traits\Livewire\LiveTable;
use App\Support\Traits\Livewire\MenuTracker;
use App\View\Components\BaseLayout;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Fluent;
use Illuminate\Support\Str;
use Livewire\Component;
use Livewire\WithPagination;

class DPJPPiutangRanap extends Component
{
    use WithPagination, FlashComponent, Filterable, ExcelExportable, LiveTable, MenuTracker, DeferredLoading;

    public $status;

    public $jenisBayar;

    public $periodeAwal;

    public $periodeAkhir;

    protected function queryString()
    {
        return [
            'status' => ['except' => ''],
            'jenisBayar' => ['except' => '', 'as' => 'kd_pj'],
            'periodeAwal' => ['except' => now()->startOfMonth()->format('Y-m-d'), 'as' => 'tgl_awal'],
            'periodeAkhir' => ['except' => now()->endOfMonth()->format('Y-m-d'), 'as' => 'tgl_akhir'],
        ];
    }

    public function mount()
    {
        $this->defaultValues();
    }

    public function getPenjaminProperty()
    {
        return Penjamin::query()
            ->where('status', '1')
            ->pluck('png_jawab', 'kd_pj');
    }


    public function getPiutangRanapProperty()
    {
        return $this->isDeferred
            ? []
            : RawatInap::query()
            ->piutangRanap($this->periodeAwal, $this->periodeAkhir, $this->status, $this->jenisBayar)
            ->with([
                'dpjpRanap',
                'billing' => fn ($q) => $q->totalBillingan(),
            ])
            ->withSum('cicilanPiutang as dibayar', 'besar_cicilan')
            ->sortWithColumns($this->sortColumns, [
                'perujuk' => DB::raw("ifnull(rujuk_masuk.perujuk, '-')"),
                'waktu_keluar' => DB::raw("timestamp(kamar_inap.tgl_keluar, kamar_inap.jam_keluar)"),
                'ruangan' => DB::raw("concat(kamar.kd_kamar, ' ', bangsal.nm_bangsal)"),
            ])
            ->paginate($this->perpage);
    }

    public function render()
    {
        return view('livewire.keuangan.dpjp-piutang-ranap')
            ->layout(BaseLayout::class, ['title' => 'DPJP Piutang Ranap']);
    }

    protected function defaultValues()
    {
        $this->status = '';
        $this->jenisBayar = '';
        $this->periodeAwal = now()->startOfMonth()->format('Y-m-d');
        $this->periodeAkhir = now()->endOfMonth()->format('Y-m-d');
    }

    public function mapData()
    {
        return RawatInap::query()
            ->piutangRanap($this->periodeAwal, $this->periodeAkhir, $this->status, $this->jenisBayar)
            ->with([
                'dpjpRanap',
                'billing' => fn ($q) => $q->totalBillingan(),
            ])
            ->withSum('cicilanPiutang as dibayar', 'besar_cicilan')
            ->get()
            ->map(function (RawatInap $ranap) {
                $kategoriBilling = $ranap
                    ->billing
                    ->pluck('total', 'status')
                    ->mapWithKeys(fn ($total, $status) => [Str::snake($status) => $total]);

                $billingTindakan = $kategoriBilling
                    ->only([
                        'ranap_dokter',
                        'ranap_dokter_paramedis',
                        'ranap_paramedis',
                        'ralan_dokter',
                        'ralan_dokter_paramedis',
                        'ralan_paramedis',
                    ])
                    ->sum();

                $total = $kategoriBilling->sum();

                $sisa = $total - $ranap->uangmuka - $ranap->dibayar;

                return new Fluent([
                    'waktu_keluar'  => $ranap->waktu_keluar,
                    'no_nota'       => $ranap->no_nota,
                    'no_rkm_medis'  => $ranap->no_rkm_medis,
                    'nm_pasien'     => $ranap->nm_pasien,
                    'png_jawab'     => $ranap->png_jawab,
                    'perujuk'       => $ranap->perujuk,
                    'registrasi'    => $kategoriBilling->get('registrasi'),
                    'tindakan'      => $billingTindakan,
                    'obat'          => $kategoriBilling->get('obat'),
                    'retur_obat'    => $kategoriBilling->get('retur_obat'),
                    'resep_pulang'  => $kategoriBilling->get('resep_pulang'),
                    'laborat'       => $kategoriBilling->get('laborat'),
                    'radiologi'     => $kategoriBilling->get('radiologi'),
                    'potongan'      => $kategoriBilling->get('potongan'),
                    'tambahan'      => $kategoriBilling->get('tambahan'),
                    'kamar_service' => $kategoriBilling->only(['kamar', 'service'])->sum(),
                    'operasi'       => $kategoriBilling->get('operasi'),
                    'harian'        => $kategoriBilling->get('harian'),
                    'total'         => $total,
                    'uangmuka'      => $ranap->uangmuka,
                    'dibayar'       => $ranap->dibayar,
                    'sisa'          => $sisa,
                    'ruangan'       => $ranap->ruangan,
                    'dokter_pj'     => $ranap->dpjpRanap->implode('nm_dokter', ', '),
                ]);
            });
    }

    protected function dataPerSheet(): array
    {
        return [
            $this->mapData(),
        ];
    }

    protected function columnHeaders(): array
    {
        return [
            "Waktu Keluar",
            "No. Nota",
            "No. RM",
            "Pasien",
            "Jenis Bayar",
            "Asal Rujukan",
            "Registrasi",
            "Tindakan",
            "Obat + Embl. Tsl.",
            "Retur Obat",
            "Resep Pulang",
            "Laboratorium",
            "Radiologi",
            "Kamar + Layanan",
            "Operasi",
            "Harian",
            "Tambahan",
            "Potongan",
            "Uang Muka",
            "Total",
            "Dibayar",
            "Sisa",
            "Kamar",
            "Dokter P.J.",
        ];
    }

    protected function pageHeaders(): array
    {
        return [
            'RS Samarinda Medika Citra',
            'Laporan Piutang Rawat Inap',
            now()->format('d F Y'),
            'Periode ' . CarbonImmutable::parse($this->periodeAwal)->format('d F Y') . ' - ' . CarbonImmutable::parse($this->periodeAkhir)->format('d F Y'),
        ];
    }
}
