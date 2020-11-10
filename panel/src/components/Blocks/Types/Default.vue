<template>
  <k-block-title
    :content="content"
    :fieldset="fieldset"
    class="k-block-default-placeholder"
    @dblclick="$emit('open')"
  />
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    content: [Array, Object],
    fieldset: Object,
  }
}
</script>

