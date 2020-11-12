<template>
  <div
    :data-empty="blocks.length === 0"
    class="k-blocks"
  >
    <k-draggable
      v-bind="draggableOptions"
      class="k-blocks-list"
      @sort="save"
    >
      <k-block
        v-for="(block, index) in blocks"
        v-if="fieldsets[block.type]"
        :ref="'block-' + block.id"
        :key="block.id"
        :endpoints="endpoints"
        :fieldset="fieldsets[block.type]"
        :is-full="isFull"
        :is-hidden="block.isHidden === true"
        :is-selected="selected && selected === block.id"
        v-bind="block"
        @append="add($event, index + 1)"
        @blur="select(null)"
        @choose="choose($event)"
        @chooseToAppend="choose(index + 1)"
        @chooseToPrepend="choose(index)"
        @convert="convert(block, $event)"
        @duplicate="duplicate(block, index)"
        @focus="select(block)"
        @hide="hide(block)"
        @prepend="add($event, index)"
        @remove="remove(block)"
        @sortDown="sort(block, index, index + 1)"
        @sortUp="sort(block, index, index - 1)"
        @show="show(block)"
        @update="update(block, $event)"
      />
      <template #footer>
        <k-empty
          icon="box"
          class="k-blocks-empty"
          @click="choose(blocks.length)"
        >
          {{ empty || $t("field.blocks.empty") }}
        </k-empty>
      </template>
    </k-draggable>

    <k-block-selector
      ref="selector"
      :fieldsets="fieldsets"
      :fieldset-groups="fieldsetGroups"
      @add="add"
    />

    <k-remove-dialog ref="removeAll" @submit="removeAll">
      {{ $t("field.blocks.delete.all.confirm") }}
    </k-remove-dialog>

  </div>
</template>

<script>
import debounce from "@/helpers/debounce.js";

export default {
  inheritAttrs: false,
  props: {
    empty: String,
    endpoints: Object,
    fieldsets: Object,
    fieldsetGroups: Object,
    group: String,
    max: {
      type: Number,
      default: null,
    },
    value: {
      type: Array,
      default() {
        return [];
      }
    }
  },
  data() {
    return {
      blocks: this.value,
    };
  },
  computed: {
    draggableOptions() {
      return {
        id: this._uid,
        handle: ".k-block-handle",
        list: this.blocks,
        move: this.move,
        delay: 10,
        data: {
          fieldsets: this.fieldsets,
          isFull: this.isFull
        },
        options: {
          group: this.group
        }
      };
    },
    isEmpty() {
      return this.blocks.length === 0;
    },
    isFull() {
      if (this.max === null) {
        return false;
      }

      return this.blocks.length >= this.max;
    },
    selected() {
      return this.$store.state.blocks.current;
    }
  },
  watch: {
    value() {
      this.blocks = this.value;
    }
  },
  created() {
    this.save = debounce(this.save, 50);
    this.outsideFocus = (event) => {
      const overlay = document.querySelector(".k-overlay:last-of-type");
      if (this.$el.contains(event.target) === false && (!overlay || overlay.contains(event.target) === false)) {
        this.select(null);
      }
    };

    document.addEventListener("focus", this.outsideFocus, true);
  },
  destroyed() {
    document.removeEventListener("focus", this.outsideFocus);
  },
  methods: {
    async add(type = "text", index) {
      const block = await this.$api.get(this.endpoints.field + "/fieldsets/" + type);
      this.blocks.splice(index, 0, block);
      this.save();

      this.$nextTick(() => {
        this.focusOrOpen(block);
      });
    },
    choose(index) {
      if (Object.keys(this.fieldsets).length === 1) {
        const type = Object.values(this.fieldsets)[0].type;
        this.add(type, index);
      } else {
        this.$refs.selector.open(index);
      }
    },
    click(block, event) {
      this.$emit("click", block);
    },
    confirmToRemoveAll() {
      this.$refs.removeAll.open();
    },
    convert(block, type) {
      if (type === "blockquote") {
        type = "quote";
      }

      this.$set(block, "type", type);
    },
    async duplicate(block, index) {
      const response = await this.$api.get(this.endpoints.field + "/uuid");
      const copy = {
        ...this.$helper.clone(block),
        id: response["uuid"]
      };
      this.blocks.splice(index + 1, 0, copy);
      this.save();
    },
    fieldset(block) {
      return this.fieldsets[block.type];
    },
    focus(block) {
      if (this.$refs["block-" + block.id]) {
        this.$refs["block-" + block.id][0].focus();
      }
    },
    focusOrOpen(block) {
      if (this.fieldsets[block.type].wysiwyg) {
        this.focus(block);
      } else {
        this.open(block);
      }
    },
    hide(block) {
      this.$set(block, "isHidden", true);
      this.save();
    },
    move(event) {
      // moving block between fields
      if (event.from !== event.to) {
        const block = event.draggedContext.element;
        const to    = event.relatedContext.component.componentData || event.relatedContext.component.$parent.componentData;

        // fieldset is not supported in target field
        if (Object.keys(to.fieldsets).includes(block.type) === false) {
          return false;
        }

        // target field has already reached max number of blocks
        if (to.isFull === true) {
          return false;
        }
      }

      return true;
    },
    open(block) {
      if (this.$refs["block-" + block.id]) {
        this.$refs["block-" + block.id][0].open();
      }
    },
    remove(block) {
      const index = this.blocks.findIndex(element => element.id === block.id);

      if (index !== -1) {

        if (this.selected && this.selected.id === block.id) {
          this.select(null);
        }

        this.$delete(this.blocks, index);
        this.save();
      }
    },
    removeAll() {
      this.blocks = [];
      this.save();
      this.$refs.removeAll.close();
    },
    save() {
      this.$emit("input", this.blocks);
    },
    select(block) {
      this.$store.dispatch("blocks/current", block ? block.id : null);
    },
    show(block) {
      this.$set(block, "isHidden", false);
      this.save();
    },
    sort(block, from, to) {
      if (to < 0) {
        return;
      }
      let blocks = this.$helper.clone(this.blocks);
      blocks.splice(from, 1);
      blocks.splice(to, 0, block);
      this.blocks = blocks;
      this.save();
      this.$nextTick(() => {
        this.focus(block);
      });
    },
    update(block, content) {
      const index = this.blocks.findIndex(element => element.id === block.id);
      if (index !== -1) {
        this.$set(this.blocks[index], "content", content);
      }
      this.save();
    }
  }
};
</script>

<style lang="scss">
.k-blocks {
  background: $color-white;
  box-shadow: $shadow;
  border-radius: $rounded;
}
.k-blocks[data-empty] {
  padding: 0;
  background: none;
  box-shadow: none;
}
.k-blocks .k-sortable-ghost {
  outline: 2px solid $color-focus;
  box-shadow: rgba($color-gray-900, 0.25) 0 5px 10px;
  cursor: grabbing;
  cursor: -moz-grabbing;
  cursor: -webkit-grabbing;
}
.k-blocks-empty.k-empty {
  cursor: pointer;
  display: flex;
  align-items: center;
}
.k-blocks-list > .k-blocks-empty:not(:only-child) {
  display: none;
}
</style>
