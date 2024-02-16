<div>
    @push('js')
        <script>
            $('#modal-input-posting-jurnal').on('shown.bs.modal', e => {
                @this.emit('posting-jurnal.show-modal')
            })

            $('#modal-input-posting-jurnal').on('hide.bs.modal', e => {
                @this.emit('posting-jurnal.hide-modal')
                @this.call('resetData')
            })

            $(document).on('data-saved', () => {
                $('#modal-input-posting-jurnal').modal('hide')
            })
        </script>
    @endpush
    <x-modal id="modal-input-posting-jurnal" :title="('Posting Jurnal Baru')" livewire centered>
        <x-slot name="body" style="overflow-x: hidden">
            <x-form id="form-input-posting-jurnal" wire:submit.prevent="create">
                <div class="form-group d-flex justify-content-start align-items-center m-0 p-0">
                    <div class="form-group mt-3">
                        <label for="no_bukti">No. Bukti</label>
                        <input type="text" id="no_bukti" wire:model.defer="no_bukti" class="form-control form-control-sm" />
                        <x-form.error name="no_bukti" />
                    </div>
                    <div class="form-group mt-3 px-5 ">
                        <label for="tgl_jurnal">Tgl. Jurnal</label>
                        <x-form.date model="tgl_jurnal" />
                        <x-form.error name="tgl_jurnal" />
                    </div>
                    <div class="form-group mt-3">
                        <label for="jenis">Jenis</label>
                        <x-form.select id="jenis" model="jenis" :options="['U' => 'Umum', 'P' => 'Penyesuaian']" />
                        <x-form.error name="jenis" />
                    </div>
                    <div class="form-group mt-3 px-5">
                        <label for="jenis">Waktu</label>
                        <input type="text" id="jamJurnal" wire:model.defer="jam_jurnal" class="form-control form-control-sm">
                    </div>
                </div>

                <div class="form-group mt-3">
                    <label for="keterangan">Keterangan</label>
                    <input type="text" id="keterangan" wire:model.defer="keterangan" class="form-control form-control-sm" />
                    <x-form.error name="keterangan" />
                </div>
                <div class="form-group mt-3">
                    <div class="d-flex justify-content-start align-items-center">
                        <span class="d-block font-weight-bold" style="width: calc(60% - 1.6rem)">Rekening</span>
                        <span class="d-block font-weight-bold">Debet</span>
                        <span class="d-block font-weight-bold px-5"></span>
                        <span class="d-block font-weight-bold px-5">Kredit</span>
                    </div>
                    <ul class="p-0 m-0 mt-2 mb-3 d-flex flex-column" style="row-gap: 0.5rem" id="detail-jurnal">
                        @foreach($this->detail as $index => $item)
                        <li class="d-flex justify-content-start align-items-center m-0 p-0" wire:key="detail-junal-{{ $index }}">  
                            <div style="width:100%" wire:ignore>
                                <select id="kd_rek_{{ $index }}" wire:model.defer="detail.{{ $index }}.kd_rek" class="form-control form-control-sm select2" data-index="{{ $index }}">
                                    <option value="">Pilih Rekening</option>
                                    @foreach($this->rekening as $kd_rek => $rekening)
                                        <option value="{{ $kd_rek }}">{{ $kd_rek }} - {{ $rekening }}</option>
                                    @endforeach
                                </select>
                                <x-form.error name="detail.{{ $index }}.kd_rek" />
                                @push('css')
                                    @once
                                        <link href="{{ asset('css/select2.min.css') }}" rel="stylesheet">
                                        <link href="{{ asset('css/select2-bootstrap4.min.css') }}" rel="stylesheet">
                                        <style>
                                            .select2-selection__arrow {
                                                top: 0 !important;
                                            }

                                            .select2-container--default .select2-selection--single .select2-selection__arrow {
                                                height: 2rem !important;
                                            }

                                            .select2-container .select2-selection--single .select2-selection__rendered {
                                                padding-left: 0 !important;
                                                margin-left: -0.125rem !important;
                                            }
                                        </style>
                                    @endonce
                                @endpush
                                @push('js')
                                    @once
                                        <script src="{{ asset('js/select2.full.min.js') }}"></script>
                                    @endonce
                                    <script>
                                        document.addEventListener('livewire:load', function () {
                                            Livewire.on('detailAdded', function () {
                                                $('.select2').select2();
                                            })
                                        })
                                        $(document).ready(function () {
                                            $('.select2').select2()
                                            $(document).on('change', '.select2', function (e) {
                                                var data = $(this).val()
                                                @this.set('detail.' + $(this).data('index') + '.kd_rek', data)
                                            });
                                        });
                                    </script>
                                @endpush
                            </div>
                            <span class="ml-4 text-sm" style="width: 3rem">Rp.</span>
                            <input type="number" class="form-control form-control-sm text-right w-25" wire:model.defer="detail.{{ $index }}.debet">
                            <span class="ml-4 text-sm" style="width: 3rem">Rp.</span>
                            <input type="number" class="form-control form-control-sm text-right w-25" wire:model.defer="detail.{{ $index }}.kredit">
                            <button type="button" wire:click="removeDetail({{ $index }})" class="btn btn-sm btn-danger ml-3"><i class="fas fa-trash"></i></button>
                        </li>
                        @endforeach
                    </ul>
                    <div class="form-group d-flex justify-content-start align-items-center m-0 p-0">
                        <x-button size="sm" variant="secondary" title="Tambah Detail" icon="fas fa-plus" wire:click="addDetail" />
                        <div class="ml-3" style="width: calc(29% - 1.6rem)">
                            <strong>Total :</strong>
                            <x-form.error name="totalDebitKredit" />
                        </div>
                        <div class="ml-3 px-5">
                            Rp. {{ number_format($totalDebet, 2) }}
                        </div>
                        <div class="ml-3 px-5">
                            Rp. {{ number_format($totalKredit, 2) }}
                        </div>
                    </div>

                </div>
                <div class="mt-4">
                    <h5>Data Jurnal Sementara</h5>
                    @if(!empty($this->jurnalSementara) && is_array($this->jurnalSementara))
                    <x-table>
                        <x-slot name="columns">
                            <x-table.th title="Aksi" />
                            <x-table.th title="No. Bukti" />
                            <x-table.th title="Tgl. Jurnal" />
                            <x-table.th title="Jam Jurnal" />
                            <x-table.th title="Jenis" />
                            <x-table.th title="Keterangan" />
                            <x-table.th title="Debet" />
                            <x-table.th title="Kredit" />
                        </x-slot>
                        <x-slot name="body">
                            @foreach($this->jurnalSementara as $index => $jurnalSementara)
                                <x-table.tr>
                                    <x-table.td>
                                        <button type="button" wire:click="hapusJurnalSementara({{ $index }})" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                    </x-table.td>
                                    <x-table.td>{{ $jurnalSementara['no_bukti'] }}</x-table.td>
                                    <x-table.td>{{ $jurnalSementara['tgl_jurnal'] }}</x-table.td>
                                    <x-table.td>{{ $jurnalSementara['jam_jurnal'] }}</x-table.td>
                                    <x-table.td>{{ $jurnalSementara['jenis'] === 'U' ? 'Umum' : 'Penyesuaian'}}</x-table.td>
                                    <x-table.td>{{ $jurnalSementara['keterangan'] }}</x-table.td>
                                    <x-table.td></x-table.td>
                                    <x-table.td></x-table.td>
                                </x-table.tr>
                                @foreach($jurnalSementara['detail'] as $detail)
                                    <x-table.tr>
                                        <x-table.td></x-table.td>
                                        <x-table.td></x-table.td>
                                        <x-table.td></x-table.td>
                                        <x-table.td></x-table.td>
                                        <x-table.td></x-table.td>
                                        <x-table.td>
                                            {{ $detail['kd_rek'] }} - {{ $this->getRekeningName($detail['kd_rek']) }}
                                        </x-table.td>
                                        <x-table.td>Rp. {{ number_format($detail['debet'], 2) }}</x-table.td>
                                        <x-table.td>Rp. {{ number_format($detail['kredit'], 2) }}</x-table.td>
                                    </x-table.tr>
                                @endforeach
                            @endforeach
                        </x-slot>
                        
                    </x-table>
                    @else
                        <x-table>
                            <x-slot name="columns">
                                <x-table.th title="Aksi" />
                                <x-table.th title="No. Bukti" />
                                <x-table.th title="Tgl. Jurnal" />
                                <x-table.th title="Jam Jurnal" />
                                <x-table.th title="Jenis" />
                                <x-table.th title="Keterangan" />
                                <x-table.th title="Debet" />
                                <x-table.th title="Kredit" />
                            </x-slot>
                            <x-slot name="body">
                                <x-table.tr-empty colspan="12" padding />
                            </x-slot>
                        </x-table>
                    @endif
                </div>
                
                            
            </x-form>
        </x-slot>
        <x-slot name="footer" class="justify-content-start">
            <x-button size="sm" variant="success" title="Tambah Jurnal" icon="fas fa-plus" wire:click="add" />
            <x-button size="sm" class="ml-auto" data-dismiss="modal" id="batalsimpan" title="Batal" />
            <x-button size="sm" variant="primary" type="submit" class="ml-2" id="simpandata" title="Simpan" icon="fas fa-save" form="form-input-posting-jurnal"
            :disabled="empty($this->jurnalSementara)" />
        @push('js')
            <script>
                document.addEventListener('livewire:load', function () {
                    Livewire.on('redirectToPrintLayout', function (jurnalSementara) {
                        window.location.href = '/print-layout?jurnalSementara=' + encodeURIComponent(JSON.stringify(jurnalSementara));
                    });
                });
            </script>        
        @endpush
        </x-slot>
    </x-modal>
</div>