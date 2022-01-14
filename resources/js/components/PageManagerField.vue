<template>
  <div id="page-manager-field" class="relative py-3">
    <div>header</div>

    <div>body</div>
  </div>
</template>

<script>
import { FormField } from 'laravel-nova';

export default {
  mixins: [FormField],

  props: ['resourceName', 'resourceId', 'field'],

  mounted() {
    // Fix classes on Detail view
    this.$parent.$el.classList.remove('py-3', 'px-6');
  },
};
</script>
