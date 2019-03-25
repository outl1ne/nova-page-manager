<template>
    <div v-if="field.value.locales">
        <span v-for="locale in Object.keys(field.value.locales)" :key="locale">
            <!-- Edit link -->
            <router-link
                v-if="field.value.locales[locale]"
                class="cursor-pointer text-70 hover:text-primary mr-3"
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
                <icon type="edit" /> {{ locale }}
            </router-link>

            <!-- Create link -->
            <router-link
                v-else-if="field.value.locale !== locale"
                dusk="create-button"
                :class="classes"
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
  }
};
</script>
