@extends('admin._master')

@section('content')
<div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-list-alt"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">ITEMS</h3>
            </div>
            <div class="uk-width-auto">
                <div class="uk--tool-header-button">
                    <a href="{{ url('/admin/items/create') }}">
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
                    <th>Name</th>
                    <th>Used</th>
                    <th width="50px">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($items as $item)
                    <tr>
                        <td>{{ $item->name }}</td>
                        <td>{{ $item->packages->count() }} Package(s)</td>
                        <td class="uk-text-center">
                            <a href="{{ url('/admin/items/'.$item->id.'/edit') }}">
                                <i class="fas fa-edit"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="uk-margin-top uk-text-center">
            {{ $items->links('vendor.pagination.element-ui') }}
        </div>
    </div>
</div>
@endsection