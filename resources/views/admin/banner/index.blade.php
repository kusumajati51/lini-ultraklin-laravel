@extends('admin._master')

@section('content')
<div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-image"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">BANNERS</h3>
            </div>
            <div class="uk-width-auto">
                <div class="uk--tool-header-button">
                    <a href="{{ url('/admin/banners/create') }}">
                        <i class="fas fa-plus"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        @include('admin._alert')
        <table class="uk-table uk-table-divider uk-table-small uk-table-middle uk-text-small">
            <thead>
                <tr>
                    <th width="100px">Image</th>
                    <th>Name</th>
                    <th>Region</th>
                    <th>Status</th>
                    <th width="50px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($banners as $banner)
                    <tr>
                        <td>
                            <img src="{{ url('/images/banners/100/'.$banner->file) }}">
                        </td>
                        <td>
                            <div>{{ $banner->name }}</div>
                            @if ($banner->target == 'web')
                                <span class="uk-label uk-label-warning">{{ $banner->target }}</span>
                            @elseif ($banner->target == 'app')
                                <span class="uk-label uk-label-primary">{{ $banner->target }}</span>
                            @else
                                <span class="uk-label uk-label-success">All</span>
                            @endif
                        </td>
                        <td>{{ $banner->region ? $banner->region->name : 'All' }}</td>
                        <td>
                            @if ($banner->active)
                                <span class="el-tag el-tag--success el-tag--small">Active</span>
                            @else
                                <span class="el-tag el-tag--danger el-tag--small">Inctive</span>
                            @endif
                        </td>
                        <td class="uk-text-center">
                            <a href="{{ url('/admin/banners/'.$banner->id.'/edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="uk-margin-top uk-text-center">
            {{ $banners->links('vendor.pagination.element-ui') }}
        </div>
    </div>
</div>
@endsection