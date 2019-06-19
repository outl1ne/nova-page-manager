<template>
  <button type="button" class="ml-3 btn btn-default btn-primary" v-on:click="publish">Publish</button>
</template>

<script>
export default {
  props: ['pageId'],

  methods: {
    publish() {
      Nova.request()
        .post(`/nova-vendor/nova-page-manager/publish/${this.pageId}`)
        .then(
          response => {
            const cb = () => {
              this.$toasted.show('Draft successfully published!', { type: 'success' });
            };

            if (this.pageId === response.data.id) {
              this.$router.go(null, cb);
            } else {
              this.$router.push(`/resources/pages/${response.data.id}`, cb);
            }
          },
          () => {
            this.$toasted.show('Failed to publish draft!', { type: 'error' });
          }
        );
    },
  },
};
</script>
