@extends('admin._master')

@section('content')
<div id="page--role-permission" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <a class="uk--tool-header-back" href="{{ url('/admin/roles') }}">
                        <i class="fas fa-angle-left fa-lg"></i>
                    </a>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title uk-text-uppercase">PERMISSIONS OF {{ $role->display_name }}</small></h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form id="form--role-permission" action="{{ request()->url() }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <table class="uk-table uk-table-small uk-table-divider uk-text-small">
                <tbody>
                    <template v-for="(permissions, key) in permissionGroups">
                        <tr>
                            <th colspan="2">@{{ key }}</th>
                        </tr>
                        <tr v-for="permission in permissions">
                            <td width="5"></td>
                            <td>
                                <label>
                                    <div uk-grid>
                                        <div class="uk-width-expand">
                                            @{{ permission.display_name }}
                                        </div>
                                        <div class="uk-width-auto">
                                            <input v-model="rolePermissions" class="uk-checkbox" type="checkbox" name="permissions" :value="permission.id" />
                                        </div>
                                    </div>
                                </label>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </form>
        <div class="uk-margin uk-text-right">
            <el-button type="primary" size="small" @click="update">Update</el-button>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var rolePermissions = JSON.parse('{{ json_encode($rolePermissions) }}')

    new Vue({
        el: '#page--role-permission',

        data: {
            permissionGroups: [],
            rolePermissions: rolePermissions
        },

        methods: {
            getPermissionGroups() {
                axios.get(`${Laravel.url}/admin/permissions/json`)
                    .then(({ data }) => {
                        this.permissionGroups = data
                    })
                    .catch(error => {
                        this.$notify({
                            title: error.response.status,
                            message: error.response.statusText,
                            type: 'error'
                        })
                    })
            },
            update() {
                axios.post('{{ request()->url() }}', {
                    _method: 'PATCH',
                    permissions: this.rolePermissions
                })
                .then(({ data }) => {
                    this.rolePermissions = data.rolePermissions

                    this.$notify({
                        title: 'Success',
                        message: 'Permissions updated.',
                        type: 'success'
                    })

                    location.reload()
                })
                .catch(error => {
                    this.$notify({
                        title: error.response.status,
                        message: error.response.statusText,
                        type: 'error'
                    })
                })
            }
        },

        mounted() {
            this.getPermissionGroups()
        }
    })
</script>
@endsection