@extends('admin._master')

@section('content')
<div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-cubes"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">PACKAGES <small>({{ $service->display_name }})</small></h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <table class="uk-table uk-table-divider uk-table-small uk-text-small">
            <thead>
                <tr>
                    <th>Name</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($packages as $package)
                    <tr>
                        <td>
                            <a href="{{ url('/admin/packages/'.$package->id.'/items') }}">{{ $package->display_name }}</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class="uk-margin-top uk-text-center">
            {{ $packages->links('vendor.pagination.element-ui') }}
        </div>
    </div>
</div>
@endsection