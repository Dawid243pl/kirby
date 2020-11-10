<template>
  <k-block-figure
    :caption="content.caption"
    :empty-text="$t('field.blocks.image.placeholder') + ' …'"
    :is-empty="!image"
    empty-icon="image"
    v-on="$listeners"
  >
    <img v-if="image" :src="image.url">
  </k-block-figure>
</template>

<script>
export default {
  computed: {
    image() {
      return this.content.image[0];
    }
  }
};
</script>

