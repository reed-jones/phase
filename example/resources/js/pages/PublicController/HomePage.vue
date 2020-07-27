<template>
<div class="prose mx-auto py-8">
  <h1 class="text-center py-8">Notices</h1>
  <article class="flex flex-wrap items-center justify-center">
    <section
      v-for="notice in $store.state.notices.all.data"
      :key="notice.all"
       class="shadow-2xl mx-4 my-12 p-4 bg-gray-100 w-full">
      <div class="text-xl text-gray-900">{{ notice.title }}</div>
      <div class="text-gray-500 flex justify-between">
        <div class="flex flex-col">
          <div>{{ notice.user.name }}</div>
          <a class="" v-if="notice.user.email" data-cy="contact-email" :href="`mailto:${notice.user.email}`">{{ notice.user.email }}</a>
          <a class="" v-if="notice.user.phone" data-cy="contact-phone" :href="`tel:${notice.user.phone}`">{{ notice.user.phone }}</a>
        </div>

        <div class="text-sm">{{ dayjs(notice.created_at).format('YYYY-MM-D') }}</div>
      </div>
      <div class="text-gray-800">{{ notice.content }}</div>
    </section>
  </article>

  <div class="flex justify-center no-prose">
    <RouterLink
      :to="{ name: 'PublicController@HomePage' }"
      class="border-b border-transparent px-4 py-2 hover:bg-gray-200 transition duration-150 w-24 flex items-center justify-center"
      v-slot="{ href, navigate }">
      <a :href="href" data-cy="nav-first" @click="navigate">First</a>
    </RouterLink>

    <RouterLink
      :to="{ name: 'PublicController@HomePage', ...($store.state.notices.all.current_page > 2) && { query: { page: $store.state.notices.all.current_page - 1 } } }"
      class="border-b border-transparent px-4 py-2 hover:bg-gray-200 transition duration-150 w-24 flex items-center justify-center"
      v-slot="{ href, navigate }">
      <a :href="href" data-cy="nav-previous" @click="navigate">Previous</a>
    </RouterLink>

    <div data-cy="nav-page" class="m-4 border-b border-gray-500 w-24 text-center">Page: {{ $store.state.notices.all.current_page }}</div>

    <RouterLink
      :to="{ name: 'PublicController@HomePage', query: { page: Math.min($store.state.notices.all.current_page + 1, $store.state.notices.all.last_page) } }"
      class="border-b border-transparent px-4 py-2 hover:bg-gray-200 transition duration-150 w-24 flex items-center justify-center"
      v-slot="{ href, navigate }">
      <a :href="href" data-cy="nav-next" @click="navigate">Next</a>
    </RouterLink>

    <RouterLink
      :to="{ name: 'PublicController@HomePage', query: { page: $store.state.notices.all.last_page } }"
      class="border-b border-transparent px-4 py-2 hover:bg-gray-200 transition duration-150 w-24 flex items-center justify-center text-decoration-none"
      v-slot="{ href, navigate }">
      <a :href="href" data-cy="nav-last" @click="navigate">Last</a>
    </RouterLink>
  </div>
</div>
</template>

<script>
export default {
  async beforeRouteUpdate(to, from, next) {
    await axios.get(to.fullPath)
    next()
  }
}
</script>
