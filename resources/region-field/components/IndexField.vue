<template>
  <span>{{ value }}</span>
</template>

<script>
export default {
  props: ['resourceName', 'field'],
  computed: {
    value() {
      return this.field.value;
    },
  },
};
</script>
