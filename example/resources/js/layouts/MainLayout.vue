<template>
<main class="bg-gray-300 min-h-screen">
    <nav class="flex justify-end" >
        <RouterLink
            class="px-8 py-4 hover:bg-gray-100"
            :to="{ name: 'Auth/LoginController@LoginPage'}"
            v-if="!$store.state.user.profile"
            data-cy="navbar-login">
            Login
        </RouterLink>

        <RouterLink
            class="px-8 py-4 hover:bg-gray-100"
            :to="{ name: 'Auth/LoginController@LoginPage'}"
            v-if="$store.state.user.profile"
            data-cy="navbar-logout">
            Logout
        </RouterLink>
    </nav>

    <slot />
</main>
</template>
