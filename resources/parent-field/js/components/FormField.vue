<template>
  <default-field :field="field" :errors="errors" v-if="canHaveParent">
    <template slot="field">
      <select name="parent" class="w-full form-control form-input form-input-bordered" v-model="parent">
        <option value="">Choose parent</option>
        <option :value="option.value" v-for="option in options" :key="option.value">{{ option.label }}</option>
      </select>
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
      parent: void 0,
    };
  },

  computed: {
    canHaveParent() {
      return this.field.canHaveParent && this.getParameterByName('localeParentId') === null;
    },
    options() {
      const ids = Object.keys(this.field.options).filter(id => id !== this.resourceId);
      return ids.map(id => ({
        value: id,
        label: this.field.options[id],
      }));
    },
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.parent = this.field.value || '';
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      formData.append(this.field.attribute, this.parent);
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
