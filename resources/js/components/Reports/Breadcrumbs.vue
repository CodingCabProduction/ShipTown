<template>
  <div class="text-left text-secondary font-weight-bold text-uppercase small flex-fill">
      {{ breadcrumbs }}
  </div>
</template>
<script>
export default {
    name: 'breadcrumbs',

    data() {
        return {
            breadcrumbs: '',
        }
    },

    mounted() {
        this.breadcrumbs = this.$router.currentRoute.path
          .replace('/', '')
          .replaceAll('/', ' > ')
          .replaceAll('-', ' ');
    }
}
</script>
