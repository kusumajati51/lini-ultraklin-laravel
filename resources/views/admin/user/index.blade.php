@extends('admin._master')

@section('content')
<div id="page--user" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-users"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">USERS</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <div>
            <form method="GET">
                <input v-for="item in filter.status" type="hidden" name="status[]" :value="item">
                <div class="uk-grid uk-grid-small" uk-grid>
                    <div class="uk-width-1-3">
                        <el-select v-model="filter.status" class="uk-width-1-1" placeholder="Status" multiple>
                            <el-option v-for="item in option.status"
                                :key="item.value"
                                :label="item.text"
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
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->status }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="uk-margin-top uk-text-center">
            {{ $users->links('vendor.pagination.element-ui') }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var filter = {
        status: JSON.parse('{!! json_encode($filter["status"]) !!}'),
    }

    new Vue({
        el: '#page--user',

        data: {
            filter: {
                status: filter.status,
                search: ''
            },
            option: {
                status: [
                    {
                        value: 'user',
                        text: 'User'
                    },
                    {
                        value: 'tester',
                        text: 'Tester'
                    }
                ]
                
            }
        }
    })
</script>
@endsection