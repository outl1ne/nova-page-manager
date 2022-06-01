<template>
  <div id="page-manager-field" class="relative py-3">
    <PageManagerFieldHeader :locales="field.locales" :activeLocale="locale" @changeLocale="(l) => (locale = l)" />

    <div>body</div>
  </div>
</template>

<script>
import { FormField } from 'laravel-nova';
import PageManagerFieldHeader from './PageManagerFieldHeader';

export default {
  mixins: [FormField],
  components: { PageManagerFieldHeader },
  props: ['resourceName', 'resourceId', 'field'],

  data: () => ({
    locale: void 0,
  }),

  beforeMount() {
    this.locale = Object.keys(this.field.locales)[0];
  },

  mounted() {
    // Fix classes on Detail view
    this.$parent.$el.classList.remove('py-3', 'px-6');
  },
};
</script>
