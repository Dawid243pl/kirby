<script>
export default {
  inheritAttrs: false,
  props: {
    content: Object,
    fieldset: Object
  },
  methods: {
    field(name, fallback = null) {
      let field = null;

      Object.values(this.fieldset.tabs).forEach(tab => {
        if (tab.fields[name]) {
          field = tab.fields[name];
        }
      });

      return field || fallback;
    },
    update(content) {
      this.$emit('update', {
        ...this.content,
        ...content
      });
    }
  }
};
</script>
