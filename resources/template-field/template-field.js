Nova.booting((Vue, router, store) => {
  Vue.component('index-template-field', require('./components/IndexField').default);
  Vue.component('detail-template-field', require('./components/DetailField').default);
  Vue.component('form-template-field', require('./components/FormField').default);
});
