<template>
  <figure class="k-block-figure" >
    <span :data-cover="cover" class="k-block-figure-container" @dblclick="$emit('open')">
      <k-button
        v-if="isEmpty"
        :icon="emptyIcon"
        class="k-block-figure-empty"
      >
        {{ emptyText }}
      </k-button>
      <slot v-else />
    </span>
    <figcaption v-if="caption">
      <k-writer
        :inline="true"
        :marks="captionMarks"
        :value="caption"
        @input="$emit('update', { caption: $event })"
      />
    </figcaption>
  </figure>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    caption: String,
    captionMarks: [Boolean, Array],
    cover: {
      type: Boolean,
      default: true
    },
    isEmpty: Boolean,
    emptyIcon: String,
    emptyText: String
  }
};
</script>

<style lang="scss">
.k-block-figure {
  cursor: pointer;
}
.k-block-figure-container {
  position: relative;
  display: block;
  overflow: hidden;
  padding-bottom: 52.65%;
  background: $color-background;
}
.k-block-figure-container > * {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  height: 100%;
  width: 100%;
  object-fit: contain;
}
.k-block-figure-container[data-cover] > * {
  object-fit: cover;
}

.k-block-figure iframe {
  border: 0;
  pointer-events: none;
  background: $color-black;
}
.k-block-figure figcaption {
  padding-top: .5rem;
  color: $color-gray-600;
  font-size: $text-sm;
}
.k-block-figure-empty.k-button {
  display: flex;
  align-items: center;
  justify-content: center;
  color: $color-gray-600;
}
</style>
