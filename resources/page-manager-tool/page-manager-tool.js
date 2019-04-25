Nova.booting((Vue, router, store) => {
  router.addRoutes([
    {
      name: 'nova-page-manager',
      path: '/nova-page-manager',
      component: require('./components/Tool'),
    },
  ]);
});
