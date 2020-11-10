<template>
  <ul @dblclick="$emit('open')">
    <template v-if="content.images.length === 0">
      <li />
      <li />
      <li />
    </template>
    <template v-else>
      <li v-for="image in content.images" :key="image.id">
        <img :src="image.url">
      </li>
    </template>
  </ul>
</template>

<style lang="scss">
.k-block-type-gallery ul {
  display: grid;
  grid-gap: .75rem;
  grid-template-columns: repeat(auto-fit, minmax(6rem, 1fr));
  line-height: 0;
  justify-content: center;
  cursor: pointer;
}
.k-block-type-gallery li {
  padding-bottom: 100%;
  position: relative;
  border-radius: $rounded-sm;
  background: $color-background;
}
.k-block-type-gallery img {
  position: absolute;
  top: 0;
  left: 0;
  bottom: 0;
  right: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
}
</style>
