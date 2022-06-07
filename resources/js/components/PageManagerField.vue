<template>
  <div id="page-manager-field" class="npm-relative npm-py-6" ref="field">
    <PageManagerFieldHeader :locales="field.locales" :activeLocale="locale" @changeLocale="changeLocale" />

    <template v-for="(localeName, key) in field.locales" :key="key">
      <div v-show="locale === key">
        <component
          v-for="panel in panelsWithFields[key]"
          :key="panel.id"
          :is="field.view + '-' + panel.component"
          @field-changed="onUpdateFormStatus"
          :panel="panel"
          :name="panel.name"
          :resource-id="resourceId"
          :resource-name="resourceName"
          :form-unique-id="formUniqueId"
          mode="form"
          :validation-errors="validationErrors"
        />
      </div>
    </template>
  </div>
</template>

<script>
import API from '../api';
import { FormField } from 'laravel-nova';
import PageManagerFieldHeader from './PageManagerFieldHeader';

export default {
  mixins: [FormField],
  components: { PageManagerFieldHeader },
  props: ['resourceName', 'resourceId', 'field'],

  data: () => ({
    locale: void 0,
    loading: false,
    panelsWithFields: {},
  }),

  beforeMount() {
    console.info(this);
    this.locale = Object.keys(this.field.locales)[0];
  },

  mounted() {
    this.refreshFields();
    if (this.$refs.field) {
      this.$refs.field.parentElement.style = 'background: none';
      this.$refs.field.parentElement.parentElement.querySelector('h1').style = 'display: none;';
    }
  },

  methods: {
    async refreshFields() {
      this.loading = true;
      const { data } = await API.getFields(this.field.type, this.resourceId);
      this.panelsWithFields = data.panelsWithFields;
      this.loading = false;
    },

    changeLocale(locale) {
      this.locale = locale;
    },

    fill(formData) {
      const ARR_REGEX = () => /\[\d+\]$/g;

      try {
        const localeKeys = Object.keys(this.field.locales);

        const data = {};
        for (const key of localeKeys) {
          const localeData = {};
          const panels = this.panelsWithFields[key];
          for (const panel of panels) {
            for (const field of panel.fields) {
              const fd = new FormData();
              field.fill(fd);

              for (const key of fd.keys()) {
                const [realKey, realValue] = this.getKeyAndValue(key, fd);

                if (this.isKeyAnArray(key)) {
                  if (!localeData[realKey]) localeData[realKey] = [];
                  localeData[realKey].push(realValue);
                } else {
                  localeData[realKey] = realValue;
                }
              }
            }
          }
          data[key] = localeData;
        }

        formData.set('data', JSON.stringify(data));
      } catch (e) {
        console.error(e);
      }
    },

    getKeyAndValue(key, formData) {
      if (this.isKeyAnArray(key)) {
        const result = /\[\d+\]$/g.exec(key);
        return [key.slice(0, result.index), formData.get(key)];
      } else {
        return [key, formData.get(key)];
      }
    },

    isKeyAnArray(key) {
      return !!key.match(/\[\d+\]$/g);
    },
  },
};
</script>
