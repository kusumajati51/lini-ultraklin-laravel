@extends('admin._master')

@section('content')
<div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-globe"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">REGIONS</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($regions as $region)
                    <tr>
                        <td>{{ $region->code }}</td>
                        <td><a href="{{ url('/admin/regions/'.$region->code) }}">{{ $region->name }}</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="uk-margin-top uk-text-center">
            {{ $regions->links('vendor.pagination.element-ui') }}
        </div>
    </div>
</div>
@endsection