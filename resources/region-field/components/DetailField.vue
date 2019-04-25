<template>
  <panel-item :field="field">
    <template slot="value">
      {{ value }}
    </template>
  </panel-item>
</template>

<script>
export default {
  props: ['resource', 'resourceName', 'resourceId', 'field'],
  computed: {
    value() {
      return this.field.value;
    },
  },
};
</script>
