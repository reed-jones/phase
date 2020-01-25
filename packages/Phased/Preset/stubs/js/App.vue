<template>
    <div class="min-h-screen flex-col flex">
        <ExampleNav />

        <transition name="fade" mode="out-in">
            <RouterView class="p-4 flex-grow"/>
        </transition>
    </div>
</template>

<script>
import ExampleNav from '@/components/ExampleNav'

export default {
    components: {
        ExampleNav
    }
}
</script>
