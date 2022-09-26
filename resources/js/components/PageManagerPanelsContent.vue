<template>
  <div class="o1-relative o1-w-full" v-if="hasFields">
    <PageManagerFieldHeader :locales="locales" :activeLocale="locale" @changeLocale="changeLocale" />

    <template v-for="(localeName, key) in locales" :key="key">
      <div v-show="locale === key">
        <component
          class="o1-py-6"
          v-for="panel in panelsWithFields[key]"
          :key="panel.id"
          :is="view + '-' + panel.component"
          @field-changed="(e) => $emit('field-changed', e)"
          :panel="panel"
          :name="panel.name"
          :resource-id="resourceId"
          :resource-name="`page-manager/${type}/${resourceName}/${key}`"
          :form-unique-id="formUniqueId"
          mode="form"
          :validation-errors="validationErrors"
        />
      </div>
    </template>
  </div>
</template>

<script>
import PageManagerFieldHeader from './PageManagerFieldHeader';

export default {
  components: { PageManagerFieldHeader },
  props: [
    'resourceName',
    'resourceId',
    'field',
    'locales',
    'panelsWithFields',
    'view',
    'type',
    'formUniqueId',
    'validationErrors',
  ],

  data: () => ({
    locale: void 0,
  }),

  beforeMount() {
    this.locale = Object.keys(this.locales)[0];
  },

  methods: {
    changeLocale(locale) {
      this.locale = locale;
    },
  },

  computed: {
    hasFields() {
      return (this.panelsWithFields[this.locale] || []).length > 0;
    },
  },
};
</script>
