<template>
  <k-dropdown class="k-block-options">
    <k-sort-handle class="k-block-handle" />
    <k-button
      class="k-block-options-toggle"
      icon="angle-down"
      @click="$refs.options.toggle()"
    />
    <k-block-dropdown
      ref="options"
      v-bind="$props"
      v-on="$listeners"
    />
  </k-dropdown>
</template>

<script>
export default {
  props: {
    isFull: Boolean,
    isHidden: Boolean,
    isOpen: Boolean,
    wysiwyg: Boolean,
  },
  methods: {
    open(event) {
      this.$refs.options.open();
    }
  }
};
</script>

<style lang="scss">
.k-block-options {
  display: flex;
  align-items: center;
}
.k-block-handle.k-sort-handle,
.k-block-options-toggle {
  width: 1.5rem;
  height: 1.5rem;
  color: $color-gray-500;
}
.k-block-handle.k-sort-handle:hover,
.k-block-options-toggle:hover {
  background: $color-background;
}
</style>

