export default {
  async getFields(type, resourceId, view) {
    const query = new URLSearchParams({view})
    return Nova.request().get(`/nova-vendor/page-manager/${type}/${resourceId}/fields?${query.toString()}`);
  },
};
