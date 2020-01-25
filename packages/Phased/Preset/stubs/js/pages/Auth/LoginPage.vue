
<template>
    <div class="w-full h-full flex items-center justify-center bg-gray-200">
        <div class="w-full max-w-xs">
            <LoginForm @submit="login">
                <LoginInput
                    id="email"
                    type="email"
                    placeholder="Email"
                    label="Email"
                    v-model="user.email"
                    :error="getError('email')"
                />

                <LoginInput
                    id="password"
                    type="password"
                    placeholder="******************"
                    label="Password"
                    v-model="user.password"
                    :error="getError('password')"
                />

                <div class="flex items-center justify-between">
                    <LoginButton>Sign In</LoginButton>
                    <LoginLink href="#">Forgot Password?</LoginLink>
                </div>
            </LoginForm>

            <LoginCopyright />
        </div>
    </div>
</template>

<script>
import * as LoginComponents from "@/components/Auth";

export default {
    components: {
        ...LoginComponents
    },

    data: _ => ({
        errors: {},
        user: {
            email: "",
            password: ""
        }
    }),

    computed: {
        getError() {
            return selector => {
                const error = this.errors[selector] && this.errors[selector].find(a => a);
                return error || "";
            };
        }
    },

    methods: {
        async login() {
            try {
                this.errors = {};
                await axios.post("/login", this.user);
                this.$router.push({ name: "PhaseController@HomePage" });
            } catch (e) {
                this.errors = e.response.data.errors;
            }
        }
    }
};
</script>
