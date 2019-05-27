<template>
  <div v-if="field.value.locales">
    <span v-for="locale in locales" :key="locale" class="flex items-center" stype="margin: 2px 0;">
      <!-- Edit link -->
      <router-link
        v-if="field.value.locales[locale]"
        class="no-underline dim text-primary font-bold flex items-center"
        :dusk="`${field.value.locales[locale].id}-edit-button`"
        :to="{
          name: 'edit',
          params: {
            resourceName: resourceName,
            resourceId: field.value.locales[locale].id,
          },
          query: {
            viaResource: resourceName,
            viaResourceId: resourceId,
            viaRelationship: $route.query.viaRelationship,
          },
        }"
        :title="__('Edit')"
      >
        <svg style="width: 20px; height: 20px; margin-right: 4px;" viewBox="0 0 24 24">
          <path
            fill="var(--primary)"
            d="M20.71,7.04C21.1,6.65 21.1,6 20.71,5.63L18.37,3.29C18,2.9 17.35,2.9 16.96,3.29L15.12,5.12L18.87,8.87M3,17.25V21H6.75L17.81,9.93L14.06,6.18L3,17.25Z"
          />
        </svg>
        {{ locale }}
      </router-link>

      <!-- Create link -->
      <router-link
        v-else
        dusk="create-button"
        class="no-underline dim text-primary font-bold flex items-center"
        :to="{
          name: 'create',
          params: {
            resourceName: resourceName,
          },
          query: {
            viaResource: resourceName,
            viaResourceId: resourceId,
            viaRelationship: $route.query.viaRelationship,
            localeParentId: field.value.id,
            locale: locale,
          },
        }"
      >
        <svg style="width: 20px; height: 20px; margin-right: 4px;" viewBox="0 0 24 24">
          <path fill="var(--primary)" d="M19,13H13V19H11V13H5V11H11V5H13V11H19V13Z" />
        </svg>
        {{ locale }}
      </router-link>
    </span>
  </div>
</template>

<script>
export default {
  props: ['resource', 'resourceName', 'resourceId', 'field'],
  computed: {
    locales() {
      return Object.keys(this.field.value.locales).filter(l => l !== this.field.value.locale);
    },
  },
};
</script>
