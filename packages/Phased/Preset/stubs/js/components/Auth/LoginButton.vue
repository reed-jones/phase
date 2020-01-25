<template>
    <button
        class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline"
        v-bind="$props"
        v-on="$listeners"
        type="submit"
    >
        <slot />
    </button>
</template>

<script>
export default {};
</script>

<style>
</style>
