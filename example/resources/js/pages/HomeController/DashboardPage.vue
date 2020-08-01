<template>
<div>
  <h1 class="text-center text-4xl mb-4">Welcome {{ $store.state.user.profile.name }}</h1>

  <div class="flex items-center justify-center">
    <button class="px-4 py-2 hover:bg-gray-400" data-cy="counter-dec" @click="decrement">Decrement</button>
    <div class="text-4xl mx-8" data-cy="counter-count">{{ $store.state.user.counter }}</div>
    <button class="px-4 py-2 hover:bg-gray-400" data-cy="counter-inc" @click="increment">Increment</button>
  </div>
</div>
</template>

<script>
export default {
  methods: {
    increment() { axios.post(`/counter/increment/${this.$store.state.user.counter}`) },
    decrement() { axios.post(`/counter/decrement/${this.$store.state.user.counter}`) }
  }
}
</script>
