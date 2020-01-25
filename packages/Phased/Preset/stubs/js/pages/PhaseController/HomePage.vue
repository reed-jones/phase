<template>
<div class="flex flex-wrap flex-col items-center justify-center flex-grow">
    <h1 class="mb-12 text-6xl font-thin text-center w-full">{{ $store.state.app.name }}</h1>
    <ul class="my-12 flex justify-around max-w-4xl mx-auto w-full">
        <li><a href="https://vuexcellent.netlify.com/">Vuexcellent</a></li>
        <li><a href="#">Phase</a></li>
        <li><a href="https://vuejs.org/">Vue</a></li>
        <li><a href="https://vuex.vuejs.org/">Vuex</a></li>
        <li><a href="https://laravel.com/">Laravel</a></li>
        <li><a href="https://router.vuejs.org/">Vue Router</a></li>
    </ul>
</div>
</template>

<script>
export default {
    //
}
</script>
