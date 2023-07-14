<template>
  <div ref="field" class="o1-w-full">
    <PageManagerPanelsContent
      v-if="seoPanelsWithFields"
      :view="field.view"
      :type="'seo'"
      :resourceId="resourceId"
      :resourceName="resourceName"
      @field-changed="onUpdateFormStatus"
      :validationErrors="validationErrors"
      :panelsWithFields="seoPanelsWithFields"
      :locales="field.locales"
    />

    <PageManagerPanelsContent
      :view="field.view"
      :type="'data'"
      :resourceId="resourceId"
      :resourceName="resourceName"
      @field-changed="onUpdateFormStatus"
      :validationErrors="validationErrors"
      :locales="field.locales"
      :panelsWithFields="panelsWithFields"
    />
  </div>
</template>

<script>
import API from '../api';
import { FormField } from 'laravel-nova';
import PageManagerPanelsContent from './PageManagerPanelsContent';

const FLEXIBLE_KEY = '___nova_flexible_content_fields';

export default {
  mixins: [FormField],
  components: { PageManagerPanelsContent },
  props: ['resourceName', 'resourceId', 'field'],

  data: () => ({
    locale: void 0,
    loading: false,
    panelsWithFields: {},
    seoPanelsWithFields: null,
  }),

  beforeMount() {
    this.locale = Object.keys(this.field.locales)[0];
  },

  mounted() {
    this.refreshFields();
    if (this.$refs.field) {
      this.$refs.field.parentElement.style = 'background: none; padding: 0; box-shadow: none;';
      this.$refs.field.parentElement.parentElement.querySelector('h1').style = 'display: none;';
    }
  },

  methods: {
    async refreshFields() {
      this.loading = true;
      const { data } = await API.getFields(this.field.type, this.resourceId, this.field.view);
      this.panelsWithFields = data.panelsWithFields;
      this.seoPanelsWithFields = data.seoPanelsWithFields;
      this.loading = false;
    },

    changeLocale(locale) {
      this.locale = locale;
    },

    fill(formData) {
      try {
        this.addFieldValuesToFormData(this.panelsWithFields, 'data', formData);
      } catch (e) {
        console.error(e);
      }

      try {
        this.addFieldValuesToFormData(this.seoPanelsWithFields, 'seo', formData);
      } catch (e) {
        console.error(e);
      }
    },

    addFieldValuesToFormData(fields, keyPrefix, formData) {
      const localizedData = this.getDataFromFill(fields);
      const localeKeys = Object.keys(localizedData);

      for (const locale of localeKeys) {
        const data = localizedData[locale];
        const dataKeys = Object.keys(data);

        for (const key of dataKeys) {
          const val = data[key];
          const isFile = val instanceof File || val instanceof Blob;

          if (typeof val === 'object' && !isFile) {
            const objKeys = Object.keys(val);
            for (const objKey of objKeys) {
              formData.set(`${keyPrefix}[${locale}][${key}][${objKey}]`, val[objKey]);
            }
          } else {
            const matches = key.match(/(\[[\da-zA-Z]+\])/g);
            if (matches && matches.length) {
              let newKey = key;

              // Remove matches from key
              matches.forEach((match) => (newKey = newKey.replace(match, '')));

              // Append them to formData key
              newKey = `${keyPrefix}[${locale}][${newKey}]`;
              matches.forEach((match) => (newKey = `${newKey}${match}`));

              formData.set(newKey, val);
            } else {
              formData.set(`${keyPrefix}[${locale}][${key}]`, val);
            }
          }
        }
      }
    },

    getDataFromFill(panelsWithFields) {
      const localeKeys = Object.keys(this.field.locales);

      const formDataToRealData = (formData) => {
        const data = {};

        for (const key of formData.keys()) {
          const [realKey, realValue] = this.getKeyAndValue(key, formData);

          if (this.isKeyAnArray(key)) {
            if (!data[realKey]) data[realKey] = [];
            data[realKey].push(realValue);
          } else {
            data[realKey] = realValue;
          }
        }

        return data;
      };

      const data = {};
      for (const panel of panelsWithFields) {
        if (panel.npmDoNotTranslate) {
          const fd = new FormData();
          for (const field of panel.fields) {
            if (field.fill) field.fill(fd);
          }

          if (data['__']) {
            let newData = formDataToRealData(fd);
            newData = this.handleFlexibleKeyIfNecessary(data['__'], newData);
            data['__'] = { ...data['__'], ...newData };
          } else {
            data['__'] = formDataToRealData(fd);
          }
        } else {
          for (const key of localeKeys) {
            const fd = new FormData();
            for (const field of panel.fields[key]) {
              field.fill(fd);
            }

            if (data[key]) {
              let newData = formDataToRealData(fd);
              newData = this.handleFlexibleKeyIfNecessary(data[key], newData);
              data[key] = { ...data[key], ...newData };
            } else {
              data[key] = formDataToRealData(fd);
            }
          }
        }
      }

      return data;
    },

    handleFlexibleKeyIfNecessary(existingData, newData) {
      if (newData[FLEXIBLE_KEY]) {
        if (existingData[FLEXIBLE_KEY]) {
          const existingKeys = JSON.parse(existingData[FLEXIBLE_KEY]);
          const newKeys = JSON.parse(newData[FLEXIBLE_KEY]);
          newData[FLEXIBLE_KEY] = JSON.stringify([...existingKeys, ...newKeys]);
        }
      }
      return newData;
    },

    getKeyAndValue(key, formData) {
      if (this.isKeyAnArray(key)) {
        const result = /\[\d+\]$/g.exec(key);
        return [key.slice(0, result.index), formData.get(key)];
      } else if (key.endsWith('[]')) {
        return [key.replace('[]', ''), formData.getAll(key)];
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
