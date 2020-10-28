import Vue from "vue";

export default {
  namespaced: true,
  state: {
    current: null,
    installed: []
  },
  mutations: {
    SET_CURRENT(state, id) {
      state.current = id;
    },
    INSTALL(state, translation) {
      state.installed[translation.id] = translation;
    }
  },
  actions: {
    load(context, id) {
      return Vue.$api.translations.get(id);
    },
    install(context, translation) {
      context.commit("INSTALL", translation);
      Vue.i18n.add(translation.id, translation.data);
    },
    async activate(context, id) {
      const translation = context.state.installed[id];

      if (!translation) {
        const translation = await context.dispatch("load", id);
        context.dispatch("install", translation);
        context.dispatch("activate", id);
        return;
      }

      // activate the translation
      Vue.i18n.set(id);

      // load dayjs locale
      const locales = [id.replace("_", "-"), id.split("_")[0]];
      for (let i = 0; i < locales.length; i++) {
        try {
          await import(/* webpackChunkName: "dayjs-[request]" */ `dayjs/locale/${locales[i]}.js`);
          Vue.$library.dayjs.locale(locales[i]);
          break;
        } catch (error) {}
      }

      // store the current translation
      context.commit("SET_CURRENT", id);

      // change the document's reading direction
      document.dir = translation.direction;

      // change the lang attribute on the html element
      document.documentElement.lang = id;
    }
  }
};
