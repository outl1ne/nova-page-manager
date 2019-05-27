<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <input type="text" :value="displayValue" readonly class="w-full form-control form-input form-input-bordered" />
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
      value: void 0,
    };
  },

  computed: {
    displayValue() {
      return this.field.resources[this.value] || '-';
    },
  },

  methods: {
    setInitialValue() {
      this.value = (this.field.value && this.field.value.locale_parent_id) || getParameterByName('localeParentId');
    },

    fill(formData) {
      if (this.value) formData.append(this.field.attribute, this.value);
    },
  },
};
</script>
