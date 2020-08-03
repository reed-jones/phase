<template>
<main class="bg-gray-300 min-h-screen">
    <nav class="flex justify-end" >
        <RouterLink
            class="px-8 py-4 hover:bg-gray-100"
            :to="{ name: 'HomeController@DashboardPage' }"
            data-cy='navbar-dashboard'>
            Dashboard
        </RouterLink>

        <RouterLink
            class="px-8 py-4 hover:bg-gray-100"
            :to="{ name: 'PublicController@HomePage' }"
            data-cy='navbar-home'>
            Notices
        </RouterLink>
    </nav>
    <slot />
</main>
</template>
