<template>
    <div class="main">
        <div class="container w-r-500">
            <form @submit.prevent="checkForm" class="rounded-perso-2 shadow-perso-md">
                <h1>Mot de Passe Perdu</h1>

                <!-- Email -->
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

                <!-- Buttons -->
                <div class="item-btns">
                    <button type="sumbit" class="rounded-perso-1">
                        Envoyer
                    </button>

                    <router-link :to="{ name: 'Login' }">
                        Connexion
                    </router-link>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '@/components/EventBus.js';
    import { User } from '@/api/User.js';

    export default {
        name: 'Lostpass',
        data() {
            return {
                form: {
                    error: [],
                    email: '',
                    reg: /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,24}))$/,
                },
            };
        },
        computed: {
            getLoginEmail() {
                return this.form.reg.test(this.form.email);
            },
        },
        methods: {
            /** @param {Event} evt */
            checkForm(evt) {
                this.error = [];

                if (this.getLoginEmail) {
                    const user = new User();
                    EventBus.$emit('loaderRequest', true);
                    user.resetPassword(this.form.email).then((result) => {
                        EventBus.$emit('loaderRequest', false);
                        if (result.status) {
                            EventBus.$emit('sendToast', {
                                type: 'success',
                                message:
                                    'Opération en cours.. Vous allez recevoir un email avec les instructions à suivre.',
                            });
                            this.$router.push({ name: 'Login' });
                        } else {
                            if (result.data === 'user not found.') {
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: 'Utilisateur introuvable.',
                                });
                            } else if (result.data === 'user already has a token.') {
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: 'Une demande de réinitialisation est déjà en cours.',
                                });
                            } else if (result.data === 'user not verified.') {
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: "Votre compte n'est pas encore vérifié.",
                                });
                            } else {
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: "Une erreur c'est produite.",
                                });
                                throw new Error(JSON.stringify(result));
                            }
                        }
                        this.form.email = '';
                    });
                } else {
                    if (!this.getLoginEmail) {
                        this.error.push('Email');
                    }

                    EventBus.$emit('sendToast', {
                        type: 'error',
                        message:
                            this.error.length === 1
                                ? this.error.join() + ' non valide.'
                                : this.error.join(' et ') + ' non valides.',
                    });
                    return false;
                }
            },
        },
    };
</script>
