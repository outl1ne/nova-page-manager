<template>
  <panel-item :field="field">
    <template slot="value">
      {{ displayValue }}
    </template>
  </panel-item>
</template>

<script>
export default {
  props: ['resource', 'resourceName', 'resourceId', 'field'],
  computed: {
    displayValue() {
      return this.field.options[this.field.value] || '-';
    },
  },
};
</script>
