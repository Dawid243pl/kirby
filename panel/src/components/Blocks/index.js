import Vue from "vue";

// main components
import Block from "./Block.vue";
import Blocks from "./Blocks.vue";

Vue.component("k-block", Block);
Vue.component("k-blocks", Blocks);

// helper components
import BlockDrawer from "./BlockDrawer.vue";
import BlockFigure from "./BlockFigure.vue";
import BlockHeader from "./BlockHeader.vue";
import BlockOptions from "./BlockOptions.vue";
import BlockSelector from "./BlockSelector.vue";
import BlockTitle from "./BlockTitle.vue";

Vue.component("k-block-drawer", BlockDrawer);
Vue.component("k-block-figure", BlockFigure);
Vue.component("k-block-header", BlockHeader);
Vue.component("k-block-options", BlockOptions);
Vue.component("k-block-selector", BlockSelector);
Vue.component("k-block-title", BlockTitle);

// block types
import Code from "./Types/Code.vue";
import Default from "./Types/Default.vue";
import Heading from "./Types/Heading.vue";
import Image from "./Types/Image.vue";
import Gallery from "./Types/Gallery.vue";
import Markdown from "./Types/Markdown.vue";
import Quote from "./Types/Quote.vue";
import Table from "./Types/Table.vue";
import Text from "./Types/Text.vue";
import Video from "./Types/Video.vue";

Vue.component("k-block-code", Code);
Vue.component("k-block-default", Default);
Vue.component("k-block-heading", Heading);
Vue.component("k-block-image", Image);
Vue.component("k-block-gallery", Gallery);
Vue.component("k-block-markdown", Markdown);
Vue.component("k-block-quote", Quote);
Vue.component("k-block-table", Table);
Vue.component("k-block-text", Text);
Vue.component("k-block-video", Video);
