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
