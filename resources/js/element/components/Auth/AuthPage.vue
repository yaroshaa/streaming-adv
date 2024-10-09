<!-- eslint-disable -->
<template>
    <div id="main">
        <div class="header"></div>

        <router-view></router-view>

        <div class="footer"></div>
    </div>
</template>

<!-- eslint-disable -->
<script>
export default {
    name: "AuthPage",
};
</script>

<style>

.login {
    flex: 1;
    display: flex;
    justify-content: center;
    align-items: center;
}

.login-form .el-form-item .login-button,
.login-form .el-form-item .switch-reg-button {
    margin-top: 40px;
}

.login-form .el-form-item .login-button,
.login-form .el-form-item .switch-reg-button,
.login-form .el-form-item .gauth-button {
    width: 100%;
}

.login-form {
    width: 290px;
}

.forgot-password {
    margin-top: 10px;
}
</style>

<style lang="scss">
#main {
    font-family: Roboto, sans-serif;
    -webkit-font-smoothing: antialiased;
    -moz-osx-font-smoothing: grayscale;
    text-align: center;
    color: #2c3e50;
    margin: 0;
    display: flex;
    flex-direction: column;
    min-height: 100vh;
}

body {
    margin: 0;
    padding: 0;
    background: #102a43;
    background-image: url("https://uploads.codesandbox.io/uploads/user/c3fb8e8a-35ea-4232-b5d6-0f3c5373510b/LVs7-dots.png");
    background-size: contain;
}

.footer,
.header {
    padding: 20px 20px;
    color: #f0f4f8;
    display: flex;
    flex-direction: column;
    align-items: center;

    h1,
    h2,
    h3 {
        color: #f0f4f8;
        padding: 0;
        margin: 0;
    }

    .links {
        display: flex;
        font-family: "Open Sans";

        span {
            padding: 0 10px;
            font-size: 18px;
            border-right: 1px solid #9fb3c8;

            &:last-child {
                border-right: none;
            }
        }
    }

    .version {
        font-family: "Open Sans";
        padding: 0 10px;
        color: #9fb3c8;
        font-size: 12px;
        margin-top: 5px;
    }
}

.header {
    padding: 10px 20px;

    .logo {
        font-family: "Open Sans";
        letter-spacing: 3px;
        padding-top: 15px;
        padding-bottom: 15px;
    }

    .logo .part-2 {
        color: #d64545;
    }
}

$teal: rgb(0, 124, 137);

.login .el-input__inner:hover {
    border-color: $teal;
}

.login .el-input__prefix {
    background: rgb(238, 237, 234);
    left: 0;
    height: calc(100% - 2px);
    left: 1px;
    top: 1px;
    border-radius: 3px;

    .el-input__icon {
        width: 30px;
    }
}

.login .el-input input {
    padding-left: 35px;
}

.login .el-card {
    padding-top: 0;
    padding-bottom: 30px;
}

h2 {
    font-family: "Open Sans";
    letter-spacing: 1px;
    font-family: Roboto, sans-serif;
    padding-bottom: 20px;
}

a {
    color: $teal;
    text-decoration: none;

    &:hover,
    &:active,
    &:focus {
        color: lighten($teal, 7);
    }
}

.login .el-card {
    width: 340px;
    display: flex;
    justify-content: center;
}
</style>
