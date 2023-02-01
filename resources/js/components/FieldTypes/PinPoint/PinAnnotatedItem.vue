<template>
  <div class="px-1 py-1 bg-white border-b border-r border-l flex-1">
    <div class="flex flex-col gap-1">
      <h5 v-text="`Point ${itemIndex + 1}`" class="text-xs mb-0"></h5>
      <h6
        v-text="item.data.heading"
        class="break-all"
        style="font-size: 10px"
      ></h6>
      <div class="flex justify-between w-full--">
        <a
          href="#"
          @click.prevent="edit"
          class="text-xs"
          v-tooltip="'Edit Point Data'"
        >
          <svg-icon name="form" class="w-4 h-4" />
        </a>
        <a
          href="#"
          @click.prevent="remove"
          class="text-xs"
          v-tooltip="'Delete Point'"
        >
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
          @click="modal"
          v-html="'&times'"
        />

        <div class="content mt-0 mb-2">
          <h2>Content</h2>
          <p class="max-w-lg">Edit your marker content here:</p>
        </div>

        <div>
          <!-- Label -->
          <label class="text-base font-bold mb-1">Label</label>
          <input
            type="text"
            v-model="item.data.heading"
            class="input-text mb-2"
          />

          <!-- Layout -->
          <div class="grid w-full grid-cols-2 gap-x-4 mt-2">
            <!-- Category -->
            <div class="flex flex-col gap-2">
              <h3 class="text-base font-bold">Category</h3>

              <button
                v-if="item.data.category !== undefined"
                @click="item.data.category = []"
                class="btn"
              >
                Clear category
              </button>

              <select v-model="item.data.category" class="py-1">
                <option
                  v-for="option in createCategoryObject()"
                  v-bind:value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>

            <!-- Entries -->
            <div class="flex flex-col gap-2">
              <h3 class="text-base font-bold">Page entry</h3>

              <button
                v-if="item.data.entries !== undefined"
                @click="item.data.entries = []"
                class="btn"
              >
                Clear entry
              </button>

              <select v-model="item.data.entries" class="py-1">
                <option
                  v-for="option in createEntriesObject()"
                  v-bind:value="option.value"
                >
                  {{ option.label }}
                </option>
              </select>
            </div>
          </div>

          <div class="grid w-full grid-cols-2 gap-x-4 mt-2">
            <!-- Icons-->
            <div class="flex flex-col gap-2 mt-2">
              <div class="flex items-center gap-x-1">
                <h3 class="text-base font-bold">Icons</h3>
                <span class="text-xs text-gray-50 italic">(Max. 3 icons).</span>
              </div>

              <button
                v-if="item.data.icons !== undefined"
                @click="item.data.icons = undefined"
                class="btn"
              >
                Clear icons
              </button>

              <select
                multiple
                v-model="item.data.icons"
                @input="toggleIcon"
                class="py-1"
              >
                <option
                  v-for="option in Object.values(createIconEnum())"
                  v-bind:value="option.class"
                  :selected="isSelected(option.class)"
                  :class="isSelected(option.class) ? 'bg-blue-200' : ''"
                >
                  {{ option.title }}
                </option>
              </select>
            </div>

            <!-- Color picker -->
            <div class="flex flex-col gap-2 mt-2">
              <!-- <p class="text-base font-bold">Color</p> -->
              <div class="flex items-center gap-x-1">
                <h3 class="text-base font-bold">Color</h3>
                <span class="text-xs text-gray-50 italic">(HEX or RGBA).</span>
              </div>

              <color-fieldtype
                v-model="item.data.color"
                :isReadOnly="false"
                :config="colorConfig"
              ></color-fieldtype>
            </div>
          </div>
        </div>

        <!-- Close button -->
        <div class="mt-2">
          <div class="flex flex-row justify-between">
            <button
              class="btn-primary w-auto ml-auto flex justify-center items-center"
              @click="modal"
            >
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
          entries: [],
          icons: [],
          color: "",
          category: [],
        },
      }),
    },
    itemIndex: {
      type: Number,
      default: 0,
    },
    entries: {
      type: Array,
      default: [],
    },
    icons: {
      type: Array,
      default: [],
    },
    category: {
      type: Array,
      default: [],
    },
  },
  data() {
    return {
      modalOpen: false,
      heading: this.item.data.heading,
      entries: this.entries,
      icons: this.icons,
      category: this.category,
      iconEnum: this.createIconEnum(),
      colorConfig: {
        lock_opacity: true,
        swatches: ["#4F7EBD", "#FEB900", "#65BA3D"],
        theme: "classic",
        default_color_mode: "HEX",
        default: "#FEB900",
        color_modes: ["hex", "rgba"],
      },
    };
  },
  computed: {},
  methods: {
    modal() {
      this.modalOpen = !this.modalOpen;

      this.item.data.icons = this.item.data.icons?.map((item) => {
        return this.iconEnum[item];
      });
    },

    createIconEnum() {
      const icons = this.icons.reduce((accumulator, icon) => {
        accumulator[icon.fa_icon] = {
          title: icon.title,
          class: icon.fa_icon,
        };

        return accumulator;
      }, {});

      return icons;
    },

    edit() {
      this.modalOpen = true;
    },
    remove() {
      if (confirm("Are you sure?")) {
        this.$emit("delete", this.index);
      }
    },
    createEntriesObject() {
      return this.entries.map((entry) => {
        return {
          label: entry.title,
          value: entry.url,
        };
      });
    },
    createCategoryObject() {
      return this.category.map((category) => {
        return {
          label: category.title,
          value: {
            title: category.title,
            color: category.color_field,
          },
        };
      });
    },
    isSelected(value) {
      return this.item.data.icons?.some((icon) => icon?.class === value);
    },
    toggleIcon(event) {
      const iconClass = event.target.value;

      if (this.item.data.icons?.includes(iconClass)) {
        this.item.data.icons?.splice(
          this.item.data.icons?.indexOf(iconClass),
          1
        );
      } else {
        this.item.data.icons?.push(iconClass);
      }
    },
  },
};
</script>

<style scoped>
.btn {
  max-width: min-content;
  padding: 5px 10px;
  border: 1px solid gray;
}
</style>
