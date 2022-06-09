<template>
  <PageLinkFieldContent :field="field" :locale="translatableLocale" />
</template>

<script>
import PageLinkFieldContent from './PageLinkFieldContent';

export default {
  props: ['resourceName', 'resourceId', 'field', 'translatableLocale'],
  components: { PageLinkFieldContent },
};
</script>
