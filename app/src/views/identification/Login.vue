<template>
    <div class="main">
        <div class="container w-r-500">
            <form @submit.prevent="checkForm" class="rounded-perso-2 shadow-perso-md">
                <h1>Bienvenue</h1>
                <div :class="['item', isValidField(form.email, getLoginEmail)]">
                    <svg width="20" height="16" viewBox="0 0 20 16" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path
                            d="M20 2C20 0.9 19.1 0 18 0H2C0.9 0 0 0.9 0 2V14C0 15.1 0.9 16 2 16H18C19.1 16 20 15.1 20 14V2ZM18 2L10 7L2 2H18ZM18 14H2V4L10 9L18 4V14Z"
                            fill="#9B9CAA"
                            fill-opacity="0.8"
                        />
                    </svg>
                    <input type="email" required v-model="form.email" placeholder="Email" />
                </div>
                <div :class="['item', isValidField(form.password, getLoginPassword)]">
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
                        required
                        v-model="form.password"
                        placeholder="Mot de Passe"
                    />
                </div>
                <div class="item-btns">
                    <button type="submit" class="rounded-perso-1">
                        Connexion
                    </button>
                    <router-link :to="{ name: 'Lostpass' }">Mot de Passe Perdu ?</router-link>
                    <router-link :to="{ name: 'Signup' }" class="rounded-perso-1 border-perso-blue"
                        >Inscription</router-link
                    >
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '../../components/EventBus.js';
    import { readTokenAuth } from '../../utils/functions';
    import { User } from '../../api/User.js';

    const userAPI = new User();
    export default {
        name: 'Login',
        data() {
            return {
                form: {
                    error: [],
                    email: '',
                    password: '',
                    reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
                },
            };
        },
        computed: {
            getLoginEmail() {
                return this.form.reg.test(this.form.email);
            },
            getLoginPassword() {
                return this.form.password.length >= 5;
            },
        },
        methods: {
            /**
             * Fonction qui enregistre l'utilisateur avec VUEX
             * et
             * qui redirige l'utilisateur une fois connecté.
             *
             * @param {string} token
             * @param {string} refreshToken
             * @param {object} user_token_data
             */
            login(token, refreshToken, user_token_data) {
                userAPI.resetPasswordProcess(token).then((result) => {
                    if (result.status && result.data !== 'not in process.') {
                        EventBus.$emit('sendToast', {
                            type: 'success',
                            message: 'Votre demande de réinitialisation du mot de passe a été annulée.',
                        });
                    }
                    this.$store
                        .dispatch('login', {
                            token: token,
                            refreshToken: refreshToken,
                        })
                        .then(() => {
                            userAPI
                                .me(token)
                                .then((resultME) => {
                                    EventBus.$emit('loaderRequest', false);

                                    this.$store.dispatch('setUser', {
                                        id: resultME.data.id,
                                        email: user_token_data.username,
                                        firstname: resultME.data.firstname,
                                        lastname: resultME.data.lastname,
                                        roles: user_token_data.roles,
                                    });
                                    if (!result.status || (result.status && result.data === 'not in process.')) {
                                        EventBus.$emit('sendToast', {
                                            type: 'success',
                                            message: 'Connexion réussie.',
                                        });
                                    }
                                    this.$router.push({ name: 'Home' });
                                })
                                .catch((err) => {
                                    console.error(err);
                                    EventBus.$emit('loaderRequest', false);
                                });
                        });
                });
            },
            /**
             * Fonction qui vérifie le formulaire
             * et
             * Qui authentifie l'utilisateur
             */
            checkForm(evt) {
                // Tableau qui va contenir les futures erreurs
                this.error = [];

                if (this.getLoginEmail && this.getLoginPassword) {
                    EventBus.$emit('loaderRequest', true);

                    // Authentification de l'utilisateur
                    userAPI.auth(this.form.email, this.form.password).then((result) => {
                        if (result && result.status) {
                            const token = result.data.token;
                            const refreshToken = result.data.refresh_token;

                            const user_token_data = readTokenAuth(token);
                            if (user_token_data) {
                                if (user_token_data.roles.includes('ROLE_NOT_VERIFIED')) {
                                    EventBus.$emit('loaderRequest', false);
                                    EventBus.$emit('sendToast', {
                                        type: 'error',
                                        message:
                                            "Votre compte n'est pas encore vérifié, merci de patienter ou contacter un Administrateur.",
                                    });
                                } else if (user_token_data.roles.length === 0) {
                                    EventBus.$emit('loaderRequest', false);
                                    EventBus.$emit('sendToast', {
                                        type: 'error',
                                        message: "Vous n'avez aucun role, merci de contacter un administrateur.",
                                    });
                                    throw new Error('No roles given.');
                                } else {
                                    this.login(token, refreshToken, user_token_data);
                                }
                            } else {
                                EventBus.$emit('loaderRequest', false);
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: 'Connexion impossible, merci de contacter un administrateur.',
                                });
                                throw new Error(user_token_data);
                            }
                        } else {
                            EventBus.$emit('loaderRequest', false);
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Mot de passe ou Email incorrect.',
                            });
                            throw new Error(JSON.stringify(result.data));
                        }
                    });
                } else {
                    // Gestion des erreurs liées au formulaire
                    if (!this.getLoginEmail) {
                        this.error.push('Email');
                    }
                    if (!this.getLoginPassword) {
                        this.error.push('Mot de Passe');
                    }

                    EventBus.$emit('sendToast', {
                        type: 'error',
                        message:
                            this.error.length === 1
                                ? this.error.join() + ' non valide.'
                                : this.error.join(' et ') + ' non valides.',
                    });
                    return;
                }
            },
        },
    };
</script>
