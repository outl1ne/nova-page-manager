<template>
    <panel-item :field="{ name: 'Parent' }">
      <template slot="value">
        {{ displayValue }}
      </template>
    </panel-item>
</template>

<script>
export default {
  props: ["resource", "resourceName", "resourceId", "field"],
  computed: {
    displayValue() {
      return this.field.resources[this.field.value.parent_id] || "-";
    }
  }
};
</script>
