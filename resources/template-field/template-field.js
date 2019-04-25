Nova.booting((Vue, router, store) => {
  Vue.component('index-template-field', require('./components/IndexField'));
  Vue.component('detail-template-field', require('./components/DetailField'));
  Vue.component('form-template-field', require('./components/FormField'));
});
