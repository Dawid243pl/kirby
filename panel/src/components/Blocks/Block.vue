<template>
  <div
    :class="'k-block-container-' + type"
    :data-disabled="fieldset.disabled"
    :data-hidden="isHidden"
    :data-open="isOpen"
    :data-translate="fieldset.translate"
    class="k-block-container"
    @contextmenu.prevent="$refs.dropdown.open($event)"
    @mouseenter="mouseenter()"
    @mouseleave="mouseleave()"
  >
    <k-block-options
      ref="options"
      v-if="isHovered"
      :is-full="isFull"
      :is-hidden="isHidden"
      :is-open="isOpen"
      @openOptions="hasOpenOptions = true"
      @closeOptions="hasOpenOptions = false"
      v-on="$listeners"
    />

    <k-block-dropdown
      ref="dropdown"
      :mouse="true"
      :is-full="isFull"
      :is-hidden="isHidden"
      :is-open="isOpen"
      v-on="$listeners"
    />

    <div :class="className" class="k-block">
      <component
        ref="editor"
        :is="customComponent"
        :is-sticky="wysiwyg"
        :is-open="isOpen && !compact"
        v-bind="$props"
        v-on="$listeners"
      />
    </div>

    <k-remove-dialog ref="removeDialog" @submit="remove">
      {{ $t("field.blocks.delete.confirm") }}
    </k-remove-dialog>
  </div>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    attrs: [Array, Object],
    compact: Boolean,
    content: [Array, Object],
    endpoints: Object,
    fieldset: Object,
    id: String,
    isFull: Boolean,
    isHidden: Boolean,
    isOpen: Boolean,
    isSticky: Boolean,
    name: String,
    tabs: Object,
    type: String,
  },
  data() {
    return {
      isHovered: false,
      hasOpenOptions: false,
    };
  },
  computed: {
    className() {
      let className = ["k-block-" + this.type];

      if (this.fieldset.preview) {
        className.push("k-block-" + this.fieldset.preview);
      }

      if (this.wysiwyg === false) {
        className.push("k-block-default");
      }

      return className;
    },
    customComponent() {
      if (this.isOpen === true && !this.compact) {
        return "k-block-default";
      }

      if (this.wysiwyg) {
        return this.wysiwygComponent;
      }

      return "k-block-default";
    },
    wysiwyg() {
      return this.wysiwygComponent !== false;
    },
    wysiwygComponent() {
      let component = "k-block-" + this.type;

      if (this.$helper.isComponent(component)) {
        return component;
      }

      if (this.fieldset.preview) {
        component = "k-block-" + this.fieldset.preview;

        if (this.$helper.isComponent(component)) {
          return component;
        }
      }

      return false;
    },
  },
  mounted() {
    this.$events.$on("click", this.outsideClick = (event) => {
      if (this.$el.contains(event.target) === false) {
        this.isHovered = false;
      }
    });
  },
  destroyed() {
    this.$events.$off("click", this.outsideClick);
  },
  methods: {
    confirmToRemove() {
      this.$refs.removeDialog.open();
    },
    focus() {
      if (typeof this.$refs.editor.focus === "function") {
        this.$refs.editor.focus();
      }
    },
    mouseenter() {
      this.isHovered = true;
    },
    mouseleave() {
      if (this.hasOpenOptions === false) {
        this.isHovered = false;
      }
    },
    remove() {
      this.$refs.removeDialog.close();
      this.$emit("remove", this.id);
    }
  }
};
</script>

<style lang="scss">
.k-block-container {
  position: relative;
  padding: 0 4rem;
  border-radius: $rounded;
}
.k-block-container:focus {
  outline: 0;
}
.k-block-container .k-block-options {
  position: absolute;
  top: 50%;
  margin-top: -.75rem;
  left: .5rem;
}
.k-block-container[data-hidden] .k-block {
  opacity: .25;
}

.k-block-drawer {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
  z-index: z-index(toolbar);
  display: flex;
  align-items: stretch;
  justify-content: flex-end;
  background: rgba($color-black, .25);
}
.k-block-drawer-box {
  flex-basis: 50rem;
  background: $color-background;
  box-shadow: $shadow-xl;
}
.k-block-drawer .k-block-default-box {
  box-shadow: none;
  border: 0;
}
.k-block-drawer .k-block-header {
  height: 2.5rem;
}
</style>
