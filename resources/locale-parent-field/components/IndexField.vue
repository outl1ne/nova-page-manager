<template>
  <localization-options :field="field" :resource-name="resourceName" style="margin: 10px 0;" />
</template>

<script>
import LocalizationOptions from './LocalizationOptions';

export default {
  components: { LocalizationOptions },
  props: ['resourceName', 'field'],
};
</script>