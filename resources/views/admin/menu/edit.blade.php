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
                <h3 class="uk--tool-header-title">EDIT MENU</h3>
            </div>
        </div>
    </div>
    <div class="uk-card-body">
        <form action="{{ url('/admin/menu/'.$menu->id) }}" method="POST" enctype="multipart/form-data">
            {{ csrf_field() }}
            {{ method_field('PATCH') }}
            <input v-model="input.target" type="hidden" name="target">
            <div class="uk-margin">
                <label class="uk-form-label">Name</label>
                <el-input name="name" value="{{ old('name', $menu->name) }}"></el-input>
                {!! $errors->first('name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Display Name</label>
                <el-input name="display_name" value="{{ old('display_name', $menu->display_name) }}"></el-input>
                {!! $errors->first('display_name', '<p class="uk-margin-small uk-text-danger uk-text-small">:message</p>') !!}
            </div>
            <div class="uk-margin">
                <label class="uk-form-label">Description</label>
                <el-input type="textarea" name="description" value="{{ old('description', $menu->description) }}"></el-input>
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
                <input type="hidden" :name="`items[${index}][id]`" :value="item.id">
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
                    <div v-if="index != 0 || input.menuItems.length > 1" class="uk-margin-small uk-text-right">
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
                    id: 0,
                    icon: '',
                    label: '',
                    link: ''
                })
            },
            removeItem(index) {
                this.$confirm('Are you sure to delete this item?')
                    .then(() => {
                        axios.post(`${Laravel.url}/admin/json/menu/{{$menu->id}}/items/${this.input.menuItems[index].id}`, {
                            _method: 'DELETE'
                        })
                        .then(({ data }) => {
                            if (data.error) {
                                this.$notify({
                                    title: 'Error',
                                    message: data.message,
                                    type: 'error'
                                })

                                return
                            }

                            this.$notify({
                                title: 'Success',
                                message: data.message,
                                type: 'success'
                            })

                            this.input.menuItems.splice(index, 1)
                        })
                        .catch(error => {
                            this.$notify({
                                title: error.response.status,
                                message: error.response.statusText,
                                type: 'error'
                            })
                        })
                    })
                    .catch(() => {})
            },
            previewCurrentIcon() {
                this.input.menuItems.forEach((item, index) => {
                    let reader = new FileReader()
                    let url = document.querySelector('meta[name=url]').getAttribute('content')
                    let imageUrl = `${url}/img/medium/${item.icon}`

                    document.getElementById(`icon-preview-${index}`).setAttribute('src', imageUrl)
                })
            }
        },

        mounted() {
            this.input.target = '{{ old("target", $menu->target) }}'
            this.input.menuItems = JSON.parse('{!! json_encode($menu->items) !!}')

            setTimeout(() => {
                this.previewCurrentIcon()
            }, 1000)
        }
    })
</script>
@endsection