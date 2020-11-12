<template>
  <k-input
    ref="input"
    :marks="marks"
    :value="content.list"
    class="k-block-type-list-input"
    type="list"
    @input="update({ list: $event })"
  />
</template>

<script>
export default {
  computed: {
    marks() {
      return this.field("list", {}).marks;
    }
  },
  methods: {
    focus() {
      this.$refs.input.focus();
    }
  }
};
</script>

