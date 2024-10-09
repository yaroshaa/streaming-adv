<!-- eslint-disable -->
<template>
    <div class="login" style="display: none;">
        <el-card>
            <h2>Login</h2>
            <el-form
                class="login-form"
                :model="model"
                :rules="rules"
                ref="form"
                @submit.native.prevent="login"
            >
                <el-form-item prop="username">
                    <el-input v-model="model.email" placeholder="Username" prefix-icon="fas fa-user"></el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input
                        v-model="model.password"
                        placeholder="Password"
                        type="password"
                        prefix-icon="fas fa-lock"
                    ></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button
                        :loading="loading"
                        class="login-button"
                        type="primary"
                        native-type="submit"
                        block
                    >Login
                    </el-button>
                </el-form-item>
                <el-form-item>
                    <el-button
                        class="gauth-button"
                        type="default"
                        @click="oauth2"
                    >GAuth
                    </el-button>
                </el-form-item>
                <el-form-item>
                    <el-button
                        class="switch-reg-button"
                        type="default"
                        @click="$router.push({name: 'register'})"
                    >Register new account
                    </el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<!-- eslint-disable -->
<script>
export default {
    name: "login",
    data() {
        return {
            model: {
                email: "",
                password: ""
            },
            loading: false,
            rules: {
                email: [
                    {
                        required: true,
                        message: "Email is required",
                        trigger: "blur"
                    },
                    {
                        min: 4,
                        message: "Username length should be at least 5 characters",
                        trigger: "blur"
                    }
                ],
                password: [
                    {required: true, message: "Password is required", trigger: "blur"},
                    {
                        min: 5,
                        message: "Password length should be at least 5 characters",
                        trigger: "blur"
                    }
                ]
            }
        };
    },
    methods: {
        async login() {
            let valid = await this.$refs.form.validate();
            if (!valid) {
                return;
            }
            this.loading = true;
            let options = {
                data: this.model,
                redirect: {name: 'orders'},
                staySignedIn: true,
                remember: true,
            };
            await this.$auth.login(options).then(() => {
                this.$auth.user() && !this.$auth.user().verified && this.$router.push('/access-denied');
            });
            this.loading = false;
        },
        async oauth2() {
            const authCode = await this.$gAuth.getAuthCode();
            const user = this.$gAuth.GoogleAuth.currentUser.get()

            await this.$auth.oauth2('google', {
                code: true,
                data: {
                    code: authCode,
                },
                redirect: {name: 'orders'},
                staySignedIn: true,
                remember: true,
            }).then(() => {
                this.$auth.user() && !this.$auth.user().verified && this.$router.push('/access-denied');
            })
        }
    },
    mounted() { // remove this to enable JS auth
        let options = {
            redirect: {name: 'orders'},
            staySignedIn: true,
            remember: true,
        };
        this.$auth.login(options)
            .then(() => {
                this.$auth.user() && !this.$auth.user().verified && this.$router.push('/access-denied');
            })
            .catch(() => {
                document.location.href = '/gauth'
            })
    }
};
</script>
