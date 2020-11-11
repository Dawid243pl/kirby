<template>
  <k-block-figure
    :caption="content.caption"
    :caption-marks="captionMarks"
    :cover="false"
    :empty-text="$t('field.blocks.image.placeholder') + ' …'"
    :is-empty="!image"
    empty-icon="image"
    @open="open"
    @update="update"
  >
    <img v-if="image" :src="image.url">
  </k-block-figure>
</template>

<script>
export default {
  computed: {
    captionMarks() {
      return this.field("caption", { marks: true }).marks;
    },
    image() {
      return this.content.image[0];
    }
  }
};
</script>

