<template>
<div class="min-h-screen min-w-screen flex items-center justify-center bg-gray-300">
  <div class="border rounded max-w-md w-full p-8 bg-white">
    <div class="text-2xl font-bold text-center my-8">Welcome Back</div>
    <form class="flex flex-col" action="#" method="POST" @submit.prevent>
      <section class="w-full flex flex-col my-4">
        <label for="email" class="my-2">Email</label>
        <input
          id="email"
          name="email"
          type="email"
          placeholder="you@example.com"
          autocomplete="email"
          required
          class="border rounded p-2"
          autofocus>
      </section>

      <section class="w-full flex flex-col my-4">
        <label for="current-password" class="my-2">Password</label>
        <span class="w-full relative">
        <input
          id="current-password"
          name="current-password"
          :type="passwordVisible ? 'text' : 'password'"
          placeholder="*****************"
          autocomplete="current-password"
          aria-describedby="password-constraints"
          class="w-full border rounded p-2"
          required>

        <button class="absolute right-0 top-0 mr-2 bottom-0 flex items-center justify-center opacity-25 hover:opacity-50" id="toggle-password" type="button" :aria-label="passwordVisible ? 'Hide Password' : 'Show password as plain text. Warning: this will display your password on the screen.'" @click="passwordVisible = !passwordVisible">
            <svg v-if="passwordVisible" class="h-6 w-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M3.707 2.293a1 1 0 00-1.414 1.414l14 14a1 1 0 001.414-1.414l-1.473-1.473A10.014 10.014 0 0019.542 10C18.268 5.943 14.478 3 10 3a9.958 9.958 0 00-4.512 1.074l-1.78-1.781zm4.261 4.26l1.514 1.515a2.003 2.003 0 012.45 2.45l1.514 1.514a4 4 0 00-5.478-5.478z" clip-rule="evenodd"></path><path d="M12.454 16.697L9.75 13.992a4 4 0 01-3.742-3.741L2.335 6.578A9.98 9.98 0 00.458 10c1.274 4.057 5.065 7 9.542 7 .847 0 1.669-.105 2.454-.303z"></path></svg>
            <svg v-else class="h-6 w-6"  fill="currentColor" viewBox="0 0 20 20"><path d="M10 12a2 2 0 100-4 2 2 0 000 4z"></path><path fill-rule="evenodd" d="M.458 10C1.732 5.943 5.522 3 10 3s8.268 2.943 9.542 7c-1.274 4.057-5.064 7-9.542 7S1.732 14.057.458 10zM14 10a4 4 0 11-8 0 4 4 0 018 0z" clip-rule="evenodd"></path></svg>
          </button>
        </span>

        <div class="w-full text-center text-gray-500 text-xs" id="password-constraints">Eight or more characters, with at least one&nbsp;lowercase and one uppercase letter.</div>
      </section>

      <button id="signin" class="bg-gray-300 hover:bg-gray-400 p-2 mt-8">Sign in</button>
    </form>
  </div>
</div>
</template>

<script>
export default {
  data: () => ({
    passwordVisible: false
  })
}
</script>
