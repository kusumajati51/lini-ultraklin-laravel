@extends('admin._master')

@section('content')
<div class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <div class="uk--tool-header-icon">
                    <i class="fas fa-tags"></i>
                </div>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">PROMOTION DETAIL</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <div class="uk-margin-small">
            <div class="uk--box-label">Code</div>
            <div class="uk--box-text">{{ $promotion->code }}</div>
        </div>
        <div class="uk-margin-small">
            <div class="uk--box-label">Name</div>
            <div class="uk--box-text">{{ $promotion->name }}</div>
        </div>
        <div class="uk-margin-small">
            <div class="uk--box-label">Minimal Order</div>
            <div class="uk--box-text">{{ ($promotion->min_order) ? $promotion->min_order : '-' }}</div>
        </div>
        <div class="uk-margin-small">
            <div class="uk--box-label">Value</div>
            <div class="uk--box-text">{{ $promotion->value }}</div>
        </div>
        @if (!is_null($promotion->day))
            <div class="uk-margin-small">
                <div class="uk--box-label">Day</div>
                <div>
                    @foreach ($promotion->day as $day)
                        <span class="el-tag el-tag--small">{{ $day }}</span>
                    @endforeach
                </div>
            </div>
        @endif
        <div class="uk-margin-small">
            <div class="uk--box-label">Time Start</div>
            @if ($promotion->daily)
                <div class="uk--box-text">{{ date('H:i', $promotion->time_start) }}</div>
            @else
                <div class="uk--box-text">{{ date('d-m-Y', $promotion->time_start) }}</div>
            @endif
        </div>
        <div class="uk-margin-small">
            <div class="uk--box-label">Time End</div>
            @if ($promotion->daily)
                <div class="uk--box-text">{{ date('H:i', $promotion->time_end) }}</div>
            @else
                <div class="uk--box-text">{{ date('d-m-Y', $promotion->time_end) }}</div>
            @endif
        </div>
        <div class="uk-magin-small">
            <div class="uk--box-label">Target</div>
            <div class="uk--box-text">{{ $promotion->target_name }}</div>
        </div>
        <div class="uk-margin-small">
            <div class="uk--box-label">Description</div>
            <div class="uk--box-text">{{ $promotion->description ? $promotion->description : '-' }}</div>
        </div>
        <div class="uk-margin-small">
            <div class="uk--box-label">Status</div>
            <div>
                @if ($promotion->active)
                    <span class="el-tag el-tag--success el-tag--small">Active</span>
                @else
                    <span class="el-tag el-tag--danger el-tag--small">Inctive</span>
                @endif
            </div>
        </div>
        <div>
            <table class="uk-table uk-table-small uk-table-divider uk-text-small">
                <thead>
                    <tr>
                        <th>Package</th>
                        <th>Region</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($promotion->packages as $package)
                        <tr>
                            <td>{{ $package->display_name }}</td>
                            <td>{{ $package->region->name }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection