<template>
    <panel-item :field="{ name: 'Localization' }">
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
      return this.field.resources[this.field.value.locale_parent_id] || '-';
    },
  },
};
</script>
