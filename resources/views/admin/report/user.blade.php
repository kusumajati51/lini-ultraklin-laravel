@extends('admin._master_no_sidebar')

@section('content')
<div id="content" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <a href="{{ url('/admin') }}">
                        <i class="fas fa-chevron-left"></i>
                    </a>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">USER REPORT<sup class="el-badge__content">{{ $users->count() }}</sup></h3>
            </div>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <a class="uk-link-reset" href="#" @click.prevent="activeFilter">
                        <i class="fas fa-filter"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <div>
            <form method="GET">
                <input type="hidden" name="date_mode" :value="filter.dateMode">
                <div class="uk-grid-small" uk-grid>
                    <div class="uk-width-expand">
                        <el-radio-group v-model="filter.dateMode">
                            <el-radio-button label="daily">Daily</el-radio-button>
                            <el-radio-button label="range">Range</el-radio-button>
                        </el-radio-group>
                    </div>
                    <div class="uk-width-auto">
                        <div>
                            <el-date-picker v-if="filter.dateMode == 'daily'"
                                v-model="filter.date"
                                type="date"
                                name="date"
                                placeholder="Pick a day">
                            </el-date-picker>
                        </div>
                        <div>
                            <el-date-picker v-if="filter.dateMode == 'range'"
                                v-model="filter.daterange"
                                type="daterange"
                                name="daterange"
                                start-placeholder="Start date"
                                end-placeholder="End date">
                            </el-date-picker>
                        </div>
                    </div>
                </div>
                <div class="uk-margin uk-text-right">
                    <a class="el-button el-button--success el-button--small" href="{{ request()->fullUrl() }}{{ request()->query() ? '&export=xlsx' : '?export=xlsx'  }}">
                        <i class="fas fa-file-excel"></i>
                    </a>
                    <el-button type="primary" native-type="submit" size="small">Filter</el-button>
                </div>
            </form>
        </div>
        <table class="uk-table uk-table-small uk-table-divider uk-text-small">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name }}</td>
                        <td>{{ $user->phone }}</td>
                        <td>{{ $user->email }}</td>
                        <td>{{ $user->last_location }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection

@section('scripts')
<script>
    var filter = {
        dateMode: '{{ $filter["date_mode"] }}',
        time: JSON.parse('{!! json_encode($filter["time"]) !!}')
    }

    new Vue({
        el: '#content',

        data: {
            filter: {
                dateMode: filter.dateMode,
                date: Date.parse(filter.time[0]),
                daterange: [Date.parse(filter.time[0]), Date.parse(filter.time[1])],
            }
        }
    })
</script>
@endsection