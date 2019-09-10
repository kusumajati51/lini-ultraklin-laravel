@extends('admin._master')

@section('content')
<div id="page--officer" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">OFFICERS</h3>
            </div>
            <div class="uk-width-auto">
                <div class="uk--tool-header-button">
                    <a href="{{ url('/admin/officers/create') }}"><i class="fas fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        @include('admin._alert')
        <div>
            <form method="GET">
                <input v-for="item in filter.region" type="hidden" name="region[]" :value="item">
                <div class="uk-grid uk-grid-small" uk-grid>
                    <div class="uk-width-1-3">
                        <el-select v-model="filter.region" class="uk-width-1-1" placeholder="Status" multiple>
                            <el-option v-for="item in option.region"
                                :key="item.value"
                                :label="item.label"
                                :value="item.value"></el-option>
                        </el-select>
                    </div>
                    <div class="uk-width-2-3">
                        <el-input v-model="filter.search" name="search" placeholder="Search">
                            <el-button slot="append" native-type="submit">
                                <i class="fas fa-search"></i>
                            </el-button>
                        </el-input>
                    </div>
                </div>
                <div class="uk-margin uk-text-right">
                    <button class="el-button el-button--primary el-button--small">Filter</button>
                </div>
            </form>
        </div>
        <div class="uk-overflow-auto">
            <table class="uk-table uk-table-divider uk-table-small uk-text-small">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Region</th>
                        <th width="50px">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($officers as $officer)
                        <tr>
                            <td>
                                <a href="{{ url('/admin/officers/'.$officer->id) }}">
                                    {{ $officer->name }}
                                </a>
                            </td>
                            <td>{{ $officer->phone }}</td>
                            <td>{{ $officer->email }}</td>
                            <td>{{ $officer->regions()->orderBy('name')->pluck('name')->implode(', ') }}</td>
                            <td class="uk-text-center">
                                <a href="{{ url('/admin/officers/'.$officer->id.'/edit') }}">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="uk-margin-top uk-text-center">
            {{ $officers->links('vendor.pagination.element-ui') }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var filter = {
        region: JSON.parse('{!! json_encode($filter["region"]) !!}'),
    }

    new Vue({
        el: '#page--officer',

        data: {
            filter: {
                region: filter.region,
                search: ''
            },
            option: JSON.parse('{!! json_encode($options) !!}')
        }
    })
</script>
@endsection