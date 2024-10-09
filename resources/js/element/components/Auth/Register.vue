<!-- eslint-disable -->
<template>
    <div class="login">
        <el-card>
            <h2>Registration</h2>
            <el-form
                class="login-form"
                :model="model"
                :rules="rules"
                ref="form"
                @submit.native.prevent="register"
            >
                <el-form-item prop="name">
                    <el-input v-model="model.name" placeholder="Username" prefix-icon="fas fa-user" autocomplete="chrome-off"></el-input>
                </el-form-item>
                <el-form-item prop="email">
                    <el-input v-model="model.email" placeholder="Email" prefix-icon="fas fa-user" autocomplete="chrome-off"></el-input>
                </el-form-item>
                <el-form-item prop="password">
                    <el-input
                        v-model="model.password"
                        placeholder="Password"
                        type="password"
                        prefix-icon="fas fa-lock"
                        autocomplete="new-password"
                    ></el-input>
                </el-form-item>
                <el-form-item prop="confirmPassword">
                    <el-input
                        v-model="model.confirmPassword"
                        placeholder="Confirm Password"
                        type="password"
                        prefix-icon="fas fa-lock"
                        autocomplete="new-password"
                    ></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button
                        :loading="loading"
                        class="login-button"
                        type="primary"
                        native-type="submit"
                        block
                    >Register
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
                        native-type="submit"
                        block
                        @click="$router.push({name: 'login'})"
                    >I've already have an account
                    </el-button>
                </el-form-item>
            </el-form>
        </el-card>
    </div>
</template>

<!-- eslint-disable -->
<script>
export default {
    name: "register",
    data() {
        return {
            model: {
                email: "",
                password: "",
                confirmPassword: "",
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
                    {
                        required: true,
                        message: "Password is required",
                        trigger: "blur",
                        validator: this.validatePassword
                    },
                    {
                        min: 5,
                        message: "Password length should be at least 5 characters",
                        trigger: "blur"
                    }
                ],
                passwordConfirm: [
                    {
                        required: true,
                        message: "Password is required",
                        trigger: "blur",
                        validator: this.validatePasswordConfirmation
                    },
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
        validatePassword(rule, value, callback) {
            if (value === '') {
                callback(new Error('Please input the password'));
            } else {
                if (this.model.confirmPassword !== '') {
                    this.$refs.form.validateField('confirmPassword');
                }
                callback();
            }
        },
        validatePasswordConfirmation(rule, value, callback) {
            if (value === '') {
                callback(new Error('Please input the password again'));
            } else if (value !== this.model.password) {
                callback(new Error('Two inputs don\'t match!'));
            } else {
                callback();
            }
        },
        async register() {
            let valid = await this.$refs.form.validate();
            if (!valid) {
                return;
            }
            this.loading = true;
            let options = {
                data: this.model,
                redirect: {name: 'orders'},
                staySignedIn: true,
                remember: true
            };
            await this.$auth.register(options).then(() => {
                this.$auth.user() && !this.$auth.user().verified && this.$router.push('/access-denied');
            });
            this.loading = false;
        },
        async oauth2() {
            const authCode = await this.$gAuth.getAuthCode();
            const user = this.$gAuth.GoogleAuth.currentUser.get()

            await this.$auth.oauth2('google', {
                code: true,
                url: 'auth/social/register',
                data: {
                    code: authCode,
                },
                redirect: {name: 'orders'},
                staySignedIn: true,
                remember: true,
            }).then(() => {
                this.$auth.user() && !this.$auth.user().verified && this.$router.push('/access-denied');
            });
        }
    }
};
</script>
