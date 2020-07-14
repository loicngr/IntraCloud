<template>
    <div id="app">
        <loading
            :active.sync="loaderRequest"
            :is-full-page="fullPage"
            :z-index="9999"
            :opacity="1"
            transition="fade"
        ></loading>
        <loading
            :active.sync="isLoading"
            :is-full-page="fullPage"
            :z-index="9999"
            :opacity="1"
            transition="fade"
        ></loading>
        <FirstLaunch v-if="!isLoading && isFirstLaunch && isLoggedIn" :step="firstLaunchStep" />
        <nav v-if="!isLoading && isLoggedIn && route.name !== 'AdminEditor'">
            <LeftNavbar>
                <template v-slot:page_logo></template>
                <template v-slot:pages>
                    <ul>
                        <!-- Accueil -->
                        <li :class="route.name === 'Home' ? 'active' : ''">
                            <router-link :to="{ name: 'Home' }" aria-describedby="tooltip">
                                <svg
                                    class="svg-icon"
                                    width="25"
                                    height="24"
                                    viewBox="0 0 25 24"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M24.4826 11.6091L13.1237 0.258509C13.042 0.176562 12.9448 0.111549 12.8379 0.0671908C12.7309 0.0228326 12.6163 0 12.5005 0C12.3847 0 12.2701 0.0228326 12.1632 0.0671908C12.0562 0.111549 11.9591 0.176562 11.8773 0.258509L0.518442 11.6091C0.187522 11.94 0 12.3895 0 12.8583C0 13.8318 0.791451 14.6232 1.76491 14.6232H2.96174V22.7225C2.96174 23.2106 3.35608 23.6049 3.84419 23.6049H10.7356V17.4278H13.8242V23.6049H21.1568C21.645 23.6049 22.0393 23.2106 22.0393 22.7225V14.6232H23.2361C23.7049 14.6232 24.1544 14.4384 24.4853 14.1048C25.172 13.4154 25.172 12.2985 24.4826 11.6091Z"
                                    />
                                </svg>
                            </router-link>
                            <div class="tooltip" role="tooltip">
                                Accueil
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </li>

                        <!-- Paramètres -->
                        <li :class="route.name === 'Settings' ? 'active' : ''">
                            <router-link :to="{ name: 'Settings' }" aria-describedby="tooltip">
                                <svg
                                    class="svg-icon"
                                    width="25"
                                    height="26"
                                    viewBox="0 0 25 26"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M22.0623 14.1105C22.1106 13.7087 22.1427 13.2909 22.1427 12.857C22.1427 12.423 22.1106 12.0052 22.0463 11.6034L24.7623 9.48203C24.8788 9.38441 24.9583 9.24979 24.9876 9.10063C25.0168 8.95147 24.994 8.79678 24.923 8.6624L22.3516 4.21069C22.1909 3.9214 21.8534 3.82498 21.5641 3.9214L18.366 5.2071C17.7019 4.69445 16.9722 4.27286 16.1964 3.95355L15.7142 0.546461C15.691 0.393172 15.6131 0.253449 15.495 0.153036C15.3769 0.0526239 15.2264 -0.00171152 15.0714 4.11129e-05H9.92861C9.77533 -0.0015144 9.62682 0.0532762 9.51127 0.154008C9.39573 0.25474 9.3212 0.394398 9.30184 0.546461L8.8197 3.95355C8.04519 4.27551 7.31586 4.6969 6.6501 5.2071L3.45194 3.9214C3.30792 3.86588 3.14863 3.86462 3.00375 3.91784C2.85887 3.97106 2.73828 4.07514 2.66445 4.21069L0.0930633 8.6624C0.0135387 8.79465 -0.0140512 8.95171 0.015642 9.10315C0.0453351 9.25458 0.130196 9.3896 0.253775 9.48203L2.9698 11.6034C2.90552 12.0052 2.8573 12.4391 2.8573 12.857C2.8573 13.2748 2.88945 13.7087 2.95373 14.1105L0.237704 16.2319C0.121182 16.3295 0.0416616 16.4641 0.0124138 16.6133C-0.016834 16.7625 0.00596012 16.9172 0.076992 17.0515L2.64838 21.5033C2.80909 21.7925 3.14658 21.889 3.43586 21.7925L6.63403 20.5068C7.29815 21.0195 8.02781 21.4411 8.80363 21.7604L9.28577 25.1675C9.35005 25.4889 9.60719 25.7139 9.92861 25.7139H15.0714C15.3928 25.7139 15.666 25.4889 15.6982 25.1675L16.1803 21.7604C16.9548 21.4384 17.6841 21.017 18.3499 20.5068L21.5481 21.7925C21.8373 21.905 22.1748 21.7925 22.3356 21.5033L24.9069 17.0515C24.9865 16.9193 25.0141 16.7622 24.9844 16.6108C24.9547 16.4594 24.8698 16.3243 24.7462 16.2319L22.0623 14.1105ZM12.5 17.6783C9.84826 17.6783 7.67865 15.5087 7.67865 12.857C7.67865 10.2052 9.84826 8.03562 12.5 8.03562C15.1517 8.03562 17.3213 10.2052 17.3213 12.857C17.3213 15.5087 15.1517 17.6783 12.5 17.6783Z"
                                    />
                                </svg>
                            </router-link>
                            <div class="tooltip" role="tooltip">
                                Paramètres
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </li>

                        <!-- Admin - Utilisateurs -->
                        <li :class="route.name === 'AdminUsers' ? 'active' : ''" v-if="isAdmin">
                            <router-link :to="{ name: 'AdminUsers' }" aria-describedby="tooltip">
                                <svg
                                    class="svg-icon"
                                    width="25"
                                    height="17"
                                    viewBox="0 0 25 17"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M25 11.0417C25 11.6667 23.75 12.0833 22.2917 12.2917C21.3542 10.5208 19.4792 9.16667 17.2917 8.22917C17.5 7.91667 17.7083 7.70833 17.9167 7.39583H18.75C21.9792 7.29167 25 9.27083 25 11.0417ZM7.08333 7.29167H6.25C3.02083 7.29167 0 9.27083 0 11.0417C0 11.6667 1.25 12.0833 2.70833 12.2917C3.64583 10.5208 5.52083 9.16667 7.70833 8.22917L7.08333 7.29167ZM12.5 8.33333C14.7917 8.33333 16.6667 6.45833 16.6667 4.16667C16.6667 1.875 14.7917 0 12.5 0C10.2083 0 8.33333 1.875 8.33333 4.16667C8.33333 6.45833 10.2083 8.33333 12.5 8.33333ZM12.5 9.375C8.22917 9.375 4.16667 12.0833 4.16667 14.5833C4.16667 16.6667 12.5 16.6667 12.5 16.6667C12.5 16.6667 20.8333 16.6667 20.8333 14.5833C20.8333 12.0833 16.7708 9.375 12.5 9.375ZM18.4375 6.25H18.75C20.5208 6.25 21.875 4.89583 21.875 3.125C21.875 1.35417 20.5208 0 18.75 0C18.2292 0 17.8125 0.104167 17.3958 0.3125C18.2292 1.35417 18.75 2.70833 18.75 4.16667C18.75 4.89583 18.6458 5.625 18.4375 6.25ZM6.25 6.25H6.5625C6.35417 5.625 6.25 4.89583 6.25 4.16667C6.25 2.70833 6.77083 1.35417 7.60417 0.3125C7.1875 0.104167 6.77083 0 6.25 0C4.47917 0 3.125 1.35417 3.125 3.125C3.125 4.89583 4.47917 6.25 6.25 6.25Z"
                                    />
                                </svg>
                            </router-link>
                            <div class="tooltip" role="tooltip">
                                Utilisateurs
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </li>

                        <!-- Admin - Serveurs -->
                        <li :class="route.name === 'AdminServers' ? 'active' : ''" v-if="isAdmin">
                            <router-link :to="{ name: 'AdminServers' }" aria-describedby="tooltip">
                                <svg
                                    class="svg-icon"
                                    width="25"
                                    height="23"
                                    viewBox="0 0 25 23"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M22.5 0H2.5C1.12125 0 0 1.12125 0 2.5V7.5C0 8.87875 1.12125 10 2.5 10H22.5C23.8788 10 25 8.87875 25 7.5V2.5C25 1.12125 23.8788 0 22.5 0ZM2.5 7.5V2.5H22.5L22.5025 7.5H2.5ZM22.5 12.5H2.5C1.12125 12.5 0 13.6212 0 15V20C0 21.3788 1.12125 22.5 2.5 22.5H22.5C23.8788 22.5 25 21.3788 25 20V15C25 13.6212 23.8788 12.5 22.5 12.5ZM2.5 20V15H22.5L22.5025 20H2.5Z"
                                    />
                                    <path
                                        d="M18.75 3.75H21.25V6.25H18.75V3.75ZM15 3.75H17.5V6.25H15V3.75ZM18.75 16.25H21.25V18.75H18.75V16.25ZM15 16.25H17.5V18.75H15V16.25Z"
                                    />
                                </svg>
                            </router-link>
                            <div class="tooltip" role="tooltip">
                                Serveurs
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </li>

                        <!-- Page de déconnexion -->
                        <li :class="route.name === 'Logout' ? 'active' : ''">
                            <router-link :to="{ name: 'Logout' }" aria-describedby="tooltip">
                                <svg
                                    class="svg-icon"
                                    width="25"
                                    height="22"
                                    viewBox="0 0 25 22"
                                    xmlns="http://www.w3.org/2000/svg"
                                >
                                    <path
                                        d="M19.1919 4.94529L25 10.5429L19.1919 16.0985V12.3737H10.5008V8.67003H19.1919V4.94529ZM15.6987 15.7197L17.9293 17.9714C15.7407 20.0196 13.3698 21.0438 10.8165 21.0438C7.77217 21.0438 5.20833 20.0372 3.125 18.024C1.04167 16.0108 0 13.4961 0 10.4798C0 8.58586 0.476992 6.83221 1.43098 5.21886C2.38496 3.6055 3.67214 2.33235 5.29251 1.39941C6.91288 0.46647 8.67003 0 10.564 0C13.1453 0 15.5934 1.03816 17.9083 3.11448L15.6987 5.34512C14.0993 3.88608 12.3948 3.15657 10.585 3.15657C8.49467 3.15657 6.7305 3.88608 5.29251 5.34512C3.85452 6.80415 3.13552 8.58586 3.13552 10.6902C3.13552 12.6543 3.87556 14.3448 5.35564 15.7618C6.83572 17.1787 8.57183 17.8872 10.564 17.8872C12.4018 17.8872 14.1134 17.1647 15.6987 15.7197Z"
                                    />
                                </svg>
                            </router-link>
                            <div class="tooltip" role="tooltip">
                                Déconnexion
                                <div class="arrow" data-popper-arrow></div>
                            </div>
                        </li>
                    </ul>
                </template>
            </LeftNavbar>
            <TopNavbar>
                <template v-slot:page_name>
                    <h2>{{ route.title }}</h2>
                </template>
                <template v-slot:user_name>
                    <div>
                        <h5>
                            {{ userName }}
                        </h5>
                    </div>
                </template>
            </TopNavbar>
        </nav>
        <transition name="fade">
            <router-view v-if="!isLoading" />
        </transition>
    </div>
