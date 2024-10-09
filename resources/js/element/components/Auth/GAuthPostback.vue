<!-- eslint-disable -->
<script>
export default {
    name: 'g-auth-postback',
    mounted() {
        this.$auth.oauth2('google', {
            code: true,
            data: {
                code: this.$route.query.code,
            },
            redirect: {name: 'product-statistic'},
            staySignedIn: true,
            remember: true,
        })
            .then(() => {
                if(!this.$auth.user())  {
                    this.$router.push('/access-denied')
                }
            })
            .catch(() => {
                document.location.href = '/gauth'
            })
    }
}
</script>
