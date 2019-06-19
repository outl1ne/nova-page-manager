<script>
export default {
  props: ['resource', 'resourceName', 'resourceId', 'field'],

  mounted() {
    if (this.field.childDraft && this.field.childDraft.id) {
      this.$router.replace(`/resources/pages/${this.field.childDraft.id}`);
    }
  },
};
</script>
