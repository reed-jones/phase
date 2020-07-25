<template>
<div class="min-h-screen min-w-screen flex items-center justify-center bg-gray-300">

  <div class="border rounded min-h-screen sm:min-h-0 sm:max-w-md w-full py-8 px-4 sm:px-8 bg-white shadow-md">
    <div class="text-center my-8">
      <h1 class="text-2xl font-bold">Welcome Back</h1>
      <div class="text-sm">Have an invite code?
        <span class="border-b-2 border-transparent focus-within:border-blue-200">
          <a href="#" class="font-medium text-blue-600 hover:text-blue-400 focus:outline-none">
          Register here
          </a>
        </span>
      </div>
    </div>

{{ isServer ? 'YUP' : 'NOPE' }}
{{ isServer }}
    <form class="flex flex-col" action="#" method="POST" @submit.prevent="login">

      <input type="hidden" name="_token" value="oGalrKS7FNwySJJpxAj7n0cZuxN2hQOKYgFFfnBM">

      <section class="w-full flex flex-col my-4">
        <label for="email" class="my-2">Email</label>
        <input
          id="email"
          name="email"
          type="email"
          placeholder="you@example.com"
          autocomplete="email"
          v-model="form.email"
          required
          class="w-full appearance-none rounded p-2 shadow border-2 border-transparent placeholder-gray-500 focus:outline-none focus:border-blue-200"
          autofocus>
      </section>

      <section class="w-full flex flex-col my-4">
        <label for="current-password" class="my-2">Password</label>
        <span class="w-full relative flex">
          <input
            id="current-password"
            name="current-password"
            :type="passwordVisible ? 'text' : 'password'"
            :placeholder="passwordVisible ? 'Sup3rT0pS3cr3t123' : '*****************'"
            autocomplete="current-password"
            v-model="form.password"
            aria-describedby="password-constraints"
            class="w-full appearance-none rounded p-2 shadow border-2 border-transparent placeholder-gray-500 focus:outline-none focus:border-blue-200"
            required >

          <button
            class="border-2 border-transparent w;-8 h-8 absolute right-0 m-2 flex items-center justify-center group focus:outline-none focus:border-blue-200"
            id="toggle-password"
            type="button"
            :aria-label="passwordVisible ? 'Hide Password' : 'Show password as plain text. Warning: this will display your password on the screen.'"
            @click="passwordVisible = !passwordVisible">
            <IconEyeOff class="h-6 w-6 opacity-25 group-hover:opacity-50" v-if="passwordVisible" />
            <IconEye class="h-6 w-6 opacity-25 group-hover:opacity-50" v-else />
          </button>
        </span>

        <div
          class="w-full text-center text-gray-500 text-xs sr-only"
          id="password-constraints">
          Eight or more characters, with at least one lowercase and one uppercase letter.
        </div>
      </section>

      <section class="w-full flex flex-row justify-between my-4">
        <div class="flex items-center border-b-2 border-transparent focus-within:border-blue-200">
          <input id="remember" name="remember" type="checkbox" class="h-4 w-4" v-model="form.remember">
          <label for="remember" class="ml-2 text-sm leading-5">Remember me</label>
        </div>

        <div class="text-sm border-b-2 border-transparent focus-within:border-blue-200">
          <a href="#" class="font-medium text-blue-600 hover:text-blue-400 focus:outline-none">
            Forgot your password?
          </a>
        </div>
      </section>

      <button
        id="signin"
        class="border-2 border-transparent bg-gray-300 hover:bg-gray-400 p-2 mt-8 focus:outline-none focus:border-blue-200">
        Sign in
      </button>

      <ul v-if="errors" class="text-sm py-2 font-medium text-center" :class="errors ? 'text-red-700' : 'text-transparent'">
        <li v-for="([key, errs]) in Object.entries(errors)" :key="key">
          <p v-for="error in errs" :key="error">{{ error }}</p>
        </li>
      </ul>
    </form>
  </div>

</div>
</template>

<script>
export default {
  data: () => ({
    passwordVisible: false,
    errors: null,
    form: {
      email: '',
      password: '',
      remember: false
    }
  }),

  created() {
      axios.get('sanctum/csrf-cookie')
  },

  computed: {
    isServer() {
      return process.env.VUE_ENV
    }
  },

  methods: {
    async login() {
      try {
        await axios.post('/login', this.form)
        this.$router.push({ name: 'HomeController@DashboardPage'});
      } catch(err) {
        this.errors  = err.response.data.errors
      }
    }
  }
}
</script>
