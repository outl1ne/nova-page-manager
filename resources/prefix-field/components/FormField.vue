<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <div class="flex align-items-center">
        <input
          v-if="field.path"
          class="mt-1 text-gray form-control form-input-bordered bg-20 rounded-r-none px-2 border-r-0"
          style="min-width: 0"
          :value="field.path"
          disabled
          readonly
        />
        <input
          :id="field.name"
          type="text"
          class="w-full form-control form-input form-input-bordered mt-1"
          :class="inputClasses"
          :placeholder="field.name"
          v-model="value"
          :disabled="isReadonly"
          ref="theInput"
        />

        <button
          class="btn btn-link rounded px-1 py-1 inline-flex text-sm text-primary ml-1 mt-2"
          v-if="field.showCustomizeButton"
          type="button"
          @click="toggleCustomizeClick"
        >
          {{ __('novaPageManager.Customize') }}
        </button>
      </div>
    </template>
  </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import lowerCase from 'lodash/lowerCase';
import slug from 'slugify';

export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ['resourceName', 'resourceId', 'field'],

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
    inputClasses() {
      const inputClasses = [...this.errorClasses];
      if (this.field.path) inputClasses.push('rounded-l-none');
      return inputClasses;
    },

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

<style lang="scss" scoped>
.text-gray {
  color: #7c858e;
}
</style>
