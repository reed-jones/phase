<template>
    <a
        class="inline-block align-baseline font-bold text-sm text-blue-500 hover:text-blue-800"
        v-bind="$props"
        v-on="$listeners"
    >
        <slot />
    </a>
</template>

<script>
export default {};
</script>

<style>
</style>
