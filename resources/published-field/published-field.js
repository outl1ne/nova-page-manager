Nova.booting((Vue, router, store) => {
  Vue.component('index-published-field', require('./components/IndexField').default);
  Vue.component('detail-published-field', require('./components/DetailButton').default);
});
