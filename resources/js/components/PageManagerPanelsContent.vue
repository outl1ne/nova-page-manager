<template>
  <div class="o1-relative o1-w-full">
    <template v-for="(panel, i) in panelsWithFields" :key="i">
      <template v-if="!panel.npmDoNotTranslate && Object.values(panel.fields)[0].length">
        <PageManagerFieldHeader :locales="locales" :activeLocale="locale" @changeLocale="changeLocale" />

        <template v-for="(localeName, key) in locales" :key="key">
          <div v-show="locale === key">
            <component
              class="o1-py-6"
              :key="panel.id"
              :is="view + '-' + panel.component"
              @field-changed="(e) => $emit('field-changed', e)"
              :panel="{ ...panel, fields: panel.fields[key] }"
              :name="panel.name"
              :resource-id="resourceId"
              :resource-name="`page-manager/${type}/${resourceName}/${key}`"
              :form-unique-id="formUniqueId"
              mode="form"
              :validation-errors="validationErrors"
              :show-help-text="true"
            />
          </div>
        </template>
      </template>

      <template v-else>
        <component
          class="o1-py-6"
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
          :show-help-text="true"
        />
      </template>
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
