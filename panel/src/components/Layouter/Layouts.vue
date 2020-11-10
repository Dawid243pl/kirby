<template>
  <div>
    <template v-if="rows.length">
      <k-draggable
          v-bind="draggableOptions"
          class="k-layouts"
          @sort="save"
      >
        <section
          v-for="(layout, layoutIndex) in rows"
          :key="layout.id"
          :data-current="currentLayout && currentLayout.id === layout.id"
          class="k-layout"
          tabindex="0"
          @click="currentLayout = layout"
        >
          <k-sort-handle class="k-layout-handle" />
          <k-grid class="k-layout-columns">
            <div
              v-for="(column, columnIndex) in layout.columns"
              :key="columnIndex"
              :data-width="column.width"
              :id="column.id"
              tabindex="0"
              class="k-column k-layout-column"
            >
              <k-blocks
                :ref="layout.id + '-' + column.id + '-blocks'"
                :endpoints="endpoints"
                :fieldsets="fieldsets"
                :max="max"
                :value="column.blocks"
                group="layout"
                @input="updateBlocks({
                  layout,
                  layoutIndex,
                  column,
                  columnIndex,
                  blocks: $event
                })"
              />
              <button
                v-if="column.blocks.length === 0"
                class="k-layout-column-filler"
                @click="$refs[layout.id + '-' + column.id + '-blocks'][0].choose(column.blocks.length)"
              />
            </div>
          </k-grid>
          <nav class="k-layout-options">
            <k-dropdown>
              <k-button icon="angle-down" @click="$refs['layout-' + layout.id][0].toggle()" />
              <k-dropdown-content :ref="'layout-' + layout.id" align="right">
                <k-dropdown-item icon="angle-up" @click="selectLayout(layoutIndex)">{{ $t("insert.before") }}</k-dropdown-item>
                <k-dropdown-item icon="angle-down" @click="selectLayout(layoutIndex + 1)">{{ $t("insert.after") }}</k-dropdown-item>
                <hr>
                <k-dropdown-item icon="trash" @click="removeLayout(layout)">{{ $t("field.layout.delete") }}</k-dropdown-item>
              </k-dropdown-content>
            </k-dropdown>
          </nav>
        </section>
      </k-draggable>

      <k-button
        class="k-layout-add-button"
        icon="add"
        @click="selectLayout(rows.length)"
      />

    </template>
    <template v-else>
      <k-empty
        icon="dashboard"
        class="k-layout-empty"
        @click="selectLayout(0)"
      >
        {{ empty || $t("field.layout.empty") }}
      </k-empty>
    </template>

    <k-dialog
      ref="selector"
      :cancel-button="false"
      :submit-button="false"
      size="medium"
      class="k-layout-selector"
    >
      <k-headline>{{ $t("field.layout.select") }}</k-headline>
      <ul>
        <li
          v-for="(layoutOption, layoutOptionIndex) in layouts"
          :key="layoutOptionIndex"
          class="k-layout-selector-option"
        >
          <k-grid @click.native="addLayout(layoutOption)">
            <k-column
              v-for="(column, columnIndex) in layoutOption"
              :key="columnIndex"
              :width="column"
            />
          </k-grid>
        </li>
      </ul>
    </k-dialog>
  </div>
</template>

