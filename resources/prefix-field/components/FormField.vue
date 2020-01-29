<template>
  <default-field :field="field" :errors="errors">
    <template slot="field">
      <div class="flex align-items-center">
        <input
          v-if="field.path"
          class="mt-1 text-gray form-control form-input-bordered bg-20 rounded-r-none px-2 border-r-0"
          style="min-width: 0;"
          :value="field.path"
          disabled
          readonly
        />
        <input
          :id="field.name"
          type="text"
          class="w-full form-control form-input form-input-bordered mt-1"
          :class="inputClasses"
          :placeholder="field.name"
          v-model="value"
        />
      </div>
    </template>
  </default-field>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';

export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ['resourceName', 'resourceId', 'field'],
  computed: {
    inputClasses() {
      const inputClasses = [...this.errorClasses];
      if (this.field.path) inputClasses.push('rounded-l-none');
      return inputClasses;
    },
  },
  methods: {
    setInitialValue() {
      this.value = this.field.value || '';
    },

    fill(formData) {
      formData.append(this.field.attribute, this.value || '');
    },

    handleChange(value) {
      this.value = value;
    },
  },
};
</script>

<style lang="scss" scoped>
.text-gray {
  color: #7c858e;
}
</style>
