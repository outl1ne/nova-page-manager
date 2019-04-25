<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <select name="locale" class="w-full form-control form-input form-input-bordered" v-model="region" v-if="canEditRegion">
        <option value="null">Choose a region</option>
        <option :value="region" v-for="region in field.regions" :key="region">{{ region }}</option>
      </select>
      <input v-else class="w-full form-control form-input form-input-bordered" type="text" :disabled="true" :value="region">
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
      region: void 0,
    };
  },

  computed: {
    canEditRegion() {
      return !(this.field.value || this.getParameterByName('localeParentId') !== void 0);
    },
  },

  methods: {
    setInitialValue() {
      const localeParentId = this.getParameterByName('localeParentId');
      this.region = localeParentId ? this.field.existingRegions[localeParentId] : this.field.value;
    },

    fill(formData) {
      formData.append(this.field.attribute, this.region);
      if (this.hasLocaleParentId) formData.append('slug', `${this.region}_${new Date().getTime()}`);
    },

    getParameterByName(name) {
      const url = window.location.href;
      name = name.replace(/[\[\]]/g, '\\$&');
      const regex = new RegExp('[?&]' + name + '(=([^&#]*)|&|#|$)');
      const results = regex.exec(url);
      if (!results) return void 0;
      if (!results[2]) return '';
      return decodeURIComponent(results[2].replace(/\+/g, ' '));
    },
  },
};
</script>
