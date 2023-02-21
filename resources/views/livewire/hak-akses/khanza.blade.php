<div>
    <x-flash />

    @once
        @push('js')
            <script>
                const inputNamaField = $('input#field')
                const inputJudulMenu = $('input#judul')

                const buttonSimpan = $('button#simpan')
                const buttonBatalSimpan = $('button#batal')
                const buttonResetFilter = $('button#reset-filter')

                buttonSimpan.click(e => {
                    @this.simpanHakAkses(inputNamaField.val(), inputJudulMenu.val())

                    resetInput(e)
                })
                buttonBatalSimpan.click(resetInput)
                buttonResetFilter.click(resetInput)

                $(document).on('data-saved', resetInput)

                inputNamaField.on('keyup', e => {
                    if (inputNamaField.val().trim() == '' && inputJudulMenu.val().trim() == '') {
                        setFormState('disabled', true)
                    }

                    setFormState('disabled', false)
                })

                inputJudulMenu.on('keyup', e => {
                    if (!inputNamaField.val() && !inputJudulMenu.val()) {
                        setFormState('disabled', true)
                    }

                    setFormState('disabled', false)
                })

                function resetInput(e) {
                    inputNamaField.val(null)
                    inputJudulMenu.val(null)

                    inputNamaField.trigger('change')
                    inputJudulMenu.trigger('change')

                    setFormState('disabled', true)
                }

                function loadData({
                    namaField,
                    judulMenu
                }) {
                    setFormState('disabled', false)

                    inputNamaField.val(namaField)
                    inputJudulMenu.val(judulMenu)

                    inputNamaField.trigger('change')
                    inputJudulMenu.trigger('change')
                }

                function setFormState(prop, state) {
                    buttonSimpan.prop(prop, state)
                    buttonBatalSimpan.prop(prop, state)
                }
            </script>
        @endpush
    @endonce

    <x-card>
        <x-slot name="header">
            <x-card.row livewire>
                <div class="col-5">
                    <div class="form-group">
                        <label class="text-sm" for="field">Nama Field</label>
                        <input class="form-control form-control-sm" id="field" type="text" autocomplete="off">
                    </div>
                </div>
                <div class="col-5">
                    <div class="form-group">
                        <label class="text-sm" for="judul">Judul Menu</label>
                        <input class="form-control form-control-sm" id="judul" type="text" autocomplete="off">
                    </div>
                </div>
                <div class="col-2">
                    <div class="d-flex align-items-end h-100">
                        <x-button class="btn-default mb-3" title="Batal" disabled />
                        <x-button class="btn-primary mb-3 ml-2" title="Simpan" icon="fas fa-save" disabled />
                    </div>
                </div>
            </x-card.row>
            <x-card.row-col class="mt-2">
                <x-filter.select-perpage />
                <x-filter.button-reset-filters class="ml-auto" />
                <x-filter.search class="ml-2" />
                <x-filter.button method="syncHakAkses" class="ml-3" icon="fas fa-sync-alt" title="Sync Hak Akses" />
            </x-card.row-col>
        </x-slot>

        <x-slot name="body" class="table-responsive">
            <x-table sortable :sortColumns="$sortColumns">
                <x-slot name="columns">
                    <x-table.th name="nama_field" title="Nama Field" />
                    <x-table.th name="judul_menu" title="Judul Menu" />
                </x-slot>
                <x-slot name="body">
                    @foreach ($this->hakAksesKhanza as $hakAkses)
                        <x-table.tr>
                            <x-table.td clickable data-nama-field="{{ $hakAkses->nama_field }}" data-judul-menu="{{ $hakAkses->judul_menu }}">
                                {{ $hakAkses->nama_field }}
                            </x-table.td>
                            <x-table.td>{{ $hakAkses->judul_menu }}</x-table.td>
                        </x-table.tr>
                    @endforeach
                </x-slot>
            </x-table>
        </x-slot>
        <x-slot name="footer">
            <x-paginator :data="$this->hakAksesKhanza" />
        </x-slot>
    </x-card>
</div>