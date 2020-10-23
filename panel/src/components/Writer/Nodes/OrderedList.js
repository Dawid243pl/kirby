import Node from "../Node";

export default class OrderedList extends Node {

  get button() {
    return {
      label: "Ordered list",
      icon: "list-numbers",
    };
  }

  commands({ type, schema, utils }) {
    return {
      "orderedList": () => utils.chainCommands(utils.setBlockType(schema.nodes.paragraph), utils.wrapInList(type)),
      "toggleOrderedList": () => utils.toggleList(type, schema.nodes.listItem),
    }

  }

  inputRules({ type, utils }) {
    return [
      utils.wrappingInputRule(
        /^(\d+)\.\s$/,
        type,
        match => ({ order: +match[1] }),
        (match, node) => node.childCount + node.attrs.order === +match[1],
      ),
    ]
  }

  keys({ type, schema, utils }) {
    return {
      "Shift-Ctrl-9": utils.toggleList(type, schema.nodes.listItem),
    }
  }

  get name() {
    return "orderedList"
  }

  get schema() {
    return {
      attrs: {
        order: {
          default: 1,
        },
      },
      content: "listItem+",
      group: "block",
      parseDOM: [
        {
          tag: "ol",
          getAttrs: dom => ({
            order: dom.hasAttribute("start") ? +dom.getAttribute("start") : 1,
          }),
        },
      ],
      toDOM: node => (node.attrs.order === 1 ? ["ol", 0] : ["ol", { start: node.attrs.order }, 0]),
    }
  }

}
