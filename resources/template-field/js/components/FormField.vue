<template>
  <default-field :field="field" :errors="errors">
      <template slot="field">
          <select name="template" class="w-full form-control form-input form-input-bordered" v-model="template" :disabled="hasParentResourceTemplate">
              <option value="">Choose a template</option>
              <option :value="template.value" v-for="template in field.templates" :key="template.value">{{ template.label }}</option>
          </select>
      </template>
  </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ["resourceName", "resourceId", "field"],

  data() {
    return {
      template: void 0
    };
  },

  computed: {
    hasParentResourceTemplate() {
      if (this.field.value) return true;
      const parentId = this.getParameterByName("parentId");
      if (parentId === null || !this.field.resourceTemplates) return false;
      return this.field.resourceTemplates[parentId] !== null;
    }
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      const template =
        this.field.resourceTemplates &&
        this.getParameterByName("parentId") &&
        this.field.resourceTemplates[this.getParameterByName("parentId")];
      this.template = this.field.value || template;
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      formData.append(this.field.attribute, this.template);
    },

    getParameterByName(name) {
      const url = window.location.href;
      name = name.replace(/[\[\]]/g, "\\$&");
      const regex = new RegExp("[?&]" + name + "(=([^&#]*)|&|#|$)");
      const results = regex.exec(url);
      if (!results) return null;
      if (!results[2]) return "";
      return decodeURIComponent(results[2].replace(/\+/g, " "));
    }
  }
};
</script>
