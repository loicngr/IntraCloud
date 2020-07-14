<template>
    <div></div>
</template>

<script>
    export default {
        name: 'Logout',
        methods: {
            logout() {
                this.$store.dispatch('logout');
                this.$router.push({ name: 'Login' });
            }
        },
        mounted() {
            this.logout();
        }
    };
</script>

<style lang="scss" scoped></style>
