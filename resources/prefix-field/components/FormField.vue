<template>
    <default-field :field="field" :errors="errors">
        <template slot="field">
          <div class="flex">
            <p class="mb-2 mt-3 text-gray">{{ field.path }}</p>
            <span v-if="field.path" class="mr-2"></span>
            <input
                :id="field.name"
                type="text"
                class="w-full form-control form-input form-input-bordered mt-1"
                :class="errorClasses"
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
