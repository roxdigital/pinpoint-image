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
          <h2>Content</h2>
          <p class="max-w-lg">Edit your pinpoints content here:</p>
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

          <!-- FA Icons -->
          <div class="flex flex-col gap-2 mt-2">
            <p class="text-base font-bold">Icon</p>
            <select-input
              :options="iconOptions"
              v-model="item.data.icon"
            ></select-input>

            <relationship-input v-model="item.data.icon" :config="{items: iconOptions2}"></relationship-input>
          </div>


          <!-- Color picker -->
          <div class="flex flex-col gap-2 mt-2">
            <p class="text-base font-bold">Marker color</p>

            <color-fieldtype
              v-model="item.data.color"
              :isReadOnly="false"
              :config="colorConfig"
            ></color-fieldtype>
          </div>
        </div>

        <!-- Close button -->
        <div class="mt-2">
          <div class="flex flex-row justify-between">
            <button class="btn-primary w-auto ml-auto flex justify-center items-center" @click="modalOpen = false">
              {{ __("Close") }}
            </button>
          </div>
        </div>
      </div>
    </modal>
  </div>
</template>
<script>

export default {
  mixins: [Fieldtype],
  props: {
    item: {
      type: Object,
      default: () => ({
        x: 0,
        y: 0,
        data: {
          heading: "",
          fields: [],
          entries: [],
          icons: [],
          color: "",
          icon: ""
        }
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
    icons: {
      type: Array,
      default: []
    }
  },
  data() {
    return {
      isSelectingNewFieldtype: false,
      modalOpen: false,
      heading: this.item.data.heading,
      fields: this.item.data.fields,
      entries: this.entries,
      icons: this.icons,
      selected: null,
      icon: "",
      colorConfig: {
        lock_opacity: true,
        swatches: ["#f44336", "#e91e63", "#9c27b0"],
        theme: "classic",
        default_color_mode: "HEX",
        default: "#f44336",
        color_modes: ["hex", "rgba"]
      }
    };
  },
  computed: {
    hasFields() {
      return (this.item.data.fields !== undefined && this.item.data.fields.length > 0);
    },
    iconOptions() {
      return this.icons.map(icon => {
        return {
          label: icon.title,
          value: icon.id
        };
      });
    },
    iconOptions2() {
      return this.icons;
    }
  },
  methods: {
    select(fieldType) {
      if (this.item.data.fields === undefined) {
        this.item.data.fields = [];
      }
      this.item.data.fields.push(this.cleanObject(fieldType));
      this.isSelectingNewFieldtype = false;
    },
    updateFieldContent($event, field, fIndex) {
      field.content = $event;
    },
    edit() {
      this.modalOpen = true;
    },
    removeField(field, fIndex) {
      this.item.data.fields = this.item.data.fields.filter(item => item.content !== field.content);
    },
    remove() {
      if (confirm("Are you sure?")) {
        this.$emit("delete", this.index);
      }
    },
    cleanObject(obj) {
      return JSON.parse(JSON.stringify(obj));
    },
    createEntriesObject() {
      return this.entries.map((entry) => {
        return {
          "label": entry.title,
          "value": entry.url
        };
      });
    },
    onSelectionChange() {
      console.log(this.item.data.entries);
    }
  }
};
</script>
