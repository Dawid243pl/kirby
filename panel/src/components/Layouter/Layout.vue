<template>
  <section
    :data-selected="isSelected"
    class="k-layout"
    tabindex="0"
    @click="$emit('select')"
  >
    <k-grid class="k-layout-columns">
      <k-layout-column
        v-for="(column, columnIndex) in columns"
        :key="column.id"
        :endpoints="endpoints"
        :fieldset-groups="fieldsetGroups"
        :fieldsets="fieldsets"
        v-bind="column"
        @input="$emit('input', {
          column,
          columnIndex,
          blocks: $event
        })"
      />
    </k-grid>
    <nav class="k-layout-toolbar">
      <k-dropdown>
        <k-button class="k-layout-toolbar-button" icon="angle-down" @click="$refs.options.toggle()" />
        <k-dropdown-content ref="options" align="right">
          <k-dropdown-item icon="angle-up" @click="$emit('prepend')">{{ $t("insert.before") }}</k-dropdown-item>
          <k-dropdown-item icon="angle-down" @click="$emit('append')">{{ $t("insert.after") }}</k-dropdown-item>
          <hr>
          <k-dropdown-item icon="copy" @click="$emit('duplicate')">{{ $t("duplicate") }}</k-dropdown-item>
          <hr>
          <k-dropdown-item icon="trash" @click="$refs.confirmRemoveDialog.open()">{{ $t("field.layout.delete") }}</k-dropdown-item>
        </k-dropdown-content>
      </k-dropdown>
      <k-sort-handle />
    </nav>

    <k-remove-dialog ref="confirmRemoveDialog" @submit="$emit('remove')">
      {{ $t("field.layout.delete.confirm") }}
    </k-remove-dialog>

  </section>
</template>

<script>
import Column from "./Column";

export default {
  components: {
    "k-layout-column": Column
  },
  props: {
    columns: Array,
    endpoints: Object,
    fieldsetGroups: Object,
    fieldsets: Object,
    id: String,
    isSelected: Boolean
  }
};
</script>

<style lang="scss">
$layout-border-color: $color-gray-300;
$layout-toolbar-width: 2rem;

.k-layout {
  position: relative;
  padding-right: $layout-toolbar-width;
  background: #fff;
  box-shadow: $shadow;
}
.k-layout:not(:last-of-type) {
  margin-bottom: 1px;
}
.k-layout:focus {
  outline: 0;
}

/** Toolbar **/
.k-layout-toolbar {
  position: absolute;
  right: 0;
  top: 0;
  bottom: 0;
  width: $layout-toolbar-width;
  display: flex;
  flex-direction: column;
  font-size: $text-sm;
  background: $color-gray-100;
  border-left: 1px solid $color-gray-200;
  color: $color-gray-500;
}
.k-layout-toolbar:hover {
  color: $color-black;
}
.k-layout-toolbar-button {
  width: $layout-toolbar-width;
  height: $layout-toolbar-width;
}
.k-layout-toolbar .k-sort-handle {
  margin-top: auto;
  color: currentColor;
}

/** Columns **/
.k-layout-columns.k-grid {
  grid-gap: 1px;
  background: $layout-border-color;
  background: $color-gray-300;
}
.k-layout:not(:first-child) .k-layout-columns.k-grid {
  border-top: 0;
}
</style>
