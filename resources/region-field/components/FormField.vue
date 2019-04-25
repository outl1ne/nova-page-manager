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
import { getParameterByName } from '../../js/util';

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
      return !(this.field.value || getParameterByName('localeParentId') !== void 0);
    },
  },

  methods: {
    setInitialValue() {
      const localeParentId = getParameterByName('localeParentId');
      this.region = localeParentId ? this.field.existingRegions[localeParentId] : this.field.value;
    },

    fill(formData) {
      formData.append(this.field.attribute, this.region);
      if (this.hasLocaleParentId) formData.append('slug', `${this.region}_${new Date().getTime()}`);
    },
  },
};
</script>
