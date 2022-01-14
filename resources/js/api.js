export default {
  async getPage(pageId, locale) {
    return Nova.request().get(`/nova-vendor/nova-page-manager/page/${pageId}`, { params: { locale } });
  },

  async getRegion(regionId, locale) {
    return Nova.request().get(`/nova-vendor/nova-page-manager/region/${regionId}`, { params: { locale } });
  },
};
