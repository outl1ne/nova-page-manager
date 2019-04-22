Nova.booting((Vue, router, store) => {
  Vue.component('index-locale-parent-field', require('./components/IndexField'));
  Vue.component('detail-locale-parent-field', require('./components/DetailField'));
  Vue.component('form-locale-parent-field', require('./components/FormField'));
});
