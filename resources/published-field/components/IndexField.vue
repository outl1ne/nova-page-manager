<template>
  <publish-indicator :published="isPublished" :draft="isDraft"/>
</template>

<script>
import PublishIndicator from './PublishIndicator';

export default {
  components: { PublishIndicator },
  props: ['resourceName', 'field'],

  computed: {
    isPublished() {
      return !!this.field.value || !!this.field.draftParent;
    },

    isDraft() {
      return !this.field.value || (this.field.value && (this.field.childDraft || this.field.draftParent));
    },
  },
};
</script>
