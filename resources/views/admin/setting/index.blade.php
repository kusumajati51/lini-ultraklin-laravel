@extends('admin._master')

@section('content')
<div id="page--setting" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">SETTINGS</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form>
            @foreach ($settings as $setting)
                @if ($setting->input_type == 'datepicker')
                <div class="uk-margin">
                    <label class="uk-form-label">{{ $setting->display_name }}</label>
                    <div>
                        <el-date-picker
                            v-model="input.close_service"
                            type="daterange"
                            name="daterange"
                            value-format="yyyy-MM-dd"
                            start-placeholder="Start date"
                            end-placeholder="End date"
                            size="small">
                        </el-date-picker>
                    </div>
                </div>
                @endif
            @endforeach
            <div class="uk-margin uk-text-right">
                <el-button type="primary" size="mini" @click="save">Save</el-button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/lodash.min.js') }}"></script>
<script>
    new Vue({
        el: '#page--setting',

        data: {
            input: []
        },

        methods: {
            setInput(settings) {
                let data = {}

                settings.forEach(setting => {
                    data[setting.name] = setting.value
                })

                this.input = data
            },
            save() {
                axios.post(`${Laravel.url}/admin/settings/ajax`, {
                    _method: 'PATCH',
                    _token: Laravel.token,
                    settings: this.input
                }).then(({ data }) => {
                    this.$notify.success({
                        title: 'Success',
                        message: data.message
                    })

                    this.setInput(data.data)
                }).catch(error => {
                    this.$notify.error({
                        title: error.response.status,
                        message: error.response.statusText
                    })
                })
            }
        },

        mounted() {
            let settings = JSON.parse('{!! json_encode($settings) !!}')
            
            this.setInput(settings)
        }
    })
</script>
@endsection