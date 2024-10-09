<template>
    <div id="app">
        <router-view></router-view>
    </div>
</template>

<script>
export default {
    name: 'App',

    mounted() {
        this.$auth.load().then(() => {
            if (
                this.$auth.user() &&
                !this.$auth.user().verified &&
                this.$route.path !== '/access-denied'
            ) {
                this.$router.push('/access-denied');
            }
        });
    },
};
</script>

<style lang="scss">
body {
    #app {
        -webkit-font-smoothing: antialiased;
        -moz-osx-font-smoothing: grayscale;
        color: #2c3e50;
        background-color: $background;
    }
    :focus {
        outline: none;
    }

    .el-picker-panel,
    .el-picker-panel__sidebar {
        background-color: $block-color;
    }

    .el-popover {
        background-color: $block-color !important;
    }
}
</style>
