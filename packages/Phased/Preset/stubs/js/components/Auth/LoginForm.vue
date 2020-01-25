<template>
    <form class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4" @submit.prevent="$emit('submit', $event)">
        <slot />
    </form>
</template>

<script>
export default {};
</script>

<style>
</style>
