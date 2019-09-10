@extends('admin._master')

@section('content')
<div id="page--officer-create" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <a class="uk--tool-header-back" href="{{ url('/admin/officers') }}">
                    <i class="fas fa-angle-left fa-lg"></i>
                </a>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">EDIT OFFICER</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form action="{{ url('/admin/officers/'.$officer->id) }}" method="POST">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <input v-for="value in input.region" type="hidden" name="region[]" :value="value">
            <input type="hidden" name="gender" :value="input.gender">
            <input type="hidden" name="role" :value="input.role">
            <div class="uk-margin">
                <label class="uk-form-label">Name</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="name" value="{{ old('name', $officer->name) }}">
                </div>
                {!! $errors->first('name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Gender</label>
                <div>
                    <el-select v-model="input.gender" class="uk-width-1-1" placeholder="Choose gender">
                        <el-option v-for="item in options.gender" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </div>
                {!! $errors->first('gender', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Phone</label>
                <div class="el-input">
                    <input class="el-input__inner" type="text" name="phone" value="{{ old('phone', $officer->phone) }}">
                </div>
                {!! $errors->first('phone', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Email</label>
                <div class="el-input">
                    <input class="el-input__inner" type="email" name="email" value="{{ old('email', $officer->email) }}">
                </div>
                {!! $errors->first('email', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Password <small>(leave blank if not change)</small></label>
                <div class="el-input">
                    <input class="el-input__inner" type="password" name="password">
                </div>
                {!! $errors->first('password', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Role</label>
                <div>
                    <el-select v-model="input.role" class="uk-width-1-1" placeholder="Choose role">
                        <el-option v-for="item in options.role" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </div>
                {!! $errors->first('role', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Region</label>
                <div>
                    <el-select v-model="input.region" class="uk-width-1-1" placeholder="Choose region" multiple>
                        <el-option v-for="item in options.region" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </div>
                {!! $errors->first('region', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
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
        el: '#page--officer-create',

        data: {
            input: {
                region: JSON.parse('{!! json_encode(old("region", $officer->regions()->pluck("id"))) !!}'),
                gender: '{{ old("gender", $officer->gender) }}',
                role: {{ old('role', $officer->role_id) == '' ? $officer->role_id : old('role', $officer->role_id) }},
            },
            options: JSON.parse('{!! json_encode($options) !!}')
        }
    })
</script>
@endsection