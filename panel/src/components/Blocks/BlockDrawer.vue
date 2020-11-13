<template>
  <k-drawer
    ref="drawer"
    :icon="fieldset.icon || 'block'"
    :tabs="fieldset.tabs"
    :tab="tab"
    :title="fieldset.name"
    class="k-block-drawer"
    @close="$emit('close')"
    @open="$emit('open')"
    @tab="tab = $event"
  >
    <template #options>
      <k-button
        v-if="isHidden"
        class="k-drawer-option"
        icon="hidden"
        @click="$emit('show')"
      />
    </template>
    <template #default>
      <k-form
        ref="form"
        :autofocus="true"
        :fields="fields"
        :value="value"
        @input="$emit('update', $event)"
      />
    </template>
  </k-drawer>
</template>

<script>
export default {
  inheritAttrs: false,
  props: {
    content: [Array, Object],
    endpoints: Object,
    fieldset: Object,
    isHidden: Boolean,
    name: String,
    type: String,
  },
  data() {
    return {
      tab: null
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
      this.$refs.drawer.close();
    },
    focus() {
      if (this.$refs.form && typeof this.$refs.form.focus === "function") {
        this.$refs.form.focus();
      }
    },
    open(tab, focus = true) {
      this.$refs.drawer.open();
      this.tab = tab || this.firstTab.name;

      if (focus !== false) {
        setTimeout(() => {
          this.focus();
        }, 1);
      }
    }
  }
}
</script>
