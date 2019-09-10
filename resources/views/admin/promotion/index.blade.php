@extends('admin._master')

@section('content')
<div id="content" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">PROMOTIONS</h3>
            </div>
            <div class="uk-width-auto">
                <div class="uk--tool-header-button">
                    <a href="{{ url('/admin/promotions/create') }}">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        @include('admin._alert')
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
            <thead>
                <tr>
                    <th width="20"></th>
                    <th>Code</th>
                    <th>Name</th>
                    <th>Status</th>
                    <th width="50">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($promotions as $i => $promotion)
                    <tr>
                        <td class="uk-text-center">
                            <a href="#" @click.prevent="showDetail({{ $i }})">
                                <i v-if="items[{{ $i }}] && !items[{{ $i }}].detail" class="fas fa-chevron-right"></i>
                                <i v-if="items[{{ $i }}] && items[{{ $i }}].detail" class="fas fa-chevron-down"></i>
                            </a>
                        </td>
                        <td>{{ $promotion->code }}</td>
                        <td>
                            <a href="{{ url('/admin/promotions/'.$promotion->id) }}">{{ $promotion->name }}</a>
                        </td>
                        <td>
                            @if ($promotion->active)
                                <span class="el-tag el-tag--success el-tag--small">Active</span>
                            @else
                                <span class="el-tag el-tag--danger el-tag--small">Inctive</span>
                            @endif
                        </td>
                        <td class="uk-text-center">
                            <a href="{{ url('/admin/promotions/'.$promotion->id.'/edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                    <tr v-show="items.length > 0 && items[{{ $i }}].detail">
                        <td></td>
                        <td colspan="4">
                            <ul class="uk-list">
                                @foreach ($promotion->packages as $package)
                                    <li>
                                        {{ $package->display_name }}
                                        <small class="uk-text-primary">{{ $package->region->name }}</small>
                                    </li>
                                @endforeach
                            </ul>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="uk-margin-top uk-text-center">
            {{ $promotions->links('vendor.pagination.element-ui') }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Vue({
        el: '#content',

        data: {
            items: []
        },

        methods: {
            setItems() {
                let items = JSON.parse('{!! json_encode($promotions->items()) !!}')

                items.forEach((item, index) => {
                    items[index].detail = false  
                })

                this.items = items
            },
            showDetail(index) {
                if (this.items[index].packages == 0) return

                this.items[index].detail = !this.items[index].detail
            }  
        },

        mounted() {
            this.setItems()
        }
    })
</script>
@endsection