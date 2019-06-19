Nova.booting((Vue, router, store) => {
  Vue.component('index-published-field', require('./components/IndexField'));
  Vue.component('detail-published-field', require('./components/DetailButton'));
});
