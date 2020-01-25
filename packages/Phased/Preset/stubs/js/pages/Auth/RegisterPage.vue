
<template>
    <div class="w-full h-full flex items-center justify-center bg-gray-200">
        <div class="w-full max-w-xs">
            <LoginForm @submit="register">
                <LoginInput
                    id="name"
                    placeholder="Name"
                    label="Name"
                    v-model="user.name"
                    :error="getError('name')"
                />

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

                <LoginInput
                    id="password_confirmation"
                    type="password"
                    placeholder="******************"
                    label="Password Confirmation"
                    v-model="user.password_confirmation"
                    :error="getError('password_confirmation')"
                />

                <div class="flex items-center justify-between">
                    <LoginButton>Register</LoginButton>
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
            name: "",
            email: "",
            password: "",
            password_confirmation: ""
        }
    }),

    computed: {
        getError() {
            return selector => {
                const error = this.errors[selector] && this.errors[selector].find(a => a)
                return error || ''
            }
        }
    },

    methods: {
        async register() {
            try {
                this.errors = {};
                await axios.post("/register", this.user)
                this.$router.push({ name: "PhaseController@HomePage" });
            } catch (e) {
                this.errors = e.response.data.errors;
            }
        }
    }
};
</script>
