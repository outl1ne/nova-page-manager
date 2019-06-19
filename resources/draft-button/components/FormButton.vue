<template>
  <div>
    <button
      ref="draftButton"
      type="button"
      class="ml-3 btn btn-default btn-primary"
      v-on:click="createDraft"
    >Create draft</button>

    <input name="draft" v-model="draft" type="hidden">
  </div>
</template>

<script>
import { FormField, HandlesValidationErrors } from 'laravel-nova';

export default {
  mixins: [FormField, HandlesValidationErrors],
  props: ['resourceName', 'resourceId', 'resource', 'field'],

  data() {
    return {
      draft: void 0,
    };
  },

  beforeMount() {
    if (this.field.childDraft && this.field.childDraft.id) {
      this.$router.replace(`/resources/pages/${this.field.childDraft.id}/edit`);
    }
  },

  mounted() {
    this.actionButton.parentNode.append(this.$refs.draftButton);
  },

  methods: {
    fill(formData) {
      if (this.draft) {
        formData.append(this.field.attribute, this.draft);
      }
    },

    createDraft() {
      this.draft = true;

      this.$nextTick(() => {
        this.actionButton.click();
      });
    },
  },

  computed: {
    isExisting() {
      return this.resourceId !== null && this.resourceId !== undefined;
    },

    actionButton() {
      return document
        .querySelector('.content')
        .querySelector(`[dusk="${this.isExisting ? 'update-button' : 'create-button'}"]`);
    },
  },
};
</script>
