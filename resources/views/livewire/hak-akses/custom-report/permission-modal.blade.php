<div wire:ignore.self>
    @once
        @push('js')
            <script>
                document.addEventListener('DOMContentLoaded', e => {
                    let inputRole
                    let inputPermissions

                    // cache checked permissions
                    let checkedPermissions

                    $(document).ready(() => {
                        inputRole = $('input[type=hidden][name=role]')
                        inputPermissions = $('input[name=permissions]')
                    })

                    $('button#simpan').click(() => {
                        let currentRoleId = inputRole.val()
                        let currentPermissionsIds = []

                        inputPermissions.each((i, el) => currentPermissionsIds.push(el.checked && el.value))

                        console.log({ inputPermissions })

                        @this.set('roleId', currentRoleId)
                        @this.set('permissionIds', currentPermissionsIds)

                        @this.updatePermissions()
                    })

                    $('#modal-role-permissions').on('shown.bs.modal', e => {
                        @this.emit('showModal')

                        let checkedPermissionsIds = @this.get('permissionIds')

                        inputPermissions.each((i, el) => checkedPermissionsIds.find(v => v === el.value))
                    })

                    $('#modal-role-permissions').on('hide.bs.modal', e => {
                        @this.emit('hideModal')
                    })
                })
            </script>
        @endpush
    @endonce

    <x-modal :livewire="true" id="modal-role-permissions" :title="'Set Permission untuk Role ' . $this->roleName">
        <x-slot name="body" class="position-relative py-0">
            <x-row-col>
                <ul class="form-group" id="role_permissions">
                    <input type="hidden" name="role" class="d-none">
                    @foreach ($this->permissions as $role => $permissions)
                        @foreach ($permissions as $key => $name)
                            <li @class(['custom-control custom-checkbox', 'mt-3' => $loop->first])>
                                <input class="custom-control-input" type="checkbox" id="permission-{{ $key }}" value="{{ $key }}" name="permissions">
                                <label for="permission-{{ $key }}" class="custom-control-label font-weight-normal">{{ $name }}</label>
                            </li>
                        @endforeach
                    @endforeach
                </ul>
            </x-row-col>
        </x-slot>
        <x-slot name="footer" class="justify-content-start">
            <x-filter.search />
            <x-button class="btn-default ml-auto" data-dismiss="modal" title="Batal" />
            <x-button class="btn-primary ml-2" data-dismiss="modal" title="Simpan" icon="fas fa-save" />
        </x-slot>
    </x-modal>
</div>