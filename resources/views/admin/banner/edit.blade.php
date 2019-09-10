@extends('admin._master')

@section('content')
<div id="edit-banner" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <a class="uk--tool-header-back" href="{{ url('/admin/banners') }}">
                    <i class="fas fa-angle-left fa-lg"></i>
                </a>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">EDIT BANNER</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form action="{{ url('/admin/banners/'.$banner->id) }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <input type="hidden" name="region" :value="input.region">
            <input type="hidden" name="target" :value="input.target">
            <div class="uk-margin uk-text-center">
                <img src="{{ url('/images/banners/960/'.$banner->file) }}">
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Image</label>
                <div>
                    <input type="file" name="image">
                </div>
                {!! $errors->first('image', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Name</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="name" value="{{ old('name') ? old('name') : $banner->name }}">
                </div>
                {!! $errors->first('name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Description</label>
                <div class="el-textarea">
                    <textarea class="el-textarea__inner" name="description">{{ old('description') ? old('description') : $banner->description }}</textarea>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Region</label>
                <div>
                    <el-select v-model="input.region" class="uk-width-1-1">
                        <el-option label="All" value=""></el-option>
                        <el-option v-for="item in options.region" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Target</label>
                <div>
                    <el-select v-model="input.target" class="uk-width-1-1">
                        <el-option v-for="item in options.target" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </div>
            </div>
            <div class="uk-margin">
                <label class="uk-form-label uk-margin-small-right">Active</label>
                <el-switch v-model="input.active" name="active"></el-switch>
                {!! $errors->first('active', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
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
        el: '#edit-banner',

        data: {
            input: {
                region: '',
                target: '',
                active: {{ $banner->active ? json_encode(true) : json_encode(false) }}
            },
            options: {
                region: JSON.parse('{!! $options["region"] !!}'),
                target: [
                    {
                        label: 'All',
                        value: ''
                    },
                    {
                        label: 'App',
                        value: 'app'
                    },
                    {
                        label: 'Web',
                        value: 'web'
                    }
                ]
            }
        },

        mounted() {
            this.input.region = {!! is_null($banner->region_id) ? "''" : $banner->region_id !!}
            this.input.target = '{{ $banner->target }}'
        }
    })
</script>
@endsection