<template>
  <panel-item :field="field">
    <template slot="value">
      <localization-options :field="field" :resource="resource" :resource-id="resourceId" :resource-name="resourceName" />
    </template>
  </panel-item>
</template>

<script>
import LocalizationOptions from './LocalizationOptions';

export default {
  components: { LocalizationOptions },
  props: ['resource', 'resourceName', 'resourceId', 'field'],
};
</script>
