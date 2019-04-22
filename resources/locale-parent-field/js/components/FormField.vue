<template>
  <default-field :field="{ name: 'Localization' }" :errors="errors">
    <template slot="field">
      <input type="text" :value="displayValue" readonly class="w-full form-control form-input form-input-bordered">
    </template>
  </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  data() {
    return {
      value: void 0,
    };
  },

  computed: {
    displayValue() {
      return this.field.resources[this.value] || '-';
    },
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.value = this.field.value || this.getParameterByName('localeParentId');
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      if (this.value !== null) {
        formData.append(this.field.attribute, this.value);
      }
    },

    getParameterByName(name) {
      const url = window.location.href;
      name = name.replace(/[\[\]]/g, '\\$&');
      const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
      const results = regex.exec(url);
      if (!results) return null;
      if (!results[2]) return '';
      return decodeURIComponent(results[2].replace(/\+/g, ' '));
    },
  },
};
</script>
