<template>
  <div>
    <template v-if="rows.length">
      <k-draggable
          v-bind="draggableOptions"
          class="k-layouts"
          @sort="save"
      >
        <k-layout
          v-for="(layout, layoutIndex) in rows"
          :key="layout.id"
          :endpoints="endpoints"
          :fieldsets="fieldsets"
          :is-selected="selected === layout.id"
          v-bind="layout"
          @select="selected = layout.id"
          @append="selectLayout(layoutIndex + 1)"
          @prepend="selectLayout(layoutIndex)"
          @remove="removeLayout(layout)"
          @input="updateLayout({
            layout,
            layoutIndex,
            ...$event
          })"
        />
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
import Layout from "./Layout";

export default {
  components: {
    "k-layout": Layout
  },
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
      currentLayout: null,
      nextIndex: null,
      rows: this.value,
      selected: null,
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
    updateLayout(args) {
      this.rows[args.layoutIndex].columns[args.columnIndex].blocks = args.blocks;
      this.save();
    }
  }
};
</script>

<style lang="scss">
/** Selector **/
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

/** Add Button **/
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
