<template>
  <span>{{ value }} ({{ field.value }})</span>
</template>

<script>
export default {
  props: ['resourceName', 'field'],
  computed: {
    value() {
      return this.field.locales[this.field.value].label;
    },
  },
};
</script>
