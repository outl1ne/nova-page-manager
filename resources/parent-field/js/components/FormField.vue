<template>
  <default-field :field="field" :errors="errors" v-if="canHaveParent">
    <template slot="field">
      <select name="parent" class="w-full form-control form-input form-input-bordered" v-model="parent">
        <option value="null">Choose parent</option>
        <option :value="pageId" v-for="pageId in Object.keys(field.options)" :key="pageId">{{ field.options[pageId] }}</option>
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

  mounted() {
    console.info(this);
  },

  computed: {
    canHaveParent() {
      return this.field.canHaveParent && this.getParameterByName('localeParentId') === null;
    },
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.parent = this.field.value;
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      if (this.parent) formData.append(this.field.attribute, this.parent);
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
