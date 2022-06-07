export default {
  async getFields(type, resourceId) {
    return Nova.request().get(`/nova-vendor/page-manager/${type}/${resourceId}/fields`);
  },
};
