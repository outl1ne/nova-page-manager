<template>
  <div>
    <span v-if="published" class="badge badge-success">PUBLISHED</span>
    <span v-if="draft" class="badge badge-primary">DRAFT</span>
  </div>
</template>

<script>
export default {
  props: ['published', 'draft'],
};
</script>

<style scoped>
.badge {
  display: inline-block;
  padding: 0.25em 0.4em;
  font-size: 75%;
  font-weight: 700;
  line-height: 1;
  text-align: center;
  white-space: nowrap;
  vertical-align: baseline;
  border-radius: 0.25rem;
  position: relative;
  bottom: 1px;
}

.badge.badge-primary {
  background-color: var(--primary);
  color: var(--white);
}

.badge.badge-success {
  background-color: var(--success);
  color: var(--white);
}
</style>