<script>
export default {
  props: {
    empty: String,
    endpoints: Object,
    fieldsets: Object,
    layouts: Array,
    max: Number,
    value: Array
  },
  data() {
    return {
      rows: this.value,
      currentLayout: null,
      nextIndex: null,
    };
  },
  computed: {
    draggableOptions() {
      return {
        id: this._uid,
        handle: true,
        list: this.rows,
        delay: 10
      };
    }
  },
  watch: {
    value() {
      this.rows = this.value;
    }
  },
  methods: {
    addLayout(columns) {

      let layout = {
        id: this.$helper.uuid(),
        columns: []
      };

      columns.forEach(width => {
        layout.columns.push({
          id: this.$helper.uuid(),
          width: width,
          blocks: []
        });
      });

      this.rows.splice(this.nextIndex, 0, layout);

      if (this.layouts.length > 1) {
        this.$refs.selector.close();
      }

      this.save();
    },
    editBlocks(args) {
      if (args.column.blocks.length === 0) {
        return;
      }

      this.$emit('edit', args);
    },
    prependLayout() {
      this.$refs.selector.open();
    },
    removeLayout(layout) {
      const index = this.rows.findIndex(element => element.id === layout.id);

      if (index !== -1) {
        this.$delete(this.rows, index);
      }

      this.save();
    },
    save() {
      this.$emit("input", this.rows);
    },
    selectLayout(index) {
      this.nextIndex = index;

      if (this.layouts.length === 1) {
        this.addLayout(this.layouts[0]);
        return;
      }

      this.$refs.selector.open();
    },
    updateBlocks(args) {
      this.rows[args.layoutIndex].columns[args.columnIndex].blocks = args.blocks;
      this.save();
    }
  }
};
</script>

<style lang="scss">
$layout-color-border: $color-blue-300;
$layout-padding: 0;

.k-layouts {
  background: $color-background;
  border-radius: $rounded;
  box-shadow: $shadow;
}
.k-layout {
  position: relative;
  margin: 0;
  border-radius: $rounded-sm;
  background: $color-background;
}
.k-layout:not(:last-child) {
  margin-bottom: 1px;
}
.k-layout .k-layout-handle,
.k-layout .k-layout-options {
  position: absolute;
  top: -1px;
  bottom: -1px;
  height: calc(100% + 2px);
  width: 1.5rem;
  left: -1.5rem;
  display: none;
  color: $color-gray-500;
}
.k-layout .k-layout-options {
  left: auto;
  right: -1.5rem;
  align-items: center;
  justify-content: center;
}
.k-layout:focus-within .k-layout-options,
.k-layout:focus-within .k-layout-handle {
  display: flex;
}
.k-layout .k-layout-options > .k-button {
  height: 100%;
  width: 100%;
}
.k-layout:hover .k-layout-options,
.k-layout:hover .k-layout-handle {
  color: $color-black;
}
.k-layout-columns.k-grid {
  grid-gap: 1px;
}
.k-layout-column {
  position: relative;
  padding: $layout-padding;
  height: 100%;
  background: #fff;
  cursor: pointer;
  display: flex;
  flex-direction: column;
  min-height: 3rem;
}

.k-layout-column-filler {
  flex-grow: 1;
}
.k-layout-column-filler:focus {
  outline: 0;
}
.k-layout-column .k-block {
  box-shadow: none;
}

.k-layout-selector.k-dialog {
  background: #313740;
  color: $color-white;
}

.k-layout-selector .k-headline {
  margin-bottom: 1.5rem;
  line-height: 1;
  margin-top: -.25rem;
}

.k-layout-selector ul {
  display: grid;
  grid-template-columns: repeat(3, 1fr);
  grid-gap: 1.5rem;
}
.k-layout-selector-option .k-grid {
  height: 5rem;
  grid-gap: 2px;
  box-shadow: $shadow;
  cursor: pointer;
}
.k-layout-selector-option:hover {
  outline: 2px solid $color-green-300;
  outline-offset: 2px;
}
.k-layout-selector-option:last-child {
  margin-bottom: 0;
}
.k-layout-selector-option .k-column {
  display: flex;
  background: rgba(#fff, .2);
  justify-content: center;
  font-size: $text-xs;
  align-items: center;
}

.k-layout:focus,
.k-layout-column:focus {
  position: relative;
  z-index: 2;
  outline: 0;
  box-shadow: $color-focus 0 0 0 1px, $color-focus-outline 0 0 0 3px;
}
.k-layout-column .k-blocks {
  background: none;
  box-shadow: none;
  padding: 0;
  margin-bottom: 3rem;
  border-bottom: 1px dashed $color-background;
}
.k-layout-column .k-blocks-empty.k-empty {
  display: none;
}

.k-layout-add-button {
  display: flex;
  align-items: center;
  width: 100%;
  color: $color-gray-500;
  justify-content: center;
  padding: .75rem 0;
}
.k-layout-add-button:hover {
  color: $color-black;
}
</style>
