@extends('admin._master')

@section('content')
<div id="edit-promotion" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <a class="uk--tool-header-back" href="{{ url('/admin/promotions') }}">
                    <i class="fas fa-angle-left fa-lg"></i>
                </a>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">EDIT PROMOTION</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form action="{{ url('/admin/promotions/'.$promotion->id) }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <input v-for="package in input.packages" type="hidden" name="packages[]" :value="package">
            <input v-model="input.target" type="hidden" name="target">
            <div class="uk-margin">
                <label class="uk-form-label">Package</label>
                <div>
                    <el-select v-model="input.packages" class="uk-width-1-1" placeholder="Choose package" multiple>
                        <el-option-group v-for="group in optionGroups.package" :label="group.label">
                            <el-option v-for="item in group.options" :value="item.value" :label="item.label"></el-option>
                        </el-option-group>
                    </el-select>
                </div>
                {!! $errors->first('service', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Code</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="code" value="{{ old('code') ? old('code') : $promotion->code }}">
                </div>
                {!! $errors->first('code', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Name</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="name" value="{{ old('name') ? old('name') : $promotion->name }}">
                </div>
                {!! $errors->first('name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Minimal Order</label>
                <div class="el-input">
                    <input class="el-input__inner" type="number" name="min_order" value="{{ old('min_order') ? old('min_order') : $promotion->min_order }}">
                </div>
                {!! $errors->first('min_order', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Value</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="value" value="{{ old('value') ? old('value') : $promotion->value }}">
                </div>
                {!! $errors->first('value', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label uk-margin-small-right">Daily</label>
                <el-switch v-model="input.daily"></el-switch>
                <div>
                    <el-checkbox-group v-model="input.selectedDays" :disabled="!input.daily">
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
                        v-if="input.daily"
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
                    <div>
                        <el-select v-model="input.target" class="uk-width-1-1" placeholder="Select promotion target">
                            <el-option-group v-for="group in optionGroups.target" :label="group.label">
                                <el-option v-for="item in group.options" :value="item.value" :label="item.label"></el-option>
                            </el-option-group>
                        </el-select>
                    </div>
                </div>
                {!! $errors->first('target', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Description</label>
                <div class="el-textarea">
                    <textarea class="el-textarea__inner" name="description">{{ old('description') ? old('description') : $promotion->description }}</textarea>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label uk-margin-small-right">Active</label>
                <el-switch v-model="input.active" name="active"></el-switch>
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
        el: '#edit-promotion',

        data: {
            input: {
                packages: JSON.parse('{{ json_encode($promotion->packages) }}'),
                daily: {{ json_encode($promotion->daily) }},
                selectedDays: {!! is_null($promotion->day) ? json_encode([]) : json_encode($promotion->day) !!},
                time: [
                    new Date({{ $promotion->time_start * 1000 }}),
                    new Date({{ $promotion->time_end * 1000 }})
                ],
                target: '{{ old("target") ? old("target") : $promotion->target }}',
                active: {{ $promotion->active ? json_encode(true) : json_encode(false) }}
            },
            optionGroups: JSON.parse('{!! json_encode($optionGroups) !!}')
        }
    })
</script>
@endsection