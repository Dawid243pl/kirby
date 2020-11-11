<template>
  <div class="k-block-type-list-editor">
    <k-writer
      ref="input"
      :key="type"
      :nodes="nodes"
      :extensions="extensions"
      :value="content.list"
      class="k-block-type-list-input"
      @input="update({ list: $event, type: type })"
    />

    <nav class="k-block-type-list-toggle">
      <k-button :current="type === 'ul'" icon="list-bullet" @click="updateType('ul')" />
      <k-button :current="type === 'ol'" icon="list-numbers" @click="updateType('ol')" />
    </nav>
  </div>
</template>

<script>

import Doc from "@/components/Writer/Nodes/Doc.js";

class BulletDoc extends Doc {
  get schema() {
    return {
      content: "bulletList",
    }
  }
}
class OrderedDoc extends Doc {
  get schema() {
    return {
      content: "orderedList",
    }
  }
}

export default {
  data() {
    return {
      type: this.content.type || "ul"
    };
  },
  watch: {
    "content.type"() {
      return this.type = this.content.type
    }
  },
  computed: {
    extensions() {
      return [
        this.type === "ul" ? new BulletDoc() : new OrderedDoc()
      ];
    },
    nodes() {
      if (this.type === "ul") {
        return ["bulletList"];
      } else {
        return ["orderedList"];
      }
    }
  },
  methods: {
    focus() {
      this.$refs.input.focus();
    },
    updateType(type) {
      this.type = type;

      this.$nextTick(() => {
        this.update({
          type: this.type,
          list: this.$refs.input.getHTML()
        });
      });

    }
  }
};
</script>

<style lang="scss">
.k-block-type-list-editor {
  position: relative;
}
.k-block-type-list-input {
  padding-right: 5rem;
}
.k-block-type-list-input.k-writer .ProseMirror {
  line-height: 1.5em;
}
.k-block-type-list-input.k-writer .ProseMirror ol > li::marker {
  font-size: $text-sm;
  color: $color-gray-500;
}
.k-block-type-list-toggle {
  position: absolute;
  bottom: 0;
  right: 0;
  display: none;
  border-radius: $rounded;
  margin-bottom: calc(-.25rem - 1px);
  margin-right: -.25rem;
  background: $color-white;
}
.k-block-container-type-list[data-selected] .k-block-type-list-toggle {
  display: flex;
}
.k-block-type-list-toggle .k-button {
  width: 2rem;
  height: 2rem;
  line-height: 1;
  color: $color-gray-500;
}
.k-block-type-list-toggle .k-button:hover,
.k-block-type-list-toggle .k-button[aria-current] {
  color: $color-black;
}
</style>
