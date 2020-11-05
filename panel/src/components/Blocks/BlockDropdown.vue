<template>
  <k-dropdown-content
    ref="options"
    :style="styles"
    align="left"
    @close="$emit('closeOptions')"
    @open="$emit('openOptions')"
  >
    <k-dropdown-item :disabled="isFull" icon="angle-up" @click="$emit('chooseToPrepend')">
      {{ $t("insert.before") }}
    </k-dropdown-item>
    <k-dropdown-item :disabled="isFull" icon="angle-down" @click="$emit('chooseToAppend')">
      {{ $t("insert.after") }}
    </k-dropdown-item>
    <hr>
    <k-dropdown-item :icon="isOpen ? 'collapse' : 'expand'" @click="$emit(isOpen ? 'close' : 'open')">
      {{ $t(isOpen ? "collapse" : "expand") }}
    </k-dropdown-item>
    <hr>
    <k-dropdown-item :icon="isHidden ? 'preview' : 'hidden'" @click="$emit(isHidden ? 'show' : 'hide')">
      {{ isHidden === true ? $t('show') : $t('hide') }}
    </k-dropdown-item>
    <k-dropdown-item :disabled="isFull" icon="copy" @click="$emit('duplicate')">
      {{ $t("duplicate") }}
    </k-dropdown-item>
    <hr>
    <k-dropdown-item icon="trash" @click="$emit('remove')">
      {{ $t("delete") }}
    </k-dropdown-item>
  </k-dropdown-content>
</template>

<script>
export default {
  props: {
    isFull: Boolean,
    isHidden: Boolean,
    isOpen: Boolean,
    mouse: true
  },
  data() {
    return {
      left: 0,
      top: 0
    }
  },
  computed: {
    styles() {
      if (this.mouse) {
        return {
          position: "fixed",
          left: this.left + "px",
          top: this.top + "px"
        };
      }
    }
  },
  methods: {
    close() {
      this.$refs.options.close();
    },
    open(event) {
      this.top = event.clientY - 24;
      this.left = event.clientX;

      this.$refs.options.open();
    },
    toggle() {
      this.$refs.options.toggle();
    }
  }
};
</script>
