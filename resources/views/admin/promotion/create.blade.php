@extends('admin._master')

@section('content')
<div id="new-promotion" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <a class="uk--tool-header-back" href="{{ url('/admin/promotions') }}">
                    <i class="fas fa-angle-left fa-lg"></i>
                </a>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">NEW PROMOTION</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form action="{{ url('/admin/promotions') }}" method="POST">
            {{ csrf_field() }}
            <input v-for="package in input.packages" type="hidden" name="packages[]" :value="package">
            <input v-model="input.target" type="hidden" name="target">
            <div class="uk-margin">
                <label class="uk-form-label">Package`s</label>
                <div>
                    <el-select v-model="input.packages" class="uk-width-1-1" placeholder="Choose package`s" multiple>
                        <el-option-group v-for="group in optionGroups.package" :label="group.label">
                            <el-option v-for="item in group.options" :value="item.value" :label="item.label"></el-option>
                        </el-option-group>
                    </el-select>
                </div>
                {!! $errors->first('packages', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Code</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="code" value="{{ old('code') }}">
                </div>
                {!! $errors->first('code', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Name</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="name" value="{{ old('name') }}">
                </div>
                {!! $errors->first('name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Minimal Order</label>
                <div class="el-input">
                    <input class="el-input__inner" type="number" name="min_order" value="{{ old('min_order') }}">
                </div>
                {!! $errors->first('min_order', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Value</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="value" value="{{ old('value') }}">
                </div>
                {!! $errors->first('value', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label uk-margin-small-right">Daily</label>
                <el-switch v-model="input.day"></el-switch>
                <div>
                    <el-checkbox-group v-model="input.selectedDays" :disabled="!input.day">
                        <el-checkbox label="Sun" name="day[]"></el-checkbox>
                        <el-checkbox label="Mon" name="day[]"></el-checkbox>
                        <el-checkbox label="Tue" name="day[]"></el-checkbox>
                        <el-checkbox label="Wed" name="day[]"></el-checkbox>
                        <el-checkbox label="Thu" name="day[]"></el-checkbox>
                        <el-checkbox label="Fri" name="day[]"></el-checkbox>
                        <el-checkbox label="Sat" name="day[]"></el-checkbox>
                    </el-checkbox-group>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Time</label>
                <div>
                    <el-time-picker
                        v-if="input.day"
                        v-model="input.time"
                        is-range
                        range-separator="To"
                        start-placeholder="Start time"
                        end-placeholder="End time"
                        format="HH:mm"
                        name="time"></el-time-picker>
                    <el-date-picker
                        v-else
                        v-model="input.time"
                        type="daterange"
                        range-separator="To"
                        start-placeholder="Start date"
                        end-placeholder="End date"
                        name="time"></el-date-picker>
                </div>
            </div>
            <div clastt="uk-margin">
                <label class="uk-form-label">Target</label>
                <div>
                    <el-select v-model="input.target" class="uk-width-1-1" placeholder="Select promotion target">
                        <el-option-group v-for="group in optionGroups.target" :label="group.label">
                            <el-option v-for="item in group.options" :value="item.value" :label="item.label"></el-option>
                        </el-option-group>
                    </el-select>
                </div>
                {!! $errors->first('target', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Description</label>
                <div class="el-textarea">
                    <textarea class="el-textarea__inner" name="description">{{ old('description') }}</textarea>
                </div>
            </div>
            <div class="uk-margin uk-text-right">
                <button class="el-button el-button--primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Vue({
        el: '#new-promotion',

        data: {
            input: {
                packages: JSON.parse('{!! json_encode(old("packages", [])) !!}'),
                daily: true,
                selectedDays: [],
                time: [new Date, new Date],
                target: '{{ old("target") }}'
            },
            optionGroups: JSON.parse('{!! json_encode($optionGroups) !!}')
        }
    })
</script>
@endsection