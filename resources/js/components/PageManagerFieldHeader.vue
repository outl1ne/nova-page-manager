<template>
  <div id="page-manager-field-header">
    <div
      class="npm-px-4 npm-py-2 npm-mr-4 npm-bg-white dark:npm-bg-gray-800 npm-rounded-lg npm-flex npm-align-center npm-justify-center"
      v-if="Object.keys(locales).length > 1"
    >
      <div
        v-for="locale of Object.keys(locales)"
        :key="locale"
        @click.prevent="$emit('changeLocale', locale)"
        class="npm-cursor-pointer npm-font-bold npm-px-2 npm-h-full npm-flex npm-items-center npm-box-border npm-border-b-2"
        :class="{
          'text-primary-500 border-primary-500': activeLocale === locale,
          'npm-text-80 npm-border-transparent': activeLocale !== locale,
        }"
      >
        <span>{{ locales[locale] }}</span>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  props: ['locales', 'activeLocale'],

  updated() {
    console.info(this.activeLocale, this.locales);
  },
};
</script>

<style lang="scss">
#page-manager-field-header {
  position: absolute;
  right: 0;
  top: -56px;
  display: flex;
}
</style>
