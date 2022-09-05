<template>
  <DefaultField :field="field" :errors="errors">
    <template #field>
      <div class="o1-flex o1-items-center">
        <div
          v-if="prefix"
          class="form-control form-input form-input-bordered o1-flex o1-items-center o1-cursor-default o1-rounded-r-none o1-border-r-0 o1-font-mono o1-whitespace-nowrap"
        >
          {{ prefix }}
        </div>

        <input
          :id="field.name"
          type="text"
          v-model="value"
          :placeholder="field.name"
          :readonly="field.readonly"
          class="w-full form-control form-input form-input-bordered"
          :class="{
            errorClasses,
            'o1-rounded-l-none': !!prefix,
            'o1-rounded-r-none': !!field.pathSuffix,
          }"
          ref="theInput"
        />

        <div
          v-if="field.pathSuffix"
          class="form-control form-input form-input-bordered o1-flex o1-items-center o1-cursor-default o1-rounded-l-none o1-border-l-0 o1-font-mono"
        >
          {{ field.pathSuffix }}
        </div>

        <button
          class="o1-cursor-pointer o1-font-bold text-primary-500 o1-ml-4 hover:o1-opacity-80"
          v-if="field.showCustomizeButton"
          type="button"
          @click="toggleCustomizeClick"
        >
          {{ __('novaPageManager.customizeSlug') }}
        </button>
      </div>
    </template>
  </DefaultField>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import lowerCase from 'lodash/lowerCase';
import slug from 'slugify';

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field', 'translatableLocale'],

  mounted() {
    if (this.shouldRegisterInitialListener) {
      this.registerChangeListener();
    }
  },

  methods: {
    setInitialValue() {
      this.value = this.field.value || '';
    },

    fill(formData) {
      formData.append(this.field.attribute, this.value || '');
    },

    handleChange(value) {
      this.value = value;
    },

    changeListener(value) {
      return (value) => {
        this.value = slugify(value, this.field.separator);
      };
    },

    registerChangeListener() {
      Nova.$on(this.eventName, (value) => {
        this.value = this.slugify(value, this.field.separator);
      });
    },

    toggleCustomizeClick() {
      if (this.field.readonly) {
        Nova.$off(this.eventName);
        this.field.readonly = false;
        this.field.extraAttributes.readonly = false;
        this.field.showCustomizeButton = false;
        this.$refs.theInput.focus();
        return;
      }
      this.registerChangeListener();
      this.field.readonly = true;
      this.field.extraAttributes.readonly = true;
    },

    slugify(value, separator = '-') {
      return slug(lowerCase(value), separator);
    },
  },
  computed: {
    shouldRegisterInitialListener() {
      return !this.field.updating;
    },

    eventName() {
      const from = this.field.from.replace('*', this.translatableLocale);
      return `${from}-change`;
    },

    extraAttributes() {
      return this.field.extraAttributes || {};
    },

    prefix() {
      if (this.field.pathPrefix) return this.field.pathPrefix[this.translatableLocale];
    },
  },
};
</script>
