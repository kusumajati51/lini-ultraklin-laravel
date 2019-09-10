@extends('admin._master')

@section('content')
<div id="page--package-create" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <a class="uk--tool-header-back" href="{{ url('/admin/packages') }}">
                    <i class="fas fa-angle-left fa-lg"></i>
                </a>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">NEW PACKAGE</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form action="{{ url('/admin/packages') }}" method="POST">
            {{ csrf_field() }}
            <input type="hidden" name="region" :value="input.region">
            <input type="hidden" name="service" :value="input.service">
            <div class="uk-margin">
                <label class="uk-form-label">Region</label>
                <div>
                    <el-select v-model="input.region" class="uk-width-1-1" placeholder="Choose region">
                        <el-option v-for="item in options.region" :value="item.value" :label="item.label"></el-option>
                    </el-select>
                </div>
                {!! $errors->first('region', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Service</label>
                <div>
                    <el-select v-model="input.service" class="uk-width-1-1" placeholder="Choose service">
                        <el-option v-for="item in options.service" :value="item.value" :label="item.label"></el-option>
                    </el-select>
                </div>
                {!! $errors->first('service', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Name</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="name" value="{{ old('name') }}">
                </div>
                {!! $errors->first('name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                    <label class="uk-form-label">Display Name</label>
                    <div class="el-input">
                    <input class="el-input__inner" type="text" name="display_name" value="{{ old('display_name') }}">
                    </div>
                    {!! $errors->first('display_name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
                </div>
            <div class="uk-margin">
                <label class="uk-form-label">Description</label>
                <div class="el-textarea">
                    <textarea class="el-textarea__inner" name="description"></textarea>
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
        el: '#page--package-create',

        data: {
            input: {
                region: {!! old('region', "''") == '' ? "''" : old('region', "''") !!},
                service: {!! old('service', "''") == '' ? "''" : old('service', "''") !!}
            },
            options: JSON.parse('{!! json_encode($options) !!}')
        }
    })
</script>
@endsection