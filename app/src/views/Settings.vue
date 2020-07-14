<template>
    <div id="page-container">
        <!--        Modifier le Mot de Passe    -->
        <div class="page-item">
            <main class="container">
                <form class="rounded-perso-2" @submit.prevent="checkForm" data-item="change_password">
                    <h1>Modifier le Mot de Passe</h1>
                    <div :class="['item', isValidField(item_password.form.current_password, getPassword)]">
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.3514 0C11.9892 0 10.7195 0.552037 9.67309 1.59766C8.48458 2.78616 7.94067 4.24744 8.09978 5.80937C8.21912 6.96865 8.74843 8.13767 9.59678 9.18329L3.71109 15.069L2.64517 14.0031C2.12561 13.4811 1.69453 13.9138 1.17416 14.4349L0.666771 14.9675C0.145584 15.4863 -0.28468 15.893 0.235695 16.4133L1.29999 17.4801L0.463816 18.3163C0.339985 18.4392 0.241707 18.5855 0.174642 18.7466C0.107578 18.9078 0.0730513 19.0806 0.0730513 19.2551C0.0730513 19.4297 0.107578 19.6025 0.174642 19.7636C0.241707 19.9247 0.339985 20.071 0.463816 20.194C0.98338 20.7127 1.82037 20.7127 2.34155 20.194L11.576 10.9595C12.5761 11.5863 13.6624 11.9232 14.721 11.9232C16.0824 11.9232 17.3545 11.3711 18.4001 10.3247C19.5895 9.13702 20.1326 7.67575 19.9726 6.11381C19.8298 4.70855 19.0837 3.29436 17.8927 2.10505C16.5435 0.754179 14.932 0 13.3514 0ZM13.4026 1.67397C14.5334 1.67397 15.7081 2.2528 16.7254 3.27244C17.6297 4.17356 18.1939 5.22243 18.2987 6.24045C18.4066 7.29906 18.034 8.30734 17.2076 9.13296C16.4672 9.87171 15.6042 10.2484 14.6706 10.2484C13.539 10.2484 12.3667 9.66876 11.3471 8.65074C10.4435 7.74718 9.87929 6.70075 9.77456 5.68273C9.6674 4.62412 10.0384 3.61665 10.8656 2.79022C11.606 2.05147 12.4698 1.67397 13.4026 1.67397Z"
                                fill="#9B9CAA"
                                fill-opacity="0.8"
                            />
                        </svg>
                        <input
                            type="password"
                            autocomplete="off"
                            placeholder="Mot de Passe"
                            v-model="item_password.form.current_password"
                            required
                        />
                    </div>
                    <div :class="['item', isValidField(item_password.form.new_password, getNewPassword)]">
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.3514 0C11.9892 0 10.7195 0.552037 9.67309 1.59766C8.48458 2.78616 7.94067 4.24744 8.09978 5.80937C8.21912 6.96865 8.74843 8.13767 9.59678 9.18329L3.71109 15.069L2.64517 14.0031C2.12561 13.4811 1.69453 13.9138 1.17416 14.4349L0.666771 14.9675C0.145584 15.4863 -0.28468 15.893 0.235695 16.4133L1.29999 17.4801L0.463816 18.3163C0.339985 18.4392 0.241707 18.5855 0.174642 18.7466C0.107578 18.9078 0.0730513 19.0806 0.0730513 19.2551C0.0730513 19.4297 0.107578 19.6025 0.174642 19.7636C0.241707 19.9247 0.339985 20.071 0.463816 20.194C0.98338 20.7127 1.82037 20.7127 2.34155 20.194L11.576 10.9595C12.5761 11.5863 13.6624 11.9232 14.721 11.9232C16.0824 11.9232 17.3545 11.3711 18.4001 10.3247C19.5895 9.13702 20.1326 7.67575 19.9726 6.11381C19.8298 4.70855 19.0837 3.29436 17.8927 2.10505C16.5435 0.754179 14.932 0 13.3514 0ZM13.4026 1.67397C14.5334 1.67397 15.7081 2.2528 16.7254 3.27244C17.6297 4.17356 18.1939 5.22243 18.2987 6.24045C18.4066 7.29906 18.034 8.30734 17.2076 9.13296C16.4672 9.87171 15.6042 10.2484 14.6706 10.2484C13.539 10.2484 12.3667 9.66876 11.3471 8.65074C10.4435 7.74718 9.87929 6.70075 9.77456 5.68273C9.6674 4.62412 10.0384 3.61665 10.8656 2.79022C11.606 2.05147 12.4698 1.67397 13.4026 1.67397Z"
                                fill="#9B9CAA"
                                fill-opacity="0.8"
                            />
                        </svg>
                        <input
                            type="password"
                            autocomplete="off"
                            placeholder="Nouveau Mot de Passe"
                            v-model="item_password.form.new_password"
                            required
                        />
                    </div>
                    <div :class="['item', isValidField(item_password.form.re_password, getRePassword)]">
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.3514 0C11.9892 0 10.7195 0.552037 9.67309 1.59766C8.48458 2.78616 7.94067 4.24744 8.09978 5.80937C8.21912 6.96865 8.74843 8.13767 9.59678 9.18329L3.71109 15.069L2.64517 14.0031C2.12561 13.4811 1.69453 13.9138 1.17416 14.4349L0.666771 14.9675C0.145584 15.4863 -0.28468 15.893 0.235695 16.4133L1.29999 17.4801L0.463816 18.3163C0.339985 18.4392 0.241707 18.5855 0.174642 18.7466C0.107578 18.9078 0.0730513 19.0806 0.0730513 19.2551C0.0730513 19.4297 0.107578 19.6025 0.174642 19.7636C0.241707 19.9247 0.339985 20.071 0.463816 20.194C0.98338 20.7127 1.82037 20.7127 2.34155 20.194L11.576 10.9595C12.5761 11.5863 13.6624 11.9232 14.721 11.9232C16.0824 11.9232 17.3545 11.3711 18.4001 10.3247C19.5895 9.13702 20.1326 7.67575 19.9726 6.11381C19.8298 4.70855 19.0837 3.29436 17.8927 2.10505C16.5435 0.754179 14.932 0 13.3514 0ZM13.4026 1.67397C14.5334 1.67397 15.7081 2.2528 16.7254 3.27244C17.6297 4.17356 18.1939 5.22243 18.2987 6.24045C18.4066 7.29906 18.034 8.30734 17.2076 9.13296C16.4672 9.87171 15.6042 10.2484 14.6706 10.2484C13.539 10.2484 12.3667 9.66876 11.3471 8.65074C10.4435 7.74718 9.87929 6.70075 9.77456 5.68273C9.6674 4.62412 10.0384 3.61665 10.8656 2.79022C11.606 2.05147 12.4698 1.67397 13.4026 1.67397Z"
                                fill="#9B9CAA"
                                fill-opacity="0.8"
                            />
                        </svg>
                        <input
                            type="password"
                            autocomplete="off"
                            placeholder="Confirmer le Mot de Passe"
                            v-model="item_password.form.re_password"
                            required
                        />
                    </div>

                    <!-- Bouton -->
                    <div class="item-btns">
                        <button type="submit" class="rounded-perso-1">
                            Valider
                        </button>
                    </div>
                </form>
            </main>
        </div>

        <!--        Modifier l'Email    -->
        <div class="page-item">
            <main class="container">
                <form class="rounded-perso-2" @submit.prevent="checkForm" data-item="change_email">
                    <h1>Modifier l'Email</h1>
                    <div :class="['item', isValidField(item_email.form.email, getEmail)]">
                        <svg width="20" height="21" viewBox="0 0 20 21" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M13.3514 0C11.9892 0 10.7195 0.552037 9.67309 1.59766C8.48458 2.78616 7.94067 4.24744 8.09978 5.80937C8.21912 6.96865 8.74843 8.13767 9.59678 9.18329L3.71109 15.069L2.64517 14.0031C2.12561 13.4811 1.69453 13.9138 1.17416 14.4349L0.666771 14.9675C0.145584 15.4863 -0.28468 15.893 0.235695 16.4133L1.29999 17.4801L0.463816 18.3163C0.339985 18.4392 0.241707 18.5855 0.174642 18.7466C0.107578 18.9078 0.0730513 19.0806 0.0730513 19.2551C0.0730513 19.4297 0.107578 19.6025 0.174642 19.7636C0.241707 19.9247 0.339985 20.071 0.463816 20.194C0.98338 20.7127 1.82037 20.7127 2.34155 20.194L11.576 10.9595C12.5761 11.5863 13.6624 11.9232 14.721 11.9232C16.0824 11.9232 17.3545 11.3711 18.4001 10.3247C19.5895 9.13702 20.1326 7.67575 19.9726 6.11381C19.8298 4.70855 19.0837 3.29436 17.8927 2.10505C16.5435 0.754179 14.932 0 13.3514 0ZM13.4026 1.67397C14.5334 1.67397 15.7081 2.2528 16.7254 3.27244C17.6297 4.17356 18.1939 5.22243 18.2987 6.24045C18.4066 7.29906 18.034 8.30734 17.2076 9.13296C16.4672 9.87171 15.6042 10.2484 14.6706 10.2484C13.539 10.2484 12.3667 9.66876 11.3471 8.65074C10.4435 7.74718 9.87929 6.70075 9.77456 5.68273C9.6674 4.62412 10.0384 3.61665 10.8656 2.79022C11.606 2.05147 12.4698 1.67397 13.4026 1.67397Z"
                                fill="#9B9CAA"
                                fill-opacity="0.8"
                            />
                        </svg>
                        <input
                            type="email"
                            autocomplete="off"
                            placeholder="Nouveau Email"
                            v-model="item_email.form.email"
                            required
                        />
                    </div>

                    <!-- Bouton -->
                    <div class="item-btns">
                        <button type="submit" class="rounded-perso-1">
                            Valider
                        </button>
                    </div>
                </form>
            </main>
        </div>

        <!--        Au démarrage                -->
        <div class="page-item">
            <main class="container">
                <form class="rounded-perso-2" @submit.prevent="checkForm" data-item="change_startup">
                    <h1>Au démarrage</h1>
                    <div class="item-list">
                        <label for="serversList">Serveur par défaut</label>
                        <select name="serversList" id="serversList" :disabled="item_startup.serversLoading">
                            <option value="default" :selected="parseInt(item_startup.form.selectedServer) === -1">
                                Désactivé
                            </option>
                            <option
                                v-for="server in item_startup.serversData"
                                :key="server.id"
                                :value="server.adresse + ':' + server.port"
                                :data-id="server.id"
                                :data-login="server.login"
                                :data-name="server.name"
                                :selected="parseInt(item_startup.form.selectedServer) === server.id"
                            >
                                {{ server.name }}
                            </option>
                        </select>
                    </div>

                    <!-- Bouton -->
                    <div class="item-btns">
                        <button type="submit" class="rounded-perso-1">
                            Valider
                        </button>
                    </div>
                </form>
            </main>
        </div>

        <!--        Interface                   -->
        <div class="page-item">
            <main class="container">
                <form class="rounded-perso-2" @submit.prevent="checkForm" data-item="change_interface">
                    <h1>Interface</h1>
                    <div class="item-list">
                        <label for="change_theme">
                            Thème Global
                        </label>
                        <select name="change_theme" id="change_theme">
                            <option value="white" :selected="item_interface.form.selectedTheme === 'white'"
                                >Par défaut</option
                            >
                            <option value="dark" :selected="item_interface.form.selectedTheme === 'dark'">Foncé</option>
                        </select>
                    </div>
                    <div class="item-list">
                        <label for="change_theme_server_icon">
                            Thème Serveur
                        </label>
                        <select name="change_theme" id="change_theme_server_icon">
                            <option value="v1" :selected="item_interface.form.selectedServerTheme === 'v1'">
                                Bleu
                            </option>
                            <option value="v2" :selected="item_interface.form.selectedServerTheme === 'v2'">
                                Gris
                            </option>
                        </select>
                    </div>

                    <!-- Bouton -->
                    <div class="item-btns">
                        <button type="submit" class="rounded-perso-1">
                            Valider
                        </button>
                    </div>
                </form>
            </main>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../components/EventBus.js';

    import { User } from '../api/User.js';
    import { Server } from '../api/Server.js';

    const userAPI = new User();
    const serverAPI = new Server();
    export default {
        name: 'Settings',
        data() {
            return {
                item_password: {
                    form: {
                        error: [],
                        current_password: '',
                        new_password: '',
                        re_password: '',
                    },
                },
                item_email: {
                    form: {
                        email: '',
                    },
                },
                item_startup: {
                    serversLoading: true,
                    serversData: [],
                    form: {
                        error: [],
                        selectedServer: -1,
                    },
                },
                item_interface: {
                    form: {
                        selectedTheme: 'white',
                        selectedServerTheme: 'v1',
                    },
                },
            };
        },
        computed: {
            getPassword() {
                return this.item_password.form.current_password.length >= 5;
            },
            getEmail() {
                return this.item_email.form.email.length >= 5;
            },
            getNewPassword() {
                return this.item_password.form.new_password.length >= 5;
            },
            getRePassword() {
                return (
                    this.item_password.form.re_password.length >= 5 &&
                    this.item_password.form.new_password === this.item_password.form.re_password
                );
            },
            getCurrentTheme() {
                return localStorage.getItem('theme');
            },
            getCurrentServerTheme() {
                return localStorage.getItem('server_theme');
            },
			isAdmin() {
				return this.$store.getters.isAdmin;
			},
        },
        methods: {
            checkForm(evt) {
                switch (evt.target.dataset.item) {
                    case 'change_password':
                        if (this.getPassword && this.getNewPassword && this.getRePassword) {
                            EventBus.$emit('loaderRequest', true);
                            userAPI
                                .updatePassword(
                                    this.$store.getters.user.id,
                                    this.item_password.form.current_password,
                                    this.item_password.form.new_password,
                                    this.$store.getters.token
                                )
                                .then((result) => {
                                    if (result.tokens !== null) {
                                        EventBus.$emit('expiredJWT', result.tokens);
                                    }

                                    if (result.status) {
                                        if (result.data === 'password updated.') {
                                            EventBus.$emit('sendToast', {
                                                type: 'success',
                                                message: 'Mot de passe bien modifié.',
                                            });

                                            userAPI
                                                .auth(
                                                    this.$store.getters.user.email,
                                                    this.item_password.form.new_password
                                                )
                                                .then((result) => {
                                                    if (result.status) {
                                                        EventBus.$emit('loaderRequest', false);
                                                        EventBus.$emit('expiredJWT', {
                                                            token: result.data.token,
                                                            refresh_token: result.data.refresh_token,
                                                        });
                                                        this.item_password.form.current_password = '';
                                                        this.item_password.form.new_password = '';
                                                        this.item_password.form.re_password = '';
                                                    } else {
                                                        EventBus.$emit('loaderRequest', false);
                                                        this.$router.push({ name: 'ForceLogout' });
                                                    }
                                                });
                                        }
                                    } else {
                                        EventBus.$emit('sendToast', {
                                            type: 'error',
                                            message: 'Mot de passe non modifié.',
                                        });
                                        EventBus.$emit('loaderRequest', false);
                                    }
                                });
                        } else {
                            if (!this.getPassword) this.item_password.form.error.push('Mot de Passe');

                            if (!this.getNewPassword) this.item_password.form.error.push('Nouveau Mot de Passe');

                            if (!this.getRePassword) this.item_password.form.error.push('Confirmation du Mot de Passe');

                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message:
                                    this.item_password.form.error.length === 1
                                        ? this.item_password.form.error.join() + ' non valide.'
                                        : this.item_password.form.error.join(' et ') + ' non valides.',
                            });
                        }
                        break;
                    case 'change_startup':
                        const selectElement = evt.target[0];
                        const serverLogin = selectElement.selectedOptions[0].dataset.login || null;
                        const serverID = selectElement.selectedOptions[0].dataset.id || null;
                        const serverName = selectElement.selectedOptions[0].dataset.name || null;

                        const [serverHost, serverPort] =
                            selectElement.value !== -1
                                ? selectElement.selectedOptions[0].value.split(':')
                                : [null, null];

                        if (
                            !(
                                serverName === null ||
                                serverLogin === null ||
                                serverHost === null ||
                                serverPort === null ||
                                serverID === null
                            )
                        ) {
                            this.item_startup.form.selectedServer = parseInt(serverID);
                            this.$store.dispatch('setSTUP_Server', parseInt(serverID));
                            EventBus.$emit('sendToast', {
                                type: 'success',
                                message: 'Serveur par défaut modifié.',
                            });
                        } else {
                            this.item_startup.form.selectedServer = -1;
                            this.$store.dispatch('setSTUP_Server', -1);
                            EventBus.$emit('sendToast', {
                                type: 'success',
                                message: 'Désactivation de la connexion à un serveur au démarrage.',
                            });
                        }
                        break;
                    case 'change_email':
                        this.item_email.form.error = [];
                        if (this.getEmail) {
                            EventBus.$emit('loaderRequest', true);
                            userAPI
                                .updateEmail(
                                    this.$store.getters.user.id,
                                    this.$store.getters.user.email,
                                    this.item_email.form.email,
                                    this.$store.getters.token
                                )
                                .then((result) => {
                                    if (result.tokens !== null) {
                                        EventBus.$emit('expiredJWT', result.tokens);
                                    }
                                    if (result.status) {
                                        if (result.data === 'email updated.') {
                                            this.$router.push({ name: 'ForceLogout' });
                                            EventBus.$emit('sendToast', {
                                                type: 'success',
                                                message: 'Email bien modifié.',
                                            });
                                        }
                                    } else {
                                        EventBus.$emit('sendToast', {
                                            type: 'success',
                                            message: 'Email non modifié.',
                                        });
                                        EventBus.$emit('loaderRequest', false);
                                    }
                                });
                        } else {
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Email non valide.',
                            });
                        }
                        break;
                    case 'change_interface': {
                        const selectElement_GlobalTheme = evt.target[0];
                        const selectElement_ServerTheme = evt.target[1];

                        const selectedValue_GlobalTheme = selectElement_GlobalTheme.value;
                        const selectedValue_ServerTheme = selectElement_ServerTheme.value;

                        this.item_interface.form.selectedTheme = selectedValue_GlobalTheme;
                        this.item_interface.form.selectedServerTheme = selectedValue_ServerTheme;

                        EventBus.$emit('setTheme', selectedValue_GlobalTheme);
                        EventBus.$emit('setServerTheme', selectedValue_ServerTheme);

                        EventBus.$emit('sendToast', {
                            type: 'success',
                            message: 'Interface à jour.',
                        });
                        break;
                    }
                    default:
                        break;
                }
            },
            findServerByID(id, array) {
                return array.find((server) => server.id === id);
            },
        },
        mounted() {
            serverAPI
                .getAll(this.$store.getters.token)
                .then(async (result) => {
                    if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
                    if (result.status) {
						const servers = await result.data.filter((server) => {
							if (!this.isAdmin && server.accepted_roles.indexOf('ROLE_USER') !== -1) return server;
							else if (this.isAdmin) return server;
						});
                        this.item_startup.serversData = servers;
                        this.item_startup.serversLoading = false;

                        if (this.findServerByID(this.$store.getters.STUP_server, servers) !== undefined) {
                            this.item_startup.form.selectedServer = this.$store.getters.STUP_server;
                        } else {
                            this.$store.dispatch('setSTUP_Server', -1);
                            this.item_startup.form.selectedServer = -1;
                        }
                    }
                })
                .catch((err) => console.error(err));

            this.item_interface.form.selectedTheme = this.getCurrentTheme !== null ? this.getCurrentTheme : 'white';
            this.item_interface.form.selectedServerTheme =
                this.getCurrentServerTheme !== null ? this.getCurrentServerTheme : 'v1';
        },
    };
</script>

<style lang="scss" scoped>
    #page-container {
        max-width: 2200px;

        display: flex;
        flex-direction: row;
        flex-wrap: wrap;
        justify-content: space-around;
        align-items: flex-start;

        padding-top: var(--navbar-top-height);
        padding-bottom: var(--navbar-top-height);
        box-sizing: border-box;

        .page-item {
            width: 400px;
            height: auto;

            margin: 2.5px 50px 50px 25px !important;

            main.container {
                form {
                    padding: 2em;

                    .item-list {
                        display: flex;
                        flex-direction: column;
                        align-items: flex-start;

                        border-left: 1px solid silver;
                        padding: 1rem;
                        margin-top: 1.5rem;
                        margin-bottom: 1.5rem;

                        select {
                            width: 100%;
                            min-width: 50%;

                            background-color: var(--color-main-blue);
                            border: none;
                            border-radius: 15px;

                            color: white;

                            padding: 0.25rem 0.5rem;
                            line-height: 1.25;

                            &:focus {
                                outline: none;
                            }
                        }
                    }
                }
            }
        }
    }
</style>
