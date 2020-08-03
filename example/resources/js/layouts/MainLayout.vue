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
            :to="{ name: 'Auth/LoginController@LoginPage'}"
            v-if="!$store.state.user.profile"
            data-cy="navbar-login">
            Login
        </RouterLink>

        <template v-if="$store.state.user.profile">
            <a class="px-8 py-4 hover:bg-gray-100" href="/logout"
                data-cy="navbar-logout"
                @click.prevent="logout">
                Logout
            </a>
        </template>
    </nav>

    <slot />
</main>
</template>

<script>
export default {
    methods: {
        async logout() {
            await axios.post('/logout')
            this.$router.push('/')
        }
    }
}
</script>
