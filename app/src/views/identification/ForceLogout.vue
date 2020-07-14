<template>
    <div></div>
</template>

<script>
    import { EventBus } from '../../components/EventBus.js';

    export default {
        name: 'ForceLogout',
        methods: {
            logout() {
                localStorage.removeItem('user_token');
                localStorage.removeItem('user_refresh_token');
                window.location = '';
                setTimeout(() => {
                    EventBus.$emit('loaderRequest', false);
                }, 500);
            },
        },
        mounted() {
            this.logout();
        },
    };
</script>
