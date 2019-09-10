@extends('admin._master')

@section('content')
<div id="page--menu-create" class="uk-card uk-card-default uk-card-small">
    <div class="uk-card-header uk-padding-remove">
        <div class="uk--tool-header uk-grid-collapse" uk-grid>
            <div class="uk-width-auto">
                <a class="uk--tool-header-back" href="{{ url('/admin/menu') }}">
                    <i class="fas fa-angle-left fa-lg"></i>
                </a>
            </div>
            <div class="uk-width-expand">
                <h3 class="uk--tool-header-title">NEW MENU</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form action="{{ url('/admin/menu') }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            <input v-model="input.target" type="hidden" name="target">
            <div class="uk-margin">
                <label class="uk-form-label">Name</label>
                <el-input name="name" value="{{ old('name') }}"></el-input>
                {!! $errors->first('name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Display Name</label>
                <el-input name="display_name" value="{{ old('display_name') }}"></el-input>
                {!! $errors->first('display_name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Description</label>
                <el-input type="textarea" name="description" value="{{ old('description') }}"></el-input>
                {!! $errors->first('description', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Target</label>
                <div>
                    <el-select v-model="input.target" class="uk-width-1-1">
                        <el-option v-for="item in options.target" :label="item.label" :value="item.value"></el-option>
                    </el-select>
                </div>
            </div>
            <h6 class="uk-heading-line uk-text-center"><span>Menu Items</span></h6>
            <div v-for="(item, index) in input.menuItems" class="uk-grid-small" uk-grid>
                <div class="uk-width-auto">
                    <div class="uk-inline uk-cover-container" style="width:128px; height:128px; border: dashed 1px #dcdfe6; cursor: pointer;">
                        <img :id="`icon-preview-${index}`" uk-cover>
                        <canvas width="128" height="128"></canvas>
                        <div class="uk-overlay uk-position-cover uk-flex uk-flex-center uk-flex-middle" @click.prevent="openFile(index)">
                            <a class="uk-link-reset"><i class="el-icon-plus"></i></a>
                        </div>
                    </div>
                    <input type="file" :name="`items[${index}][icon]`" @change="previewIcon($event, index)" style="display:none;">
                </div>
                <div class="uk-width-expand">
                    <div class="uk-margin-small">
                        <el-input v-model="input.menuItems[index].label" :name="`items[${index}][label]`" placeholder="Label" size="small"></el-input>
                    </div>
                    <div class="uk-margin-small">
                        <el-input v-model="input.menuItems[index].link" :name="`items[${index}][link]`" placeholder="Link" size="small"></el-input>
                    </div>
                    <div v-if="index != 0" class="uk-margin-small uk-text-right">
                        <el-button type="danger" size="small" @click="removeItem(index)"><i class="fas fa-trash"></i></el-button>
                    </div>
                </div>
            </div>
            <div class="uk-margin uk-text-right">
                <el-button type="success" @click="addItem">Add Item</el-button>
                <button class="el-button el-button--primary" type="submit">Save</button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
<script>
    new Vue({
        el: '#page--menu-create',

        data: {
            input: {
                target: '',
                menuItems: []
            },
            options: {
                target: [
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

        methods: {
            openFile(index) {
                document.getElementsByName(`items[${index}][icon]`)[0].click()
            },
            previewIcon(event, index) {
                let reader = new FileReader()

                reader.onload = (e) => {
                    document.getElementById(`icon-preview-${index}`).setAttribute('src', e.target.result)
                }

                if (typeof event.target.files[0] == 'undefined') return

                reader.readAsDataURL(event.target.files[0])
            },
            addItem() {
                this.input.menuItems.push({
                    icon: '',
                    label: '',
                    link: ''
                })
            },
            removeItem(index) {
                this.input.menuItems.splice(index, 1)
            }
        },

        mounted() {
            this.input.target = '{{ old("target", "app") }}'
            this.input.menuItems = JSON.parse('{!! json_encode(old("items", [[ "icon" => "", "label" => "", "link" => "" ]])) !!}')
        }
    })
</script>
@endsection