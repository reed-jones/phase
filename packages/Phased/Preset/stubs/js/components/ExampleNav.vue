<template>
  <div class="w-full flex border-b shadow-md">
    <RouterLink
      v-for="route in routesWithoutParameters"
      class="p-4 hover:bg-gray-200"
      :key="route.path"
      :to="{ name: route.name }">
      {{ route.name }}
    </RouterLink>
  </div>
</template>

<script>
export default {
    computed: {
        routesWithoutParameters() {
            return this.$router.options.routes.filter(r => !r.path.includes(':'))
        }
    }
}
</script>
