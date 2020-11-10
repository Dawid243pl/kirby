<template>
  <header class="k-block-header">
    <k-block-title
      :content="content"
      :fieldset="fieldset"
    />
    <nav
      v-if="hasTabs"
      class="k-block-header-tabs"
    >
      <k-button
        v-for="tab in tabs"
        :key="tab.name"
        :current="currentTab == tab.name"
        class="k-block-header-tab"
        @click.stop="$emit('open', tab.name)"
      >
        {{ tab.label }}
      </k-button>
    </nav>

    <k-button
      v-if="isHidden"
      class="k-block-header-status"
      icon="hidden"
      @click.stop="$emit('show')"
    />
  </header>
</template>

<script>
export default {
  props: {
    content: Object,
    fieldset: Object,
    isHidden: Boolean,
    tab: String,
    tabs: Array
  },
  computed: {
    currentTab() {
      return this.tab || this.tabs[0].name;
    },
    hasTabs() {
      return this.tabs.length > 1
    }
  }
};
</script>

<style lang="scss">
$block-header-padding: 1.5rem;

.k-block-header {
  height: 2.5rem;
  padding-left: 1.5rem;
  display: flex;
  align-items: center;
  font-size: $text-xs;
  line-height: 1;
  justify-content: space-between;
  cursor: pointer;
  background: #fff;
}
.k-block-header-tabs {
  display: flex;
  align-items: center;
  line-height: 1;
  margin-right: $block-header-padding - .75rem;
}
.k-block-header-tab.k-button {
  height: 36px;
  padding: 0 .75rem;
  display: flex;
  align-items: center;
  font-size: $text-xs;
}
.k-block-header-tab[aria-current]::after {
  position: absolute;
  bottom: -1px;
  left: .75rem;
  right: .75rem;
  content: "";
  background: $color-black;
  height: 2px;
}
.k-block-header-status {
  width: 2.5rem;
  color: $color-gray-500;
}
</style>
