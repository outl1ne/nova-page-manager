Nova.booting((Vue, router, store) => {
  Vue.component('form-draft-button', require('./components/FormButton').default);
  Vue.component('detail-draft-button', require('./components/DetailButton').default);
});
