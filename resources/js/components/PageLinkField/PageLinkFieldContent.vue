<template>
  <div
    class="o1-p-2 o1-font-mono whitespace-no-wrap o1-text-xs o1-bg-gray-100 dark:o1-bg-gray-900 o1-rounded o1-border o1-border-gray-200 dark:o1-border-gray-700 o1-inline-block"
  >
    {{ field.value }}
  </div>

  <a
    v-if="fullUrl"
    :href="fullUrl"
    target="_BLANK"
    rel="noopener noreferrer"
    class="text-primary-500 o1-font-bold o1-ml-3 hover:o1-opacity-80"
    @click.stop
  >
    {{ __('novaPageManager.pageLinkViewButton') }}
  </a>
</template>

<script>
export default {
  props: ['field', 'locale'],

  computed: {
    fullUrl() {
      if (this.field.pageUrl) {
        return this.field.pageUrl[this.locale];
      }
      return void 0;
    },
  },
};
</script>
