<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <select
        name="template"
        class="w-full form-control form-input form-input-bordered"
        v-model="template"
        :disabled="hasParentResourceTemplate"
      >
        <option value="">Choose a template</option>
        <option :value="template.value" v-for="template in field.templates" :key="template.value">{{
          template.label
        }}</option>
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
      template: void 0,
    };
  },

  computed: {
    hasParentResourceTemplate() {
      if (this.field.value) return true;
      const localeParentId = getParameterByName('localeParentId');
      if (!localeParentId || !this.field.resourceTemplates) return false;
      return !!this.field.resourceTemplates[localeParentId];
    },
  },

  methods: {
    setInitialValue() {
      const localeParentId = getParameterByName('localeParentId');
      const template = this.field.resourceTemplates && localeParentId && this.field.resourceTemplates[localeParentId];
      this.template = this.field.value || template || '';
    },

    fill(formData) {
      if (this.template) formData.append(this.field.attribute, this.template);
    },
  },
};
</script>
