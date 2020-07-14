<template>
    <div id="page-container">
        <div class="page-item">
            <h4 v-if="gridData.length >= 1">
                Rechercher
                <span class="no-bg span-btn">
                    <form id="search" style="width: 100%; height: 100%;">
                        <input
                            name="query"
                            v-model="searchQuery"
                            class="rounded-perso-1"
                            placeholder=" Rechercher un utilisateur"
                            style="width: 100%; border: 0; height: 100%;"
                        />
                    </form>
                </span>
            </h4>
        </div>
        <div class="page-item">
            <UsersTable
                :elements="gridData"
                :columns="gridColumns"
                :filter-key="searchQuery"
                :isLoading="usersLoading"
            />
        </div>
    </div>
</template>

<script>
    import UsersTable from '@/components/Admin/UsersTable';

    import { User } from '@/api/User.js';
    import { EventBus } from '@/components/EventBus.js';

    const userAPI = new User();
    export default {
        name: 'Admin-Users',
        components: {
            UsersTable,
        },
        data() {
            return {
                searchQuery: '',
                gridColumns: ['icone', 'nom', 'email', 'role', 'actions'],
                gridData: [],
                usersLoading: true,
            };
        },
        methods: {
            /*** @param {Array} users */
            createGridData(users) {
                this.gridData = [];

                users.forEach((user) => {
                    this.gridData.push({
                        icone: '-_user_-',
                        nom: `${user.firstname} ${user.lastname}`,
                        email: user.email,
                        role: user.roles.join(' - '),
                        actions: '-_actions_-',
                    });
                });
            },
            getUsers() {
                userAPI
                    .getAll(this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null) {
                            EventBus.$emit('expiredJWT', result.tokens);
                        }
                        if (result.status) {
                            this.createGridData(result.data);
                            this.usersLoading = false;
                        }
                    })
                    .catch((err) => console.error(err));
            },
            initEventsListeners() {
                EventBus.$on('tableUserUpdated', () => {
                    this.usersLoading = true;
                    this.getUsers();
                });
            },
        },
        mounted() {
            this.getUsers();
            this.initEventsListeners();
        },
    };
</script>

<style></style>
