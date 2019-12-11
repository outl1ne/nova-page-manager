Nova.booting((Vue, router, store) => {
  Vue.component('index-parent-field', require('./components/IndexField').default);
  Vue.component('detail-parent-field', require('./components/DetailField').default);
  Vue.component('form-parent-field', require('./components/FormField').default);
});
