<template>
<main class="bg-gray-300 min-h-screen">
    <nav>
        <RouterLink
            :to="{ name: 'PublicController@HomePage' }"
            data-cy='navbar-home'
            >Home</RouterLink>
    </nav>
    <slot />
</main>
</template>
