@extends('admin._master')

@section('content')
<div id="content" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-bars"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">MENU</h3>
            </div>
            <div class="uk-width-auto">
                <div class="uk--tool-header-button">
                    <a href="{{ url('/admin/menu/create') }}"><i class="fas fa-plus"></i></a>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        @include('admin._alert')
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Display Name</th>
                    <th>Target</th>
                    <th>Active</th>
                    <th class="uk-text-center" width="100">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($menu as $m)
                    <tr>
                        <td>{{ $m->name }}</td>
                        <td>
                            <a href="{{ url('/admin/menu/'.$m->name) }}">
                                {{ $m->display_name }}
                            </a>
                        </td>
                        <td>{{ $m->target }}</td>
                        <td>
                            @if ($m->active)
                                <el-tag type="success" size="small">TRUE</el-tag>
                            @else
                                <el-tag type="danger" size="small">FALSE</el-tag>
                            @endif
                        </td>
                        <td class="uk-text-center">
                            <a href="{{ url('/admin/menu/'.$m->id.'/edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="uk-margin-top uk-text-center">
            {{ $menu->links('vendor.pagination.element-ui') }}
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Vue({
        el: '#content'
    })
</script>
@endsection