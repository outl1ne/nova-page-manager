Nova.booting((Vue, router, store) => {
  Vue.component('index-locale-field', require('./components/IndexField'));
  Vue.component('detail-locale-field', require('./components/DetailField'));
  Vue.component('form-locale-field', require('./components/FormField'));
});
