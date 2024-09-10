<template>
  <div id="page-manager-field-header" class="overflow-x-auto">
    <div
      class="o1-px-4 o1-py-2 o1-bg-white dark:o1-bg-gray-800 o1-rounded-lg o1-flex o1-align-center o1-justify-center"
      v-if="Object.keys(locales).length > 1"
    >
      <div
        v-for="locale of Object.keys(locales)"
        :key="locale"
        @click.prevent="$emit('changeLocale', locale)"
        class="o1-cursor-pointer o1-font-bold o1-px-2 o1-h-full o1-flex o1-items-center o1-box-border o1-border-b-2 o1-select-none whitespace-nowrap"
        :class="{
          'text-primary-500 border-primary-500': activeLocale === locale,
          'o1-text-80 o1-border-transparent': activeLocale !== locale,
        }"
      >
        <span v-html="getLocaleDisplay(locale)" />
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['locales', 'activeLocale'],

  methods: {
    getLocaleDisplay(locale) {
      const customDisplay = Nova.config('customLocaleDisplay');

      if (customDisplay && customDisplay[locale]) {
        return customDisplay[locale];
      }

      return `${this.locales[locale]} (${locale})`;
    },
  }
};
</script>

<style lang="scss">
#page-manager-field-header {
  position: absolute;
  right: 0;
  top: 14px;
  display: flex;
  max-width: 80%;

  &::-webkit-scrollbar {
    height: 5px;
  }

  &::-webkit-scrollbar-thumb {
    background-color: #C1C1C1;
    border-radius: 5px;
  }
}
</style>
