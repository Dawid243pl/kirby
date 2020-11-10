<template>
  <k-block-figure
    :caption="content.caption"
    :empty-text="$t('field.blocks.image.placeholder') + ' …'"
    :is-empty="!image"
    class="k-image-block"
    empty-icon="image"
    v-on="$listeners"
  >
    <img v-if="image" :src="image.url">
  </k-block-figure>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    content: Object
  },
  computed: {
    image() {
      return this.content.image[0];
    }
  }
};
</script>

