<template>
    <div class="px-1 py-1 bg-white border-b border-r border-l flex-1">
        <div class="flex flex-col gap-1">
            <h5 v-text="`Point ${itemIndex + 1}`" class="text-xs mb-0"></h5>
            <h6 v-text="item.data.heading" class="break-all" style="font-size:10px;"></h6>
            <div class="flex justify-between w-full--">
                <a href="#" @click.prevent="edit" class="text-xs" v-tooltip="'Edit Point Data'">
                    <svg-icon name="form" class="w-4 h-4" />
                </a>
                <a href="#" @click.prevent="remove" class="text-xs" v-tooltip="'Delete Point'">
                    <svg-icon name="trash" class="w-4 h-4" />
                </a>
            </div>
        </div>
        <modal
            v-if="modalOpen"
            name="updater-composer-output"
            :overflow="false"
            width="75%"
        >
            <div class="p-3 relative">
                <button
                    class="btn-close absolute top-0 right-0 mt-2 mr-2"
                    :aria-label="__('Close')"
                    @click="modalOpen = false"
                    v-html="'&times'" />
                <div class="content mt-0 mb-2">
                    <h2>Content</h2> <p class="max-w-lg">Edit your pinpoints content here:</p>
                </div>
                <div>
                    <label class="text-base font-bold mb-1">Heading</label>
                    <input type="text" v-model="item.data.heading" class="input-text mb-2">

                    <div class="flex flex-col gap-2">
                      <h3 class="text-base font-bold">Page entry</h3>

                      <select v-model="item.data.entries" @change="onSelectionChange" class="py-1">
                        <option v-for="option in createEntriesObject()" v-bind:value="option.value">
                            {{ option.label }}
                          </option>
                      </select>
                    </div>

                    <div class="flex flex-col gap-2 mt-2">
                      <p class="text-base font-bold">Marker color</p>

                      <color-fieldtype 
                        v-model="item.data.color"
                        :isReadOnly="false"
                        :config="colorConfig"
                      ></color-fieldtype>
                    </div>

                    <div v-if="hasFields">
                        <label class="text-base font-bold mb-1">Fields</label>
                        <div
                            class="w-full pb-4"
                            v-for="(field, fIndex) in item.data.fields"
                            :key="fIndex"
                        >
                            <component
                                :is="`${field.value}-fieldtype`"
                                :value.sync="field.content"
                                :handle="`${field.value}_field`"
                                name-prefix=""
                                error-key-prefix=""
                                :read-only="false"
                                deed-eded="$emit('input', $event)"
                                @input="updateFieldContent($event, field, fIndex)"
                                @meta-updated="$emit('meta-updated', $event)"
                                @focus="focused"
                            />
                            <div class="flex justify-end mt-1">
                                <a href="#" @click.prevent="removeField(field, fIndex)" class="text-xs" v-tooltip="'Delete field'">
                                    <svg-icon name="trash" class="w-4 h-4" />
                                </a>
                            </div>
                        </div>
                    </div>

                </div>
                <div class="mt-5">
                    <div v-if="isSelectingNewFieldtype">
                        <div class="fieldtype-selector">
                            <div class="fieldtype-list">
                                <div class="p-1" v-for="(fieldtype, findex) in fieldtypes" :key="findex">
                                    <button
                                        class="bg-white border border-grey-50 flex items-center group w-full rounded hover:border-grey-60 shadow-sm hover:shadow-md pr-1.5"
                                        @click="select(fieldtype)"
                                    >
                                        <div class="p-1 flex items-center border-r border-grey-50 group-hover:border-grey-60 bg-grey-20 rounded-l">
                                            <svg-icon class="h-5 w-5 text-grey-80" :name="fieldtype.icon" default="generic-field"></svg-icon>
                                        </div>
                                        <span class="pl-1.5 text-grey-80 text-md group-hover:text-grey-90">{{ fieldtype.text }}</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex flex-row justify-between">
                        <button class="btn w-auto flex justify-center items-center" @click="isSelectingNewFieldtype = true;">
                            <svg-icon name="wireframe" class="mr-1 w-4 h-4" />
                            {{ __('Add Field') }}
                        </button>
                        <button class="btn-primary w-auto ml-auto flex justify-center items-center" @click="modalOpen = false">
                            {{ __('Close') }}
                        </button>
                    </div>
                </div>
            </div>
        </modal>
    </div>
</template>
<script>
export default {
    props: {
        item: {
            type: Object,
            default: () => ({
                x: 0,
                y: 0,
                data: {
                    heading: '',
                    fields: [],
                    entries: [],
                    color: '',
                },
            })
        },
        itemIndex: {
            type: Number,
            default: 0
        },
        entries: {
            type: Array,
            default: []
        },
    },
    data() {
        return {
            isSelectingNewFieldtype: false,
            modalOpen: false,
            heading: this.item.data.heading,
            fields: this.item.data.fields,
            entries: this.entries,
            selected: null,
            fieldtypes: [
                { icon:"entries", text:"entries", value:"link", content:[] },
                { icon:"markdown", text:"Markdown", value:"markdown", content:'' },
                { icon:"text", text:"Text", value:"text", content:'' },
                { icon:"textarea", text:"Textarea", value:"textarea", content:'' }
            ],
            colorConfig: {
              lock_opacity: true,
              swatches: ['#f44336', '#e91e63', '#9c27b0'],
              theme: 'classic',
              default_color_mode: 'HEX',
              default: '#f44336',
              color_modes: ['hex', 'rgba']
            },
            colorValue: '#f44336',
        }
    },
    mounted() {
      // console.log(this.selected)
    },
    computed: {
        hasFields() {
            return (this.item.data.fields !== undefined && this.item.data.fields.length > 0)
        }
    },
    methods: {
        select(fieldType) {
            if(this.item.data.fields === undefined) {
                this.item.data.fields = []
            }
            this.item.data.fields.push(this.cleanObject(fieldType))
            this.isSelectingNewFieldtype = false
        },
        updateFieldContent($event, field, fIndex) {
            field.content = $event
        },
        edit() {
            this.modalOpen = true
        },
        removeField(field, fIndex) {
            this.item.data.fields = this.item.data.fields.filter(item => item.content !== field.content)
        },
        remove() {
            if (confirm('Are you sure?')) {
                this.$emit('delete', this.index);
            }
        },
        cleanObject(obj) {
            return JSON.parse(JSON.stringify(obj))
        },
        createEntriesObject() {
          return this.entries.map((entry) => {
            return {
              'label': entry.title,
              'value': entry.url,
            }
          })
        },
        onSelectionChange() {
          console.log(this.item.data.entries)
        },
    }
}
</script>
