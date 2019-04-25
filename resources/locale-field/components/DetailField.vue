<template>
    <panel-item :field="field">
        <template slot="value">
            {{ value }} ({{ field.value }})
        </template>
    </panel-item>
</template>

<script>
export default {
  props: ['resource', 'resourceName', 'resourceId', 'field'],
  computed: {
    value() {
      return this.field.locales[this.field.value].label;
    },
  },
};
</script>
