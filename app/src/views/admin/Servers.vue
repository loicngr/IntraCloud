<template>
    <div id="page-container">
        <div class="page-item">
            <h4>
                Ajouter un serveur
                <span class="no-bg">
                    <button class="rounded-perso-1" @click="addServer">
                        AJOUTER
                    </button>
                </span>
            </h4>
            <h4 v-if="gridData.length >= 1">
                Rechercher
                <span class="no-bg span-btn">
                    <form id="search" style="width: 100%; height: 100%;">
                        <input
                            name="query"
                            v-model="searchQuery"
                            class="rounded-perso-1"
                            placeholder=" Rechercher un Serveur"
                            style="width: 100%; border: 0; height: 100%;"
                        />
                    </form>
                </span>
            </h4>
        </div>
        <div class="page-item">
            <ServersTable
                v-if="gridData.length !== 0"
                :elements="gridData"
                :columns="gridColumns"
                :filter-key="searchQuery"
                :isLoading="serversLoading"
            />
        </div>
    </div>
</template>

<script>
    import ServersTable from '../../components/Admin/ServersTable';

    import { Server } from '../../api/Server.js';
    import { EventBus } from '../../components/EventBus.js';
    import Swal from 'sweetalert2';

    const serverAPI = new Server();
    export default {
        name: 'Admin-Servers',
        components: {
            ServersTable,
        },
        data() {
            return {
                searchQuery: '',
                gridColumns: ['icone', 'name', 'adresse', 'port', 'login', 'documents', 'actions'],
                gridData: [],
                serversLoading: true,
            };
        },
        methods: {
            addServer(evt) {
                const baseParentElement = evt.target;

                const form = `
                <main class="container">
                    <form id="credentialsSSH">
                        <div class="item">
                            <svg width="19" height="17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17 0H1.889A1.89 1.89 0 000 1.889v3.778a1.89 1.89 0 001.889 1.889H17a1.89 1.89 0 001.889-1.89V1.89A1.89 1.89 0 0017 0zM1.889 5.667V1.889H17l.002 3.778H1.889zM17 9.444H1.889A1.89 1.89 0 000 11.334v3.777A1.89 1.89 0 001.889 17H17a1.89 1.89 0 001.889-1.889v-3.778A1.89 1.89 0 0017 9.444zM1.889 15.111v-3.778H17l.002 3.778H1.889z"
                                        fill="#9B9CAA"
                                    />
                                    <path
                                        d="M14.167 2.833h1.889v1.89h-1.89v-1.89zm-2.834 0h1.89v1.89h-1.89v-1.89zm2.834 9.445h1.889v1.889h-1.89v-1.89zm-2.834 0h1.89v1.889h-1.89v-1.89z"
                                        fill="#9B9CAA"
                                    />
                               </svg>
                            <input name="name" type="text" placeholder="Nom du serveur"  />
                        </div>
                        <div class="item">
                            <svg width="19" height="17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17 0H1.889A1.89 1.89 0 000 1.889v3.778a1.89 1.89 0 001.889 1.889H17a1.89 1.89 0 001.889-1.89V1.89A1.89 1.89 0 0017 0zM1.889 5.667V1.889H17l.002 3.778H1.889zM17 9.444H1.889A1.89 1.89 0 000 11.334v3.777A1.89 1.89 0 001.889 17H17a1.89 1.89 0 001.889-1.889v-3.778A1.89 1.89 0 0017 9.444zM1.889 15.111v-3.778H17l.002 3.778H1.889z"
                                        fill="#9B9CAA"
                                    />
                                    <path
                                        d="M14.167 2.833h1.889v1.89h-1.89v-1.89zm-2.834 0h1.89v1.89h-1.89v-1.89zm2.834 9.445h1.889v1.889h-1.89v-1.89zm-2.834 0h1.89v1.889h-1.89v-1.89z"
                                        fill="#9B9CAA"
                                    />
                               </svg>
                            <input name="adresse" type="text" placeholder="Adresse"  />
                        </div>
                        <div class="item">
                            <svg width="19" height="17" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path
                                        d="M17 0H1.889A1.89 1.89 0 000 1.889v3.778a1.89 1.89 0 001.889 1.889H17a1.89 1.89 0 001.889-1.89V1.89A1.89 1.89 0 0017 0zM1.889 5.667V1.889H17l.002 3.778H1.889zM17 9.444H1.889A1.89 1.89 0 000 11.334v3.777A1.89 1.89 0 001.889 17H17a1.89 1.89 0 001.889-1.889v-3.778A1.89 1.89 0 0017 9.444zM1.889 15.111v-3.778H17l.002 3.778H1.889z"
                                        fill="#9B9CAA"
                                    />
                                    <path
                                        d="M14.167 2.833h1.889v1.89h-1.89v-1.89zm-2.834 0h1.89v1.89h-1.89v-1.89zm2.834 9.445h1.889v1.889h-1.89v-1.89zm-2.834 0h1.89v1.889h-1.89v-1.89z"
                                        fill="#9B9CAA"
                                    />
                               </svg>
                            <input name="port" type="number" placeholder="Port (Par défaut: 22)"  />
                        </div>
                        <div class="item">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10 12C15.5227 12 20 14.0893 20 16.6667V20H0V16.6667C0 14.0893 4.47733 12 10 12ZM18.6667 16.6667C18.6667 14.8267 14.7867 13.3333 10 13.3333C5.21333 13.3333 1.33333 14.8267 1.33333 16.6667V18.6667H18.6667V16.6667ZM10 0C11.2377 0 12.4247 0.491666 13.2998 1.36684C14.175 2.24201 14.6667 3.42899 14.6667 4.66667C14.6667 5.90434 14.175 7.09133 13.2998 7.9665C12.4247 8.84167 11.2377 9.33333 10 9.33333C8.76232 9.33333 7.57534 8.84167 6.70017 7.9665C5.825 7.09133 5.33333 5.90434 5.33333 4.66667C5.33333 3.42899 5.825 2.24201 6.70017 1.36684C7.57534 0.491666 8.76232 0 10 0ZM10 1.33333C9.11594 1.33333 8.2681 1.68452 7.64298 2.30964C7.01786 2.93477 6.66667 3.78261 6.66667 4.66667C6.66667 5.55072 7.01786 6.39857 7.64298 7.02369C8.2681 7.64881 9.11594 8 10 8C10.8841 8 11.7319 7.64881 12.357 7.02369C12.9821 6.39857 13.3333 5.55072 13.3333 4.66667C13.3333 3.78261 12.9821 2.93477 12.357 2.30964C11.7319 1.68452 10.8841 1.33333 10 1.33333Z"
                                    fill="#9B9CAA"
                                />
                            </svg>
                            <input name="login" type="text" placeholder="Nom d'utilisateur SSH"  />
                        </div>
                        <div class="item">
                            <svg width="19" height="21" viewBox="0 0 19 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 7.167h2c.265 0 .52.1.707.279a.93.93 0 01.293.673v11.429a.93.93 0 01-.293.673 1.026 1.026 0 01-.707.279h-16c-.265 0-.52-.1-.707-.279a.93.93 0 01-.293-.673V8.119a.93.93 0 01.293-.673c.187-.179.442-.28.707-.28h2v-.952c0-1.515.632-2.969 1.757-4.04A6.156 6.156 0 019.5.5c1.591 0 3.117.602 4.243 1.674 1.125 1.071 1.757 2.525 1.757 4.04v.953zM2.5 9.07v9.524h14V9.071h-14zm6 3.81h2v1.905h-2V12.88zm-4 0h2v1.905h-2V12.88zm8 0h2v1.905h-2V12.88zm1-5.714v-.953a3.72 3.72 0 00-1.172-2.693A4.104 4.104 0 009.5 2.405c-1.06 0-2.078.401-2.828 1.116A3.72 3.72 0 005.5 6.214v.953h8z" fill="#9B9CAA"/></svg>
                            <input name="password" type="password" placeholder="Mot de Passe SSH" />
                        </div>
                        <div class="item">
                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.49 14.871a.21.21 0 00-.294 0L9.17 17.897c-1.4 1.401-3.766 1.55-5.313 0-1.55-1.55-1.4-3.911 0-5.312l3.027-3.027a.21.21 0 000-.294L5.847 8.227a.21.21 0 00-.294 0l-3.026 3.027a5.63 5.63 0 000 7.969 5.633 5.633 0 007.969 0l3.026-3.026a.209.209 0 000-.295l-1.031-1.031zm6.735-12.344a5.63 5.63 0 00-7.969 0L8.227 5.553a.21.21 0 000 .294l1.034 1.034c.081.08.214.08.295 0l3.026-3.026c1.401-1.401 3.766-1.55 5.313 0 1.55 1.55 1.4 3.912 0 5.313l-3.027 3.026a.21.21 0 000 .294l1.037 1.037c.08.08.214.08.294 0l3.026-3.027a5.638 5.638 0 000-7.971zm-5.794 4.71a.21.21 0 00-.294 0l-5.9 5.897a.209.209 0 000 .294L8.27 14.46c.08.08.214.08.294 0l5.897-5.897a.21.21 0 000-.294l-1.03-1.031z" fill="#9B9CAA"/></svg>
                            <input name="default_path" type="text" placeholder="Chemin de connexion (/var/www/html)" />
                        </div>
                        <div class="item">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.973 0C11.65 0 10.416.536 9.4 1.552 8.244 2.707 7.716 4.127 7.87 5.645c.116 1.126.63 2.262 1.455 3.278l-5.719 5.72-1.036-1.037c-.505-.507-.923-.086-1.43.42l-.492.518c-.507.504-.925.899-.419 1.404l1.034 1.037-.812.812a1.285 1.285 0 000 1.825 1.29 1.29 0 001.824 0l8.973-8.973c.972.61 2.027.936 3.056.936 1.323 0 2.559-.536 3.575-1.553 1.155-1.154 1.683-2.574 1.528-4.091-.139-1.366-.864-2.74-2.021-3.896C16.075.733 14.509 0 12.973 0zm.05 1.627c1.099 0 2.24.562 3.229 1.553.878.875 1.427 1.894 1.528 2.884.105 1.028-.257 2.008-1.06 2.81-.72.718-1.558 1.084-2.465 1.084-1.1 0-2.239-.563-3.23-1.552-.877-.878-1.426-1.895-1.527-2.884-.104-1.029.256-2.008 1.06-2.81.72-.719 1.559-1.085 2.465-1.085z" fill="#9B9CAA"/></svg>
                            <textarea name="privateKey" placeholder="Clé Privé SSH"></textarea>
                        </div>
                        <div class="item">
                            <svg width="18" height="21" viewBox="0 0 18 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M14.786 7.167h1.905a.952.952 0 01.952.952v11.429a.953.953 0 01-.953.952H1.453a.952.952 0 01-.952-.952V8.119a.952.952 0 01.952-.952h1.905v-.953a5.714 5.714 0 1111.429 0v.953zm-1.905 0v-.953a3.81 3.81 0 00-7.62 0v.953h7.62zM8.119 12.88v1.905h1.905V12.88H8.119zm-3.81 0v1.905h1.905V12.88H4.31zm7.62 0v1.905h1.904V12.88H11.93z" fill="#9B9CAA"/></svg>
                            <input name="passphrase" type="password" placeholder="Passphrase - Clé Privé SSH" />
                        </div>
                        <div class="item">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10 12C15.5227 12 20 14.0893 20 16.6667V20H0V16.6667C0 14.0893 4.47733 12 10 12ZM18.6667 16.6667C18.6667 14.8267 14.7867 13.3333 10 13.3333C5.21333 13.3333 1.33333 14.8267 1.33333 16.6667V18.6667H18.6667V16.6667ZM10 0C11.2377 0 12.4247 0.491666 13.2998 1.36684C14.175 2.24201 14.6667 3.42899 14.6667 4.66667C14.6667 5.90434 14.175 7.09133 13.2998 7.9665C12.4247 8.84167 11.2377 9.33333 10 9.33333C8.76232 9.33333 7.57534 8.84167 6.70017 7.9665C5.825 7.09133 5.33333 5.90434 5.33333 4.66667C5.33333 3.42899 5.825 2.24201 6.70017 1.36684C7.57534 0.491666 8.76232 0 10 0ZM10 1.33333C9.11594 1.33333 8.2681 1.68452 7.64298 2.30964C7.01786 2.93477 6.66667 3.78261 6.66667 4.66667C6.66667 5.55072 7.01786 6.39857 7.64298 7.02369C8.2681 7.64881 9.11594 8 10 8C10.8841 8 11.7319 7.64881 12.357 7.02369C12.9821 6.39857 13.3333 5.55072 13.3333 4.66667C13.3333 3.78261 12.9821 2.93477 12.357 2.30964C11.7319 1.68452 10.8841 1.33333 10 1.33333Z"
                                    fill="#9B9CAA"
                                />
                            </svg>
                            <label for="acceptedRoles_user">
                            	Autoriser la connexion aux utilisateurs ?
                            	<input type="checkbox" id="acceptedRoles_user" name="acceptedRoles_user">
							</label>
                        </div>
                    </form>
                </main>
                `;

                const _this = this;
                Swal.fire({
                    title: "Ajout d'un serveur",
                    html: form,
                    showCancelButton: true,
                    cancelButtonText: 'Annuler',
                    confirmButtonText: 'Valider',
                    showLoaderOnConfirm: true,
                    preConfirm: () => {
                        const domArray = Array.from(document.querySelector('form#credentialsSSH').children).splice(
                            0,
                            9
                        );

                        let name = domArray[0].children[1].value.trim();
                        let adresse = domArray[1].children[1].value.trim();
                        let port = domArray[2].children[1].value.trim();
                        let login = domArray[3].children[1].value.trim();
                        let password = domArray[4].children[1].value.trim();
                        let defaultPath = domArray[5].children[1].value.trim();
                        let privateKey = domArray[6].children[1].value.trim();
                        let passphrase = domArray[7].children[1].value.trim();
                        let acceptedRoles = domArray[8].children[1].firstElementChild.checked
                            ? ['ROLE_USER', 'ROLE_ADMIN']
                            : ['ROLE_ADMIN'];

                        acceptedRoles = JSON.stringify(acceptedRoles);
                        if (!port) port = 22;
                        if (privateKey && !passphrase) passphrase = password;

                        if (name && adresse && port && login && password) {
                            return serverAPI
                                .newServer(
                                    {
                                        name: name,
                                        login: login,
                                        adresse: adresse,
                                        port: port,
                                        password: password,
                                        defaultPath: defaultPath,
                                        privateKey: privateKey,
                                        passphrase: passphrase,
                                        acceptedRoles: acceptedRoles,
                                    },
                                    this.$store.getters.token
                                )
                                .then((result) => {
                                    if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
                                    return result.data;
                                })
                                .catch((err) => console.log(err));
                        }
                        return;
                    },
                    allowOutsideClick: () => false,
                }).then((result) => {
                    if (result.value) {
                        switch (result.value) {
                            case 'server already created.':
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: 'Le serveur existe déjà.',
                                });
                                break;
                            case 'server created.':
                                EventBus.$emit('sendToast', {
                                    type: 'success',
                                    message: 'Serveur bien créé.',
                                });
                                break;
                            default:
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: "Une erreur c'est produite.",
                                });
                                break;
                        }
                        EventBus.$emit('tableServerUpdated');
                    }
                });
            },
            createGridData(servers) {
                this.gridData = [];

                servers.forEach((server) => {
                    this.gridData.push({
                        icone: '-_server_-',
                        name: server.name,
                        adresse: server.adresse,
                        port: server.port,
                        login: server.login,
                        documents: server.documents_count,
                        actions: '-_actions_-',
                    });
                });
            },
            getServers() {
                serverAPI
                    .getAll(this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
                        if (result.status) {
                            this.createGridData(result.data);
                            this.serversLoading = false;
                        }
                    })
                    .catch((err) => {
                        this.serversLoading = false;
                        console.error(err);
                    });
            },
            initEventsListeners() {
                EventBus.$on('tableServerUpdated', () => {
                    this.serversLoading = true;
                    this.getServers();
                });
            },
        },
        mounted() {
            this.getServers();
            this.initEventsListeners();
        },
    };
</script>

<style></style>
