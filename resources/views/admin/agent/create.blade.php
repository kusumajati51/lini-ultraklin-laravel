@extends('admin._master')

@section('content')
<div id="content" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <a class="uk--tool-header-back" href="{{ url('/admin/agents') }}">
                    <i class="fas fa-angle-left fa-lg"></i>
                </a>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">NEW AGENT</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form action="{{ url('/admin/agents') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="id" :value="input.user.id"/>
            <div uk-grid>
                <div class="uk-width-1-2">
                    <label class="uk-form-label uk-text-uppercase">User</label>
                    <div>
                        <el-autocomplete
                            v-model="input.userString"
                            class="inline-input uk-width-1-1"
                            placeholder="Please input name / email / phone"
                            :fetch-suggestions="queryUser"
                            :trigger-on-focus="false"
                            @select="onSelectUser">
                            <template slot-scope="{ item }">
                                <div class="uk-text-uppercase uk-text-bold">@{{ item.value }}</div>
                                <div>@{{ item.data.email }}</div>
                                <div>@{{ item.data.phone }}</div>
                            </template>
                        </el-autocomplete>
                    </div>
                    {!! $errors->first('id', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
                </div>
                <div class="uk-width-1-2">
                    <div class="uk-margin-small">
                        <div class="uk--box-label">Name</div>
                        <div class="uk--box-text">@{{ input.user.name }}</div>
                    </div>
                    <div class="uk-margin-small">
                        <div class="uk--box-label">Email</div>
                        <div class="uk--box-text">@{{ input.user.email }}</div>
                    </div>
                    <div class="uk-margin-small">
                        <div class="uk--box-label">Phone</div>
                        <div class="uk--box-text">@{{ input.user.phone }}</div>
                    </div>
                </div>
            </div>
            <div class="uk-margin-small uk-text-right">
                <el-button type="primary" native-type="submit">SAVE</el-button>
            </div>
        </form>
    </div>
</div>
@stop

@section('scripts')
<script>
    new Vue({
        el: '#content',

        data: {
            input: {
                userString: '',
                user: {}
            },
            resource: {
                users: []
            }
        },

        methods: {
            queryUser (queryString, cb) {
                axios.get(`${Laravel.url}/admin/json/users`, {
                    params: {
                        search: this.input.userString,
                        sort: ['name', 'asc'],
                        status: ['user', 'tester']
                    }
                }).then(response => {
                    this.resource.users = response.data.data.map(item => {
                        return {
                            value: item.name,
                            data: item
                        }
                    })

                    cb(this.resource.users)
                }).catch(error => {
                    if (error.response) {
                        this.$notify({
                            title: error.response.status,
                            message: error.response.statusText,
                            type: 'error'
                        })
                    }

                    console.log(error.message)
                })
            },
            onSelectUser (item) {
                this.input.user = item.data
            }
        }
    })
</script>
@stop