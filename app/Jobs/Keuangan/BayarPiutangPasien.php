<?php

namespace App\Jobs\Keuangan;

use App\Models\Keuangan\BayarPiutang;
use App\Models\Keuangan\Jurnal\Jurnal;
use App\Models\Keuangan\PenagihanPiutang;
use App\Models\Keuangan\PiutangPasien;
use App\Models\Keuangan\PiutangPasienDetail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class BayarPiutangPasien implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $noTagihan;
    private string $jaminanPiutang;
    private string $noRawat;

    private string $tglAwal;
    private string $tglAkhir;
    private string $jaminanPasien;
    private string $jenisPerawatan;

    private string $tglBayar;
    private string $userId;
    private string $akun;
    private float $diskonPiutang;
    private string $akunDiskonPiutang;
    private float $tidakTerbayar;
    private string $akunTidakTerbayar;

    private float $totalPiutang;
    private float $cicilanSekarang;

    /**
     * Create a new job instance.
     * 
     * @param  array{
     *     no_tagihan: string,
     *     kd_pj: string,
     *     no_rawat: string,
     *     tgl_awal: string,
     *     tgl_akhir: string,
     *     jaminan_pasien: string,
     *     jenis_perawatan: string,
     *     tgl_bayar: string,
     *     user_id: string,
     *     akun: string,
     *     diskon_piutang: float,
     *     akun_diskon_piutang: string,
     *     tidak_terbayar: float,
     *     akun_tidak_terbayar: string,
     * } $params
     */
    public function __construct(array $params)
    {
        $this->noTagihan = $params['no_tagihan'];
        $this->jaminanPiutang = $params['kd_pj'];
        $this->noRawat = $params['no_rawat'];

        $this->tglAwal = $params['tgl_awal'];
        $this->tglAkhir = $params['tgl_akhir'];
        $this->jaminanPasien = $params['jaminan_pasien'];
        $this->jenisPerawatan = $params['jenis_perawatan'];

        $this->tglBayar = $params['tgl_bayar'];
        $this->userId = $params['user_id'];
        $this->akun = $params['akun'];
        $this->diskonPiutang = $params['diskon_piutang'] ?? 0;
        $this->akunDiskonPiutang = $params['akun_diskon_piutang'];
        $this->tidakTerbayar = $params['tidak_terbayar'] ?? 0;
        $this->akunTidakTerbayar = $params['akun_tidak_terbayar'];
    }

    public function handle(): void
    {
        $this->proceed();
    }

    protected function proceed(): void
    {
        $model = PenagihanPiutang::query()
            ->accountReceivable($this->tglAwal, $this->tglAkhir, $this->jaminanPasien, $this->jenisPerawatan)
            ->where([
                ['penagihan_piutang.no_tagihan', '=', $this->noTagihan],
                ['penagihan_piutang.kd_pj', '=', $this->jaminanPiutang],
                ['detail_penagihan_piutang.no_rawat', '=', $this->noRawat],
            ])
            ->first();

        if (is_null($model)) {
            return;
        }

        DB::connection('mysql_sik')
            ->transaction(function () use ($model) {
                $totalCicilan = $model->sisa_piutang;

                $detailJurnal = collect();

                if ($this->diskonPiutang > 0) {
                    $totalCicilan -= $this->diskonPiutang;

                    $detailJurnal->push(['kd_rek' => $this->akunDiskonPiutang, 'debet' => $this->diskonPiutang, 'kredit' => 0]);
                }

                if ($this->tidakTerbayar > 0) {
                    $totalCicilan -= $this->tidakTerbayar;

                    $detailJurnal->push(['kd_rek' => $this->akunTidakTerbayar, 'debet' => $this->tidakTerbayar, 'kredit' => 0]);
                }

                $detailJurnal->push(
                    ['kd_rek' => $this->akun, 'debet' => $totalCicilan, 'kredit' => 0],
                    ['kd_rek' => $model->kd_rek, 'debet' => 0, 'kredit' => ($totalCicilan + $this->diskonPiutang + $this->tidakTerbayar)],
                );

                tracker_start('mysql_sik');

                BayarPiutang::insert([
                    'tgl_bayar'             => $this->tglBayar,
                    'no_rkm_medis'          => $model->no_rkm_medis,
                    'catatan'               => sprintf('diverifikasi oleh %s', $this->userId),
                    'no_rawat'              => $this->noRawat,
                    'kd_rek'                => $this->akun,
                    'kd_rek_kontra'         => $model->kd_rek,
                    'besar_cicilan'         => $totalCicilan,
                    'diskon_piutang'        => $this->diskonPiutang,
                    'kd_rek_diskon_piutang' => $this->akunDiskonPiutang,
                    'tidak_terbayar'        => $this->tidakTerbayar,
                    'kd_rek_tidak_terbayar' => $this->akunTidakTerbayar,
                ]);

                PiutangPasienDetail::query()
                    ->where('no_rawat', $this->noRawat)
                    ->where('nama_bayar', $model->nama_bayar)
                    ->where('kd_pj', $model->kd_pj_tagihan)
                    ->update([
                        'sisapiutang' => $model->sisa_piutang - 
                            ($totalCicilan + $this->diskonPiutang + $this->tidakTerbayar)
                    ]);

                tracker_end('mysql_sik', $this->userId);

                $this->setLunasPiutang($model->no_rkm_medis, $model->nama_bayar, $model->kd_pj_tagihan);

                $this->setSelesaiPenagihanPiutang($model->kd_rek);

                tracker_start('mysql_sik');

                Jurnal::catat(
                    $this->noRawat,
                    'U',
                    sprintf('BAYAR PIUTANG TAGIHAN %s, OLEH %s', $this->noTagihan, $this->userId),
                    $this->tglBayar,
                    $detailJurnal->all()
                );

                tracker_end('mysql_sik', $this->userId);
            });
    }

    protected function setLunasPiutang(
        string $noRM,
        string $namaBayar,
        string $kodePenjamin
    ): void {
        if (empty($noRM) || empty($namaBayar) || empty($kodePenjamin)) {
            return;
        }

        $this->totalPiutang = PiutangPasienDetail::query()
            ->where('no_rawat', $this->noRawat)
            ->sum('totalpiutang');

        $this->totalPiutang = intval(round(floatval($this->totalPiutang)));

        $this->cicilanSekarang = BayarPiutang::query()
            ->where('no_rawat', $this->noRawat)
            ->where('no_rkm_medis', $noRM)
            ->sum(DB::raw('besar_cicilan + diskon_piutang + tidak_terbayar'));

        $this->cicilanSekarang = intval(round(floatval($this->cicilanSekarang)));

        if ($this->totalPiutang !== $this->cicilanSekarang) {
            return;
        }

        tracker_start('mysql_sik');

        PiutangPasien::query()
            ->where('no_rawat', $this->noRawat)
            ->update(['status' => 'Lunas']);

        tracker_end('mysql_sik', $this->userId);
    }

    protected function setSelesaiPenagihanPiutang(string $akunKontra): void
    {
        $tagihanPiutang = PenagihanPiutang::query()
            ->with('detail')
            ->where('no_tagihan', $this->noTagihan)
            ->first();
            
        if (is_null($tagihanPiutang)) {
            return;
        }

        $totalTagihanPiutang = $tagihanPiutang->detail->sum('sisapiutang');
        $totalTagihanPiutang = intval(round(floatval($totalTagihanPiutang)));

        $piutangDibayar = BayarPiutang::query()
            ->whereIn('no_rawat', $tagihanPiutang->detail->pluck('no_rawat')->all())
            ->where('kd_rek', $this->akun)
            ->where('kd_rek_kontra', $akunKontra)
            ->sum(DB::raw('besar_cicilan + diskon_piutang + tidak_terbayar'));

        $piutangDibayar = intval(round(floatval($piutangDibayar)));

        if ($totalTagihanPiutang !== $piutangDibayar) {
            return;
        }

        tracker_start('mysql_sik');

        $tagihanPiutang->update(['status' => 'Sudah Dibayar']);

        tracker_end('mysql_sik', $this->userId);
    }
}
