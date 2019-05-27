<template>
  <localization-options field="field" resource-name="resourceName" />
</template>

<script>
import LocalizationOptions from './LocalizationOptions';

export default {
  components: { LocalizationOptions },
  props: ['resourceName', 'field'],
};
</script>
