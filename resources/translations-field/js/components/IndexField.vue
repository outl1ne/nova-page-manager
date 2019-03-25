<template>
    <div v-if="field.value.locales">
        <span v-for="locale in locales" :key="locale" class="flex" style="margin: 2px 0;">
            <!-- Edit link -->
            <router-link
                v-if="field.value.locales[locale]"
                class="btn btn-default btn-primary flex items-center cursor-pointer select-none"
                :dusk="`${field.value.locales[locale].id}-edit-button`"
                :to="{
                    name: 'edit',
                    params: {
                        resourceName: resourceName,
                        resourceId: field.value.locales[locale].id,
                    },
                    query: {
                        viaResource: viaResource,
                        viaResourceId: viaResourceId,
                        viaRelationship: viaRelationship,
                    },
                }"
                :title="__('Edit')"
            >
                <icon type="edit" style="margin-right: 8px;" /> {{ locale }}
            </router-link>

            <!-- Create link -->
            <router-link
                v-else
                dusk="create-button"
                class="btn btn-default btn-primary flex items-center cursor-pointer select-none"
                :to="{
                    name: 'create',
                    params: {
                        resourceName: resourceName,
                    },
                    query: {
                        viaResource: viaResource,
                        viaResourceId: viaResourceId,
                        viaRelationship: viaRelationship,
                        parentId: field.value.id,
                        locale: locale
                    },
                }"
            >
                Create {{ locale }}
            </router-link>
        </span>
    </div>
</template>

<script>
export default {
  props: ["resourceName", "field"],
  methods: {
    hasChildInLocale(locale) {
      return this.field.resourceLocales[this.field.value.id].includes(locale);
    }
  },
  computed: {
    locales() {
      return Object.keys(this.field.value.locales).filter(
        l => l !== this.field.value.locale
      );
    }
  }
};
</script>
