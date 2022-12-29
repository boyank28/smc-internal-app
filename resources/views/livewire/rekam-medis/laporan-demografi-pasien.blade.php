<div>
    <x-flash />

    <div class="card">
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-start">
                        <span class="text-sm pr-4">Periode:</span>
                        <input class="form-control form-control-sm" style="width: 10rem" type="date" wire:model.defer="periodeAwal" />
                        <span class="text-sm px-2">sampai</span>
                        <input class="form-control form-control-sm" style="width: 10rem" type="date" wire:model.defer="periodeAkhir" />
                        <div class="ml-auto">
                            <button class="ml-auto btn btn-default btn-sm" type="button" wire:click="exportToExcel">
                                <i class="fas fa-file-excel"></i>
                                <span class="ml-1">Export ke Excel</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row mt-2">
                <x-filter />
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover table-striped table-sm text-sm m-0" style="width: 150rem">
                    <thead>
                        <tr>
                            <th width="250">Kecamatan</th>
                            <th width="70">No. RM</th>
                            <th width="150">No. Registrasi</th>
                            <th width="250">Nama Pasien</th>
                            <th width="500">Alamat</th>
                            <th width="50">Umur</th>
                            <th width="50">L / P</th>
                            <th>Diagnosa</th>
                            <th width="100">Agama</th>
                            <th width="100">Pendidikan</th>
                            <th width="100">Bahasa</th>
                            <th width="100">Suku</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($this->demografiPasien as $pasien)
                            <tr>
                                <td>{{ $pasien->kecamatan }}</td>
                                <td>{{ $pasien->no_rm }}</td>
                                <td>{{ $pasien->no_rawat }}</td>
                                <td>{{ $pasien->nm_pasien }}</td>
                                <td>{{ $pasien->almt }}</td>
                                <td>{{ $pasien->umur }}</td>
                                <td>{{ $pasien->jk }}</td>
                                <td>{{ $pasien->diagnosa }}</td>
                                <td>{{ $pasien->agama }}</td>
                                <td>{{ $pasien->pendidikan }}</td>
                                <td>{{ $pasien->bahasa }}</td>
                                <td>{{ $pasien->suku }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer">
            <div class="d-flex align-items-center justify-content-start bg-light">
                <p class="text-muted">Menampilkan {{ $this->demografiPasien->count() }} dari total {{ number_format($this->demografiPasien->total(), 0, ',', '.') }} item.</p>
                <div class="ml-auto">
                    {{ $this->demografiPasien->links() }}
                </div>
            </div>
        </div>
    </div>
</div>