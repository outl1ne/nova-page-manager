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
      const { data } = await API.getFields(this.field.type, this.resourceId);
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
            formData.set(`${keyPrefix}[${locale}][${key}]`, val);
          }
        }
      }
    },

    getDataFromFill(panelsWithFields) {
      const localeKeys = Object.keys(this.field.locales);

      const data = {};
      for (const key of localeKeys) {
        const localeData = {};
        const panels = panelsWithFields[key];
        const fd = new FormData();

        for (const panel of panels) {
          for (const field of panel.fields) {
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

      return data;
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
