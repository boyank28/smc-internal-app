<div>
    <x-flash />

    <x-card>
        <x-slot name="header">
            <x-card.row-col>
                <x-filter.range-date title="Tgl. SPM" />
                <x-filter.toggle class="ml-4" model="hanyaTampilkanBarangSelisih" title="Tampilkan Barang Selisih" />
                <x-filter.button-export-excel class="ml-auto" />
            </x-card.row-col>
            <x-card.row-col class="mt-2">
                <x-filter.select-perpage />
                <x-filter.button-reset-filters class="ml-auto" />
                <x-filter.search class="ml-2" />
            </x-card.row-col>
        </x-slot>
        <x-slot name="body">
            <x-table :sortColumns="$sortColumns" sortable zebra hover sticky>
                <x-slot name="columns">
                    <x-table.th name="no_pemesanan" title="No. Pemesanan" />
                    <x-table.th name="nama_brng" title="Nama" />
                    <x-table.th name="suplier_pesan" title="Supplier Tujuan" />
                    <x-table.th name="suplier_datang" title="Supplier yang Mendatangkan" />
                    <x-table.th name="jumlah_pesan" title="Jumlah Dipesan" />
                    <x-table.th name="jumlah_datang" title="Jumlah yang Datang" />
                    <x-table.th name="selisih" title="Selisih" />
                </x-slot>
                <x-slot name="body">
                    @forelse ($this->perbandinganOrderObatPO as $obat)
                        <x-table.tr>
                            <x-table.td>{{ $obat->no_pemesanan }}</x-table.td>
                            <x-table.td>{{ $obat->nama_brng }}</x-table.td>
                            <x-table.td>{{ $obat->suplier_pesan }}</x-table.td>
                            <x-table.td>{{ $obat->suplier_datang }}</x-table.td>
                            <x-table.td>{{ $obat->jumlah_pesan }}</x-table.td>
                            <x-table.td>{{ $obat->jumlah_datang }}</x-table.td>
                            <x-table.td>{{ $obat->selisih }}</x-table.td>
                        </x-table.tr>
                    @empty
                        <x-table.tr-empty :colspan="7" />
                    @endforelse
                </x-slot>
            </x-table>
        </x-slot>
        <x-slot name="footer">
            <x-paginator :data="$this->perbandinganOrderObatPO" />
        </x-slot>
    </x-card>
</div>
