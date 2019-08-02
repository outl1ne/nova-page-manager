<template>
  <button
    ref="deleteDraftButton"
    type="button"
    class="mr-3 btn btn-default btn-danger"
    v-if="isDraft"
    v-on:click="discard"
  >Discard draft</button>
</template>

<script>
import { InteractsWithResourceInformation, Deletable } from 'laravel-nova';

export default {
  mixins: [Deletable, InteractsWithResourceInformation],

  props: ['resource', 'resourceId', 'field', 'resourceName'],

  mounted() {
    if (this.isDraft) {
      this.deleteButton.parentNode.replaceChild(this.$refs.deleteDraftButton, this.deleteButton);
    }
  },

  beforeMount() {
    if (this.field.childDraft && this.field.childDraft.id) {
      this.$router.replace(`/resources/pages/${this.field.childDraft.id}`);
      this.$nextTick(this.$parent.$parent.getFields); // ! Might break with new Laravel Nova versions
    }
  },

  computed: {
    deleteButton() {
      return document.querySelector('.content').querySelector('[dusk=open-delete-modal-button]');
    },

    isDraft() {
      return this.field.isDraft;
    },
  },

  methods: {
    discard() {
      this.forceDeleteResources([this.resource], () => {
        this.$toasted.show(this.__('The page draft was discarded!'), { type: 'success' });
        this.$router.push({ name: 'index', params: { resourceName: this.resourceName } });
      });
    },
  },
};
</script>

<style scoped>
.btn-danger {
  white-space: pre;
}
</style>
