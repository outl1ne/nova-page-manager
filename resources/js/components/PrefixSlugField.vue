<template>
  <DefaultField :field="field" :errors="errors">
    <template #field>
      <div class="npm-flex npm-items-center">
        <div
          v-if="field.pathPrefix"
          class="form-control form-input form-input-bordered npm-flex npm-items-center npm-cursor-default npm-rounded-r-none npm-border-r-0 npm-font-mono"
        >
          {{ field.pathPrefix }}
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
            'npm-rounded-l-none': !!field.pathPrefix,
            'npm-rounded-r-none': !!field.pathSuffix,
          }"
          ref="theInput"
        />

        <div
          v-if="field.pathSuffix"
          class="form-control form-input form-input-bordered npm-flex npm-items-center npm-cursor-default npm-rounded-l-none npm-border-l-0 npm-font-mono"
        >
          {{ field.pathSuffix }}
        </div>

        <button
          class="npm-cursor-pointer npm-font-bold text-primary-500 npm-ml-4 hover:npm-opacity-80"
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

  props: ['resourceName', 'resourceId', 'field'],

  mounted() {
    console.info(this.field);
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
      return `${this.field.from}-change`;
    },

    extraAttributes() {
      return this.field.extraAttributes || {};
    },
  },
};
</script>
