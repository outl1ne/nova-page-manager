Nova.booting((Vue, router, store) => {
  Vue.component('index-template-field', require('./template-field/components/IndexField').default);
  Vue.component('detail-template-field', require('./template-field/components/DetailField').default);
  Vue.component('form-template-field', require('./template-field/components/FormField').default);

  Vue.component('index-region-field', require('./region-field/components/IndexField').default);
  Vue.component('detail-region-field', require('./region-field/components/DetailField').default);
  Vue.component('form-region-field', require('./region-field/components/FormField').default);

  Vue.component('form-prefix-field', require('./prefix-field/components/FormField').default);

  Vue.component('index-parent-field', require('./parent-field/components/IndexField').default);
  Vue.component('detail-parent-field', require('./parent-field/components/DetailField').default);
  Vue.component('form-parent-field', require('./parent-field/components/FormField').default);

  router.addRoutes([
    {
      name: 'nova-page-manager',
      path: '/nova-page-manager',
      component: require('./page-manager-tool/components/Tool').default,
    },
  ]);
});
