<template>
  <div
    class="npm-p-2 npm-font-mono whitespace-no-wrap npm-text-xs npm-bg-gray-100 dark:npm-bg-gray-900 npm-rounded npm-border npm-border-gray-200 dark:npm-border-gray-700 npm-inline-block"
  >
    {{ path }}
  </div>

  <a
    v-if="baseUrl"
    :href="fullUrl"
    target="_BLANK"
    rel="noopener noreferrer"
    class="text-primary-500 npm-font-bold npm-ml-3 hover:npm-opacity-80"
    @click.stop
  >
    {{ __('novaPageManager.pageLinkViewButton') }}
  </a>
</template>

<script>
export default {
  props: ['field'],

  computed: {
    path() {
      return this.field.value;
    },

    baseUrl() {
      return this.field.baseUrl;
    },

    fullUrl() {
      return `${this.baseUrl}${this.path}`;
    },
  },
};
</script>
