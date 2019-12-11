Nova.booting((Vue, router, store) => {
  Vue.component('index-region-field', require('./components/IndexField').default);
  Vue.component('detail-region-field', require('./components/DetailField').default);
  Vue.component('form-region-field', require('./components/FormField').default);
});
