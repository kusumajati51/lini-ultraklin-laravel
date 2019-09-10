@extends('admin._master')

@section('content')
<div id="item-add" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <a class="uk--tool-header-back" href="{{ url('/admin/packages/'.$package->id.'/items') }}">
                    <i class="fas fa-angle-left fa-lg"></i>
                </a>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">ADD ITEM <small>({{ $package->display_name }})</small></h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form action="{{ url('/admin/packages/'.$package->id.'/items') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="item" value="{{ old('item') }}">
            <div class="uk-margin">
                <label class="uk-form-label">Item</label>
                <div>
                    <el-autocomplete
                        class="uk-width-1-1"
                        v-model="item"
                        :fetch-suggestions="querySearch"
                        placeholder="Please Input"
                        :trigger-on-focus="false"
                        name="item_name"
                        @select="handleSelect"></el-autocomplete>
                </div>
                {!! $errors->first('item', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Price</label>
                <div class="el-input">
                    <input class="el-input__inner" type="number" name="price">
                </div>
                {!! $errors->first('price', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin uk-text-right">
                <button class="el-button el-button--primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Vue({
        el: '#item-add',

        data: {
            item: '{{ old('item_name') }}'
        },

        methods: {
            querySearch(queryString, cb) {
                axios.get(`${Laravel.url}/admin/items/list`, {
                    params: {
                        search: queryString
                    }
                })
                .then(({ data }) => {
                    let result = [];

                    data.forEach(item => {
                        result.push({
                            value: item.name,
                            id: item.id
                        })
                    })

                    cb(result)
                })
                .catch(error => {
                    this.$notify({
                        title: error.response.status,
                        message: error.response.statusText,
                        type: 'error'
                    })
                })
            },

            handleSelect(item) {
                document.querySelector('input[name=item]').setAttribute('value', item.id)
            }
        }
    })
</script>
@endsection