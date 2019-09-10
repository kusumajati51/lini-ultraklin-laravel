@extends('admin._master')

@section('content')
<div id="content" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <a class="uk--tool-header-back" href="{{ url('/admin/menu') }}">
                    <i class="fas fa-angle-left fa-lg"></i>
                </a>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title uk-text-uppercase">MENU DETAIL <small>({{ $menu->display_name }})</small></h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <div>
            <div class="uk-margin-small">
                <span class="uk--box-label">Name</span>
                <span class="uk--box-text">{{ $menu->name }}</span>
            </div>
            <div class="uk-margin-small">
                <span class="uk--box-label">Display Name</span>
                <span class="uk--box-text">{{ $menu->display_name }}</span>
            </div>
            <div class="uk-margin-small">
                <span class="uk--box-label">Target</span>
                <span class="uk--box-text">{{ $menu->target }}</span>
            </div>
            <div class="uk-margin-small">
                <span class="uk--box-label">Description</span>
                <span class="uk--box-text">{{ $menu->description }}</span>
            </div>
            <div class="uk-margin-small">
                <span class="uk--box-label">Active</span>
                @if ($menu->active)
                    <el-tag type="success" size="small">TRUE</el-tag>
                @else
                    <el-tag type="danger" size="small">FALSE</el-tag>
                @endif
            </div>
        </div>
        <hr>
        <div id="sortable">
            <div v-for="(item, index) in items" class="uk-grid-small" uk-grid>
                <input class="item-id" type="hidden" :value="item.id">
                <div class="item-handle uk-width-auto uk-flex uk-flex-middle" style="cursor: move;">
                    <i class="fas fa-bars"></i>
                </div>
                <div class="uk-width-auto">
                    <img :src="`{{ url('/img/small') }}/${item.icon}`">
                </div>
                <div class="uk-width-expand">
                    <div>
                        <div class="uk-margin-small">
                            <span class="uk--box-label">Label</span>
                            <span class="uk--box-text">@{{ item.label }}</span>
                        </div>
                        <div class="uk-margin-small">
                            <span class="uk--box-label">Link</span>
                            <span class="uk--box-text">@{{ item.link }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset('js/lodash.min.js') }}"></script>
<script>
    new Vue({
        el: '#content',
        
        data: {
            items: JSON.parse('{!! json_encode($items) !!}')
        },

        methods: {
            updateOrder(fn) {
                let self = this

                setTimeout(() => {
                    let elements = document.getElementsByClassName('item-id')

                    Array.from(elements).forEach((item, i) => {
                        let index = _.findIndex(self.items, object => {
                            return object.id == item.value
                        })

                        self.items[index].order = i + 1

                        console.log(self.items[index].order +' :: '+ self.items[index].link)
                    })

                    return fn()
                }, 1000)

            }
        },

        mounted() {
            let self = this

            let sortable = UIkit.sortable('#sortable', {
                handle: '.item-handle'
            })

            $(document).on('moved', sortable, (e) => {
                self.updateOrder(() => {
                    axios.patch(`${Laravel.url}/admin/json/menu/{{ $menu->id }}/sort-items`, {
                        items: self.items
                    })
                    .then(({ data }) => {
                        if (data.error) {
                            this.$notify({
                                title: 'Error',
                                message: data.message,
                                type: 'error'
                            })

                            return
                        }

                        this.$notify({
                            title: 'Success',
                            message: data.message,
                            type: 'success'
                        })
                    })
                    .catch(error => {
                        this.$notify({
                            title: error.response.status,
                            message: error.response.statusText,
                            type: 'error'
                        })
                    })
                })
            })
        }
    })
</script>
@endsection