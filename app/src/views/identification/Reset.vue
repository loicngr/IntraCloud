<template>
    <div>
        <div v-if="!isLoading && user !== null">
            <div class="main">
                <div class="container w-r-500">
                    <form @submit.prevent="checkForm" class="rounded-perso-2 shadow-perso-md">
                        <h1>Changer votre Mot de Passe</h1>
                        <!-- Email -->
                        <div :class="['item', isValidField(form.email, getLoginEmail)]">
                            <svg
                                width="20"
                                height="16"
                                viewBox="0 0 20 16"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M20 2C20 0.9 19.1 0 18 0H2C0.9 0 0 0.9 0 2V14C0 15.1 0.9 16 2 16H18C19.1 16 20 15.1 20 14V2ZM18 2L10 7L2 2H18ZM18 14H2V4L10 9L18 4V14Z"
                                    fill="#9B9CAA"
                                    fill-opacity="0.8"
                                />
                            </svg>
                            <input type="email" required v-model="form.email" placeholder="Email" />
                        </div>
                        <!-- Password -->
                        <div :class="['item', isValidField(form.password, getLoginPassword)]">
                            <svg
                                width="20"
                                height="21"
                                viewBox="0 0 20 21"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
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
                        <!-- Re Password -->
                        <div :class="['item', isValidField(form.repassword, getLoginRePassword)]">
                            <svg
                                width="20"
                                height="21"
                                viewBox="0 0 20 21"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
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
                                v-model="form.repassword"
                                placeholder="Confirmer le Mot de Passe"
                            />
                        </div>

                        <!-- Buttons -->
                        <div class="item-btns">
                            <button type="submit" class="rounded-perso-1">
                                Valider
                            </button>
                            <router-link :to="{ name: 'Login' }">Connexion</router-link>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import { EventBus } from '@/components/EventBus.js';
    import { User } from '@/api/User.js';

    export default {
        name: 'Reset',
        data() {
            return {
                user: null,
                isLoading: true,
                form: {
                    error: [],
                    password: '',
                    repassword: '',
                    email: '',
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
            getLoginRePassword() {
                return this.form.repassword.length > 5 && this.form.password === this.form.repassword;
            },
        },
        methods: {
            checkForm(evt) {
                this.error = [];

                if (this.getLoginPassword && this.getLoginRePassword && this.getLoginEmail) {
                    const user = new User();
                    EventBus.$emit('loaderRequest', true);
                    user.updateResetPassword(
                        this.$router.currentRoute.params.token,
                        this.form.password,
                        this.form.email
                    ).then((result) => {
                        EventBus.$emit('loaderRequest', false);
                        if (!result.status) {
                            if (result.data === 'email not valid.') {
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: 'Email incorrect.',
                                });
                            } else if (result.data === 'ip adress not valid.') {
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: 'Mauvaise adresse IP.',
                                });
                            } else {
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: 'Mise à jour du mot de passe impossible.',
                                });
                                throw new Error(JSON.stringify(result));
                            }
                            return false;
                        }

                        if (result.data === 'password updated.') {
                            EventBus.$emit('sendToast', {
                                type: 'success',
                                message: 'Mot de passe bien modifié.',
                            });
                            this.$router.push({ name: 'Login' });
                        }
                    });
                } else {
                    if (!this.getLoginEmail) {
                        this.error.push('Email');
                    }
                    if (!this.getLoginPassword) {
                        this.error.push('Mot de Passe');
                    }
                    if (!this.getLoginRePassword) {
                        this.error.push('Confirmation du Mot de Passe');
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
        beforeMount() {
            EventBus.$emit('loaderRequest', true);
            const token = this.$router.currentRoute.params.token;
            if (token.length >= 7) {
                const user = new User();
                user.checkValidResetToken(token).then((result) => {
                    EventBus.$emit('loaderRequest', false);
                    this.isLoading = false;
                    if (!result.status) {
                        if (result.data === 'expired token.') {
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Vous avez dépassé le délai de réinitialisation.',
                            });
                        } else {
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Token introuvable.',
                            });
                        }
                        if (this.$store.getters.isLoggedIn) this.$store.dispatch('logout');
                        this.$router.push({ name: 'Login' });
                        return false;
                    }

                    this.user = result.data;
                });
            } else {
                if (this.$store.getters.isLoggedIn) this.$store.dispatch('logout');
                this.$router.push({ name: 'Login' });
            }
        },
    };
</script>

<style></style>