</template>

<script>
    import Swal from 'sweetalert2';
    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    import FirstLaunch from './components/FirstLaunch/FirstLaunch';

    /** Navbars */
    import TopNavbar from './components/TopNavbar';
    import LeftNavbar from './components/LeftNavbar';

    /** Event Bus */
    import { EventBus } from './components/EventBus.js';

    /** JsonWebToken */
    import { isValidToken as API_isValidToken } from './api/isValidToken.js';
    import { refreshToken as API_refreshToken } from './api/refreshToken.js';

    export default {
        name: 'app',
        components: {
            TopNavbar,
            LeftNavbar,
            Loading,
            FirstLaunch,
        },
        data() {
            return {
                firstLaunchStep: '',
                loaderRequest: false,
                serverErrorTimeout: null,
                isLoading: true,
                fullPage: true,
                route: {
                    name: null,
                    title: null,
                },
            };
        },
        computed: {
            isLoggedIn() {
                return this.$store.getters.isLoggedIn;
            },
            userName() {
                const user = this.$store.getters.user;
                return `${user.firstname} ${user.lastname}`;
            },
            isAdmin() {
                return this.$store.getters.isAdmin;
            },
            isFirstLaunch() {
                return this.$store.getters.isFirstLaunch;
            },
            getTheme() {
                return window.localStorage.getItem('theme');
            },
            getServerTheme() {
                return window.localStorage.getItem('server_theme');
            },
        },
        watch: {
            isLoading(newValue) {
                if (!newValue) clearTimeout(this.serverErrorTimeout);
            },
        },
        methods: {
            /**
             * Vérification si le localStorage est disponible dans le navigateur
             * */
            storageAvailable(type) {
                let storage;
                try {
                    storage = window[type];
                    let x = '__storage_test__';
                    storage.setItem(x, x);
                    storage.removeItem(x);
                    return true;
                } catch (e) {
                    return (
                        e instanceof DOMException &&
                        // everything except Firefox
                        (e.code === 22 ||
                            // Firefox
                            e.code === 1014 ||
                            // test name field too, because code might not be present
                            // everything except Firefox
                            e.name === 'QuotaExceededError' ||
                            // Firefox
                            e.name === 'NS_ERROR_DOM_QUOTA_REACHED') &&
                        // acknowledge QuotaExceededError only if there's something already stored
                        storage &&
                        storage.length !== 0
                    );
                }
            },

            /**
             * Mise à jour du thème (Thème couleur de l'interface)
             * */
            setTheme(color) {
                localStorage.setItem('theme', color);
                document.body.setAttribute('data-theme', color);
            },

            setServerTheme(version) {
                localStorage.setItem('server_theme', version);
                document.body.setAttribute('data-server-theme', version);
            },

            /**
             * Fonction qui envoi un message avec SweetAlert
             **/
            sendToast(type, message, timer = 4000) {
                Swal.fire({
                    icon: type,
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: timer,
                    timerProgressBar: true,
                    text: message,
                    onOpen(toast) {
                        toast.addEventListener('mouseenter', Swal.stopTimer);
                        toast.addEventListener('mouseleave', Swal.resumeTimer);
                    },
                });
            },

            /**
             * L'utilisateur est connecté mais ne peut pas rester sur le page ou il est
             * */
            isLoginAndCantStayHere(pagename) {
                if (pagename === 'Home') {
                    EventBus.$emit('sendToast', {
                        type: 'error',
                        message: 'Vous êtes déjà connecté.',
                    });
                    this.$router.push({ name: pagename });
                } else if (pagename === 'Logout') {
                    this.isLoading = false;
                    EventBus.$emit('sendToast', {
                        type: 'error',
                        message: 'Session expiré.',
                    });
                    this.$router.push({ name: 'ForceLogout' });
                }
            },

            /**
             * L'utilisateur n'est pas connecté et ne peux pas rester sur la page ou il est
             * */
            isNotLoginAndCantStayHere(pagename = 'Login') {
                EventBus.$emit('sendToast', {
                    type: 'error',
                    message: "Vous n'êtes pas connecté.",
                });
                this.$router.push({ name: pagename });
            },

            /**
             * Initialisation des évènements
             * */
            initEventsListeners() {
                EventBus.$on('setTheme', (color) => {
                    this.setTheme(color);
                });
                EventBus.$on('setServerTheme', (version) => {
                    this.setServerTheme(version);
                });
                EventBus.$on('sendToast', ({ type, message, timer }) => {
                    this.sendToast(type, message, timer);
                });
                EventBus.$on('loaderRequest', (status) => {
                    this.loaderRequest = status;
                });
                EventBus.$on('expiredJWT', (tokens) => {
                    this.expiredJWT(tokens);
                });
                EventBus.$on('firstLaunch_next', () => {
                    this.$store.dispatch('nextStep').then((status) => {
                        if (!status) {
                            this.firstLaunchStep = '';
                            EventBus.$emit('sendToast', {
                                type: 'success',
                                message: 'Vous avez fini le tutoriel, bonne navigation.',
                            });
                            return;
                        }

                        this.firstLaunchStep = status;
                    });
                });
            },
            expiredJWT(tokens) {
                this.$store.dispatch('setToken', tokens);
            },
            initRouter() {
                this.$router.beforeEach((to, from, next) => {
                    /**
                     * Si l'utilisateur fais un retour arrière et que la page est null
                     */
                    if (to.name === null) {
                        this.$router.go(-1);
                        return;
                    }

                    if (!this.$store.getters.socket && to.meta.requiresAuth) {
                        this.initSocket();
                    }

                    //  Récupération du meta title de la Page
                    const nearestWithTitle = to.matched
                        .slice()
                        .reverse()
                        .find((r) => r.meta && r.meta.title);

                    // Si la page à un Titre, on le mets à jour
                    if (nearestWithTitle) document.title = nearestWithTitle.meta.title;

                    // Si la page demandé demande à être connecté
                    if (to.matched.some((record) => record.meta.requiresAuth)) {
                        // est connecté
                        if (this.$store.getters.isLoggedIn) {
                            if (to.matched.some((record) => record.meta.requiresVerified)) {
                                // est vérifié
                                if (this.$store.getters.isVerified) {
                                    if (to.matched.some((record) => record.meta.requiresAdmin)) {
                                        // être admin
                                        if (!this.$store.getters.isAdmin) {
                                            EventBus.$emit('sendToast', {
                                                type: 'error',
                                                message: "Vous n'êtes pas administrateur.",
                                            });
                                            next(false);
                                        } else {
                                            next();
                                        }
                                    } else {
                                        if (to.name === 'Logout') {
                                            Swal.fire({
                                                title: 'Êtes-vous sûr de vouloir vous déconnecter ?',
                                                icon: 'warning',
                                                showCancelButton: true,
                                                confirmButtonColor: '#d33',
                                                cancelButtonColor: '#3085d6',
                                                cancelButtonText: 'Non',
                                                confirmButtonText: 'Oui',
                                            }).then((result) => {
                                                if (result.value) {
                                                    next();
                                                } else {
                                                    next(false);
                                                }
                                            });
                                            return;
                                        }

                                        next();
                                    }
                                }
                                // pas vérifié
                                else if (!this.$store.getters.isVerified) {
                                    EventBus.$emit('sendToast', {
                                        type: 'error',
                                        message:
                                            "Votre compte n'est pas encore vérifié, merci de patienter ou contacter un Administrateur.",
                                    });
                                    next(false);
                                }
                            }
                        }
                        // pas connecté
                        else if (!this.$store.getters.isLoggedIn) {
                            if (to.name !== 'Login') {
                                next();
                            } else {
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: 'Vous êtes déjà connecté.',
                                });
                                next(false);
                            }
                        }
                    }
                    // Sinon
                    else {
                        if (this.$store.getters.isLoggedIn) {
                            this.$store.dispatch('logout');
                        }
                        next();
                    }

                    this.route.name = to.name;
                    this.route.title = to.meta.topbar;
                });
            },
            initFirstLaunch() {
                if (this.isFirstLaunch) {
                    EventBus.$emit('firstLaunch_next');
                }
                EventBus.$on('ws_ssh_init', () => {
                    const socket = this.$store.getters.socket || null;
                    if (socket) {
                        socket.close();
                        console.log('ws -- restart websocket connexion');
                        this.initSocket();
                    }
                });
            },
            initSocket() {
                const adresse_url = process.env.VUE_APP_WS_BASE_IP;
                const socket = new WebSocket('ws://' + adresse_url);
                socket.addEventListener('open', () => {
                    console.log('ws -- connected');
                    this.$store.dispatch('setSocket', socket);
                });
                socket.addEventListener('error', (err) => {
                    throw new Error(`WebSocket Erreur -- ${JSON.stringify(err)}.`);
                });
            },

            handlerBeforeUnload() {
                EventBus.$emit('closeBrowser');
            },
        },
        mounted() {
            this.initEventsListeners();
            this.initRouter();
            this.initFirstLaunch();
            window.addEventListener('beforeunload', this.handlerBeforeUnload);
        },
        beforeMount() {
            // Initialisation du thème
            this.setTheme(this.getTheme !== null ? this.getTheme : 'white');
            this.setServerTheme(this.getServerTheme !== null ? this.getServerTheme : 'v1');

            /**
             * Vérification si l'utilisateur est sur une page liée à l'identification
             *
             * * On ne prend pas en compte la page Logout
             * */
            const identificationPages = ['Login', 'Signup', 'Lostpass', 'ResetPassword', 'Logout'];
            const checkPageIdentification = () => {
                return (
                    identificationPages
                        .slice(0, identificationPages.length - 1)
                        .indexOf(this.$router.currentRoute.name) !== -1
                );
            };

            /**
             * Si le navigateur ne supporte pas le Local Storage ou Fetch
             */
            if (!this.storageAvailable('localStorage') || !window.fetch) {
                Swal.fire({
                    icon: 'error',
                    title: 'Oupss :/',
                    text: 'Navigateur non compatible.',
                    footer:
                        "<a href='https://browser-update.org/fr/update.html' target='_blank'>Pourquoi ai-je besoin d'un navigateur à jour ?</a>",
                });
                this.isLoading = false;
                return;
            }

            this.route.name = this.$router.currentRoute.name;
            this.route.title = this.$router.currentRoute.meta.topbar;

            // Si la route contient un titre de page, on l'applique
            if (this.$router.currentRoute.meta.title) document.title = this.$router.currentRoute.meta.title;

            // Si l'utilisateur est marqué comme Connecté dans le state VueX
            if (this.$store.getters.isLoggedIn) {
                /**
                 * On refresh le Token de l'API,
                 * Si le refresh ne fonctionne pas, on redirige sur la page de connexion.
                 */
                API_isValidToken(this.$store.getters.token).then((data) => {
                    if (data.status) {
                        if (checkPageIdentification()) {
                            this.isLoginAndCantStayHere('Home');
                            return;
                        }
                        this.$store.dispatch('setUser', {
                            id: data.data.id,
                            email: data.data.email,
                            firstname: data.data.firstname,
                            lastname: data.data.lastname,
                            roles: data.data.roles,
                        });

                        /**
                         * Si l'utilisateur est arrivé sur une page qui demande un Compte Admin
                         * Mais qu'il n'a pas les droits, on le redirige sur l'accueil
                         */
                        if (this.$router.currentRoute.meta.requiresAdmin) {
                            if (data.data.roles.includes('ROLE_ADMIN') === false) {
                                this.$router.push({ name: 'Home' });
                            }
                        }

                        if (this.$router.currentRoute.meta.requiresAuth) {
                            this.initSocket();
                        }
                        this.isLoading = false;
                        return;
                    }

                    // Actualisation du Token avec le refreshToken
                    API_refreshToken(this.$store.getters.refreshToken).then((response) => {
                        if (response.status) {
                            /**
                             * Requête vers la route http://...../me
                             * Pour récupérer les informations utilisateurs
                             * */
                            API_isValidToken(response.data.token).then((data) => {
                                if (data.status) {
                                    this.expiredJWT(response.data);
                                    if (checkPageIdentification()) {
                                        this.isLoginAndCantStayHere('Home');
                                        return;
                                    }
                                    this.$store.dispatch('setUser', {
                                        id: data.data.id,
                                        email: data.data.email,
                                        firstname: data.data.firstname,
                                        lastname: data.data.lastname,
                                        roles: data.data.roles,
                                    });
                                    if (this.$router.currentRoute.meta.requiresAdmin) {
                                        if (data.data.roles.includes('ROLE_ADMIN') === false) {
                                            this.$router.push({ name: 'Home' });
                                        }
                                    }
                                    if (this.$router.currentRoute.meta.requiresAuth) {
                                        this.initSocket();
                                    }
                                }
                                this.isLoading = false;
                            });
                        } else {
                            this.isLoading = false;
                            this.isLoginAndCantStayHere('Logout');
                        }
                    });
                });
            } else {
                this.isLoading = false;
                if (!checkPageIdentification() || this.$router.currentRoute.name === 'Logout') {
                    this.isNotLoginAndCantStayHere();
                }
            }
            /**
             * Si le serveur ne répond pas au bout de 20 secondes de chargement
             */
            this.serverErrorTimeout = setTimeout(() => {
                if (this.isLoading) {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oupss :/',
                        text:
                            'Problème de latence avec le serveur... Merci de vérifier votre connexion et de réessayez.',
                    });
                }
            }, 20000);
        },
        beforeDestroy() {
            /** @param {WebSocket} socket */
            const socket = this.$store.getters.socket || null;
            const user = this.$store.getters.user;

            /**
             * Si on trouve le socket
             * et
             * que celui-ci est encore ouvert
             * */
            if (socket && socket.readyState === 1) {
                socket.send(
                    JSON.stringify({
                        type: 'close',
                        email: user.email,
                    })
                );
                socket.close();
            }
        },
    };
</script>

<style lang="scss">
    @import './styles/mvp';
    @import './styles/Variables';
    @import './styles/Fonts';
    @import './styles/App';
    @import './styles/PageContainer';

    .fade-enter-active,
    .fade-leave-active {
        transition: opacity 0.5s;
    }
    .fade-enter,
    .fade-leave-to {
        opacity: 0;
    }
</style>
