<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <select name="locale" class="w-full form-control form-input form-input-bordered" v-model="locale" :disabled="localeAlreadySet">
        <option value="">Choose a locale</option>
        <option :value="locale.value" v-for="locale in field.locales" :key="locale.value">{{ locale.label }}</option>
      </select>
    </template>
  </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';
import { getParameterByName } from '../../js/util';

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ['resourceName', 'resourceId', 'field'],

  data() {
    return {
      locale: void 0,
    };
  },

  computed: {
    localeAlreadySet() {
      return this.field.value || !!getParameterByName('locale');
    },
  },

  methods: {
    setInitialValue() {
      this.locale = this.field.value || getParameterByName('locale') || '';
    },

    fill(formData) {
      if (this.locale) formData.append(this.field.attribute, this.locale);
    },
  },
};
</script>
