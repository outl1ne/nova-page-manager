Nova.booting((Vue, router, store) => {
  Vue.component("index-translations-field", require("./components/IndexField"));
  Vue.component("detail-translations-field", require("./components/DetailField"));
  Vue.component("form-translations-field", require("./components/FormField"));
});
