Nova.booting((Vue, router, store) => {
  Vue.component('index-parent-field', require('./components/IndexField'));
  Vue.component('detail-parent-field', require('./components/DetailField'));
  Vue.component('form-parent-field', require('./components/FormField'));
});
