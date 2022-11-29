<div class="card">
    @once
        @push('css')
            <link rel="stylesheet" href="{{ asset('plugins/select2/css/select2.min.css') }}">
            <link rel="stylesheet" href="{{ asset('plugins/select2-bootstrap4-theme/select2-bootstrap4.min.css') }}">
        @endpush
        @push('js')
            <script src="{{ asset('plugins/select2/js/select2.full.min.js') }}"></script>
            <script>
                let supplierSelectComponent

                $(document).ready(() => {
                    supplierSelectComponent = $('.select2').select2({
                        theme: 'bootstrap4'
                    })
                })

                const loadData = (barang, supplier) => {
                    @this.getItem(barang)

                    supplierSelectComponent.val(supplier)

                    supplierSelectComponent.trigger('change')
                }

                $('#simpandata').click(() => {
                    let result = @this.simpan()

                    if (!result) {
                        $(document).Toasts('create', {
                            title: "Sukses!",
                            autohide: true,
                            delay: 3000,
                            body: "Data berhasil disimpan."
                        })
                    }
                });
            </script>
        @endpush
    @endonce
    <div class="card-body border-bottom" id="input">
        <div class="row">
            <div class="col-2">
                <div class="form-group">
                    <label for="kode_brng">Kode Barang</label>
                    <input type="text" class="form-control" id="kode_brng" readonly autocomplete="off" wire:model.defer="kodeBarang">
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="nama_brng">Nama Barang</label>
                    <input type="text" class="form-control" id="nama_brng" readonly autocomplete="off" wire:model.defer="namaBarang">
                </div>
            </div>
            <div class="col-6">
                <div class="form-group" wire:ignore>
                    <label for="supplier">Supplier</label>
                    <select class="form-control select2" name="supplier" id="supplier">
                        @foreach ($supplier as $kode => $nama)
                            <option value="{{ $kode }}" {{ old('kd_supplier', $kodeSupplier) == $kode ? 'selected' : null }}>{{ $nama }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="stok_min">Stok minimal</label>
                    <input type="number" class="form-control" id="stok_min" min="0" autocomplete="off" wire:model.defer="stokMin">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="stok_max">Stok maksimal</label>
                    <input type="number" class="form-control" id="stok_max" min="0" autocomplete="off" wire:model.defer="stokMax">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="stok_saat_ini">Stok saat ini</label>
                    <input type="text" class="form-control" id="stok_saat_ini" autocomplete="off" disabled wire:model.defer="stokSekarang">
                </div>
            </div>
            <div class="col-3">
                <div class="form-group">
                    <label for="saran_order">Saran order</label>
                    <input type="text" class="form-control" id="saran_order" autocomplete="off" disabled wire:model.defer="saranOrder">
                </div>
            </div>
        </div>
        <div class="row mt-2">
            <div class="col-12">
                <div class="d-flex justify-content-start align-items-center">
                    <div class="input-group w-25">
                        <div class="input-group-prepend">
                            <label for="cari" class="input-group-text">Cari...</label>
                        </div>
                        <input type="search" id="cari" name="cari" class="form-control">
                    </div>
                    <button type="button" wire:click="exportToExcel" class="ml-auto btn btn-default">
                        <i class="fas fa-file-excel"></i>
                        <span class="ml-1">Export ke Excel</span>
                    </button>
                    <button type="button" class="ml-2 btn btn-primary" id="simpandata">
                        Simpan
                    </button>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0" style="position:relative">
        <table id="table_index" class="table table-hover table-striped table-sm text-sm">
            <thead>
                <tr>
                    <th>Kode</th>
                    <th>Nama</th>
                    <th>Satuan</th>
                    <th>Jenis</th>
                    <th>Supplier</th>
                    <th>Min</th>
                    <th>Max</th>
                    <th>Saat ini</th>
                    <th>Saran order</th>
                    <th>Harga Per Unit (Rp)</th>
                    <th>Total Harga (Rp)</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($barang as $item)
                    <tr style="position: relative">
                        <td>
                            {{ $item->kode_brng }}
                            <a href="#" style="position: absolute; left: 0; right: 0; top: 0; bottom: 0" onclick="loadData('{{ $item->kode_brng }}', '{{ $item->kode_supplier }}')"></a>
                        </td>
                        <td>{{ $item->nama_brng }}</td>
                        <td>{{ $item->satuan }}</td>
                        <td>{{ $item->jenis }}</td>
                        <td>{{ $item->nama_supplier }}</td>
                        <td>{{ $item->stokmin }}</td>
                        <td>{{ $item->stokmax }}</td>
                        <td>{{ $item->stok }}</td>
                        <td>{{ $item->saran_order }}</td>
                        <td>{{ $item->harga }}</td>
                        <td>{{ $item->total_harga }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
