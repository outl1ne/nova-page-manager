<template>
  <PanelItem :index="index" :field="field">
    <template #value>
      <PageLinkFieldContent :field="field" />
    </template>
  </PanelItem>
</template>

<script>
import PageLinkFieldContent from './PageLinkFieldContent';

export default {
  props: ['resourceName', 'resourceId', 'field', 'index'],
  components: { PageLinkFieldContent },
};
</script>
