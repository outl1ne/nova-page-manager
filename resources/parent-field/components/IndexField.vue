<template>
  <span>{{ value }}</span>
</template>

<script>
export default {
  props: ['resourceName', 'field'],
  computed: {
    value() {
      if (!this.field.parent) return '-';
      return `${this.field.parent.name} (${this.field.parent.slug})`;
    },
  },
};
</script>
