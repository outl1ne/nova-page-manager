<template>
  <PageLinkFieldContent :field="field" />
</template>

<script>
import PageLinkFieldContent from './PageLinkFieldContent';

export default {
  props: ['resourceName', 'resourceId', 'field'],
  components: { PageLinkFieldContent },
};
</script>
