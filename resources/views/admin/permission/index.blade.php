@extends('admin._master')

@section('content')
<div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">PERMISSIONS</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <table class="uk-table uk-table-small uk-text-small">
            <tbody>
                @foreach ($permissionGroups as $key => $permissions)
                    <tr>
                        <th colspan="2">{{ $key }}</th>
                    </tr>
                    @foreach ($permissions as $permission)
                    <tr>
                        <td width="5"></td>
                        <td>{{ $permission->display_name }}</td>
                    </tr>
                    @endforeach
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection