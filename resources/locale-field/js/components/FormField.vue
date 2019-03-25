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
import { FormField, HandlesValidationErrors } from "laravel-nova";

export default {
  mixins: [FormField, HandlesValidationErrors],

  props: ["resourceName", "resourceId", "field"],

  data() {
    return {
      locale: void 0
    };
  },

  computed: {
    localeAlreadySet() {
      return this.field.value || this.getParameterByName("locale") !== null;
    }
  },

  methods: {
    /*
     * Set the initial, internal value for the field.
     */
    setInitialValue() {
      this.locale = this.field.value || this.getParameterByName("locale");
    },

    /**
     * Fill the given FormData object with the field's internal value.
     */
    fill(formData) {
      formData.append(this.field.attribute, this.locale);
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
