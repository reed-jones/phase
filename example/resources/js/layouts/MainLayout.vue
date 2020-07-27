<template>
<main class="bg-gray-300 min-h-screen">
    <nav>
        <RouterLink :to="{ name: 'Auth/LoginController@LoginPage'}" data-cy="navbar-login">Login</RouterLink>
    </nav>
    <slot />
</main>
</template>
