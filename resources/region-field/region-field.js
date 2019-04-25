Nova.booting((Vue, router, store) => {
  Vue.component('index-region-field', require('./components/IndexField'));
  Vue.component('detail-region-field', require('./components/DetailField'));
  Vue.component('form-region-field', require('./components/FormField'));
});
