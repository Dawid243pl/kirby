<template>
  <k-overlay
    ref="overlay"
    :dimmed="false"
    @close="$emit('close')"
    @open="$emit('open')"
  >
    <div class="k-block-drawer" @click.stop="close()">
      <div class="k-block-drawer-box" @click.stop>
        <k-button
          class="k-block-drawer-close"
          icon="cancel"
          @click="close()"
        />

        <k-block-header
          class="k-block-drawer-header"
          :content="content"
          :fieldset="fieldset"
          :is-hidden="isHidden"
          :tabs="tabs"
          :tab="tab"
          @show="$emit('show')"
          @tab="tab = $event"
        />
        <k-form
          ref="form"
          :autofocus="true"
          :fields="fields"
          :value="value"
          class="k-block-drawer-form"
          @input="$emit('update', $event)"
        />
      </div>
    </div>
  </k-overlay>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    attrs: [Array, Object],
    content: [Array, Object],
    endpoints: Object,
    fieldset: Object,
    id: String,
    isHidden: Boolean,
    name: String,
    type: String,
  },
  data() {
    return {
      tab: null,
      isOpen: false
    };
  },
  computed: {
    fields() {

      const tabId = this.tab || null;
      const tabs = this.fieldset.tabs;
      const tab = tabs[tabId] || Object.values(tabs)[0];
      const fields = tab.fields || {};

      if (Object.keys(fields).length === 0) {
        return {
          noFields: {
            type: "info",
            text: "This block has no fields"
          }
        };
      }

      Object.keys(fields).forEach(name => {
        let field = fields[name];

        field.section = this.name;
        field.endpoints = {
          field: this.endpoints.field + "/fieldsets/" + this.type + "/fields/" + name,
          section: this.endpoints.section,
          model: this.endpoints.model
        };

        fields[name] = field;
      });

      return fields;

    },
    firstTab() {
      return this.tabs[0];
    },
    tabs() {
      return Object.values(this.fieldset.tabs);
    },
    value() {
      return this.$helper.clone(this.content);
    }
  },
  methods: {
    close() {
      this.$refs.overlay.close();
      this.isOpen = false;
    },
    focus() {
      if (this.$refs.form && typeof this.$refs.form.focus === "function") {
        this.$refs.form.focus();
      }
    },
    open(tab, focus = true) {
      this.$refs.overlay.open();
      this.tab = tab || this.firstTab.name;

      this.$nextTick(() => {
        this.isOpen = true;
      });


      if (focus !== false) {
        setTimeout(() => {
          this.focus();
        }, 1);
      }
    },
    remove() {
      this.$refs.remove.close();
      this.$emit("remove", this.id);
    }
  }
}
</script>

<style lang="scss">
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
  background: rgba(#000, .2);
}
.k-block-drawer-box {
  position: relative;
  flex-basis: 50rem;
  background: $color-background;
  box-shadow: $shadow-xl;
}
.k-block-drawer-close {
  position: absolute;
  top: 0;
  right: 0;
  padding: 0 1.5rem;
  height: 2.5rem;
  line-height: 1;
  color: $color-gray-500;
}
.k-block-drawer-close:hover {
  color: $color-black;
}
.k-block-drawer-form {
  padding: 1.5rem;
  background: $color-background;
}
</style>
