<template>
    <div class="mb-6">
        <label class="block text-gray-700 text-sm font-bold mb-2" :for="id">{{ label }}</label>
        <input
            class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"
            :class="{'border-red-500 mb-3': error }"
            :id="id"
            :value="value"
            @input="$emit('input', $event.target.value)"
            :type="type"
            :placeholder="placeholder"
        />
        <p class="text-red-500 text-xs italic" v-if="error">{{ error }}</p>
    </div>
</template>

<script>
export default {
    props: {
        id: {
            type: String,
            required: true
        },
        type: {
            type: String,
            default: "text"
        },
        value: {
            type: String,
            required: true
        },
        placeholder: {
            type: String,
            default: ""
        },
        error: {
            type: String,
            default: ""
        },
        label: {
            type: String,
            default: ""
        }
    }
};
</script>

<style>
</style>
