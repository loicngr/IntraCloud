<template>
    <div class="table-grid-container animated fadeIn">
        <table v-if="!isLoading && elements.length !== 0">
            <thead>
                <tr>
                    <th v-for="key in columns" :key="key" :class="{ active: sortKey === key }">
                        <span v-if="key !== 'icone' && key !== 'actions' && key !== 'name'" @click="sortBy(key)">
                            {{ key | upperCase }}
                            <span class="arrow" :class="sortOrders[key] > 0 ? 'asc' : 'dsc'"> </span>
                        </span>
                        <span v-else-if="key === 'name'">
                            {{ 'nom' | upperCase }}
                        </span>
                        <span v-else-if="key === 'actions'">
                            {{ key | upperCase }}
                        </span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(entry, keyEntry) in filteredElements" class="animated fadeIn">
                    <td v-for="(element, keyElement) in entry" :key="element">
                        <span v-if="element === '-_server_-'">
                            <div>
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
                            </div>
                        </span>
                        <span v-else-if="keyElement === 'documents'">
                            <button
                                class="rounded-perso-2"
                                @click="viewDocuments"
                                :data-server_adresse="entry.adresse"
                                :data-server_port="entry.port"
                                :data-server_login="entry.login"
                                :data-server_name="entry.name"
                            >
                                {{ element }} documents
                            </button>
                        </span>
                        <span v-else-if="element === '-_actions_-'">
                            <span v-if="editMod && editedServer.name === entry.name">
                                <button
                                    class="rounded-perso-2"
                                    data-action="validate"
                                    aria-describedby="tooltip"
                                    @mouseenter="show"
                                    @mouseleave="hide"
                                    @click="updateServer"
                                >
                                    <svg width="14" height="12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M4.79 11.44L.396 7.044l2.003-2.003 2.393 2.4L11.784.44l2.004 2.003L4.79 11.44z"
                                            fill="#9B9CAA"
                                        />
                                    </svg>
                                    <div class="tooltip" role="tooltip">
                                        Valider
                                        <div class="arrow" data-popper-arrow></div>
                                    </div>
                                </button>
                                <button
                                    class="rounded-perso-2"
                                    data-action="cancel"
                                    aria-describedby="tooltip"
                                    @mouseenter="show"
                                    @mouseleave="hide"
                                    @click="cancelUpdate"
                                >
                                    <svg width="12" height="12" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M6 0a6 6 0 110 12A6 6 0 016 0zm0 1.2a4.8 4.8 0 00-3.792 7.746l6.738-6.738A4.803 4.803 0 006 1.2zm0 9.6a4.8 4.8 0 003.792-7.746L3.054 9.792A4.803 4.803 0 006 10.8z"
                                            fill="#9B9CAA"
                                        />
                                    </svg>
                                    <div class="tooltip" role="tooltip">
                                        Annuler
                                        <div class="arrow" data-popper-arrow></div>
                                    </div>
                                </button>
                            </span>
                            <span v-else-if="!editMod">
                                <button
                                    class="rounded-perso-2"
                                    data-action="delete"
                                    aria-describedby="tooltip"
                                    @mouseenter="show"
                                    @mouseleave="hide"
                                    @click="deleteServer"
                                    :data-server_adresse="entry.adresse"
                                    :data-server_port="entry.port"
                                    :data-server_login="entry.login"
                                    :data-server_name="entry.name"
                                >
                                    <svg width="10" height="13" fill="none" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M1.167 11.167A1.333 1.333 0 002.5 12.5h5.333a1.334 1.334 0 001.334-1.333v-8h-8v8zm1.64-4.747l.94-.94 1.42 1.413L6.58 5.48l.94.94-1.413 1.413L7.52 9.247l-.94.94-1.413-1.414-1.414 1.414-.94-.94 1.414-1.414-1.42-1.413zM7.5 1.167L6.833.5H3.5l-.667.667H.5V2.5h9.333V1.167H7.5z"
                                            fill="#9B9CAA"
                                        />
                                    </svg>
                                    <div class="tooltip" role="tooltip">
                                        Supprimer
                                        <div class="arrow" data-popper-arrow></div>
                                    </div>
                                </button>
                                <button
                                    class="rounded-perso-2"
                                    data-action="edit"
                                    aria-describedby="tooltip"
                                    :data-server_adresse="entry.adresse"
                                    :data-server_port="entry.port"
                                    :data-server_login="entry.login"
                                    :data-server_name="entry.name"
                                    @mouseenter="show"
                                    @mouseleave="hide"
                                    @click="editServer"
                                >
                                    <svg width="15" height="12" xmlns="http://www.w3.org/2000/svg">
                                        <path
                                            d="M5.25 6a3 3 0 100-6 3 3 0 000 6zm2.1.75h-.392a4.084 4.084 0 01-3.417 0H3.15A3.15 3.15 0 000 9.9v.975C0 11.495.504 12 1.125 12h6.443a1.125 1.125 0 01-.061-.5l.16-1.427.027-.26 1.997-1.997A3.118 3.118 0 007.35 6.75zm1.062 3.405l-.16 1.43a.373.373 0 00.413.412l1.427-.159 3.232-3.232-1.68-1.68-3.232 3.23zm6.424-3.853l-.889-.888a.562.562 0 00-.792 0l-.982.982 1.683 1.68.98-.98a.565.565 0 000-.794z"
                                            fill="#9B9CAA"
                                        />
                                    </svg>
                                    <div class="tooltip" role="tooltip">
                                        Modifier
                                        <div class="arrow" data-popper-arrow></div>
                                    </div>
                                </button>
                            </span>
                        </span>
                        <span v-else>
                            <span v-if="keyElement === 'name'">
                                <span v-if="editMod && editedServer.name === entry.name">
                                    <input
                                        type="text"
                                        id="editServerName"
                                        :value="editedServer.name"
                                        placeholder="Nom du serveur"
                                        style="width: 100%; text-align: center;"
                                    />
                                </span>
                                <span v-else>
                                    {{ element }}
                                </span>
                            </span>
                            <span v-if="keyElement === 'adresse'">
                                <span v-if="editMod && editedServer.name === entry.name">
                                    <input
                                        type="text"
                                        id="editServerAdresseIpt"
                                        :value="editedServer.adresse"
                                        placeholder="Adresse du serveur"
                                        style="width: 100%; text-align: center;"
                                    />
                                </span>
                                <span v-else>
                                    {{ element }}
                                </span>
                            </span>
                            <span v-else-if="keyElement === 'port'">
                                <span v-if="editMod && editedServer.name === entry.name">
                                    <input
                                        type="text"
                                        id="editServerPortIpt"
                                        :value="editedServer.port"
                                        placeholder="Port du serveur"
                                        style="width: 50%; text-align: center;"
                                    />
                                </span>
                                <span v-else>
                                    {{ element }}
                                </span>
                            </span>
                            <span v-else-if="keyElement === 'login'">
                                <span v-if="editMod && editedServer.name === entry.name">
                                    <button class="rounded-perso-2 btn-white" @click="serverUpdateLogin">
                                        Modifier la connexion
                                    </button>
                                </span>
                                <span v-else>
                                    {{ element }}
                                </span>
                            </span>
                        </span>
                    </td>
                </tr>
            </tbody>
        </table>
        <span v-else-if="!isLoading && elements.length === 0">
            Il n'y a aucun serveurs...
        </span>
    </div>
</template>

<script>
    import Swal from 'sweetalert2';
    import { EventBus } from '../EventBus';

    import { createPopper } from '@popperjs/core';

    import { Server } from '@/api/Server.js';

    const serverAPI = new Server();
    export default {
        name: 'ServersTable',
        props: {
            elements: Array,
            columns: Array,
            filterKey: String,
            isLoading: Boolean,
        },
        data() {
            let sortOrders = {};
            this.columns.forEach((key) => {
                if (key === 'documents') sortOrders[key] = -1;
                else sortOrders[key] = 1;
            });
            return {
                sortKey: 'documents',
                sortOrders: sortOrders,
                popperInstance: null,
                previousPopper: null,
                editMod: false,
                editedServer: {
                    node: null,
                    adresse: null,
                    port: null,
                    name: null,
                    login: null,
                    privateKey: null,
                    passphrase: null,
                    password: null,
                },
            };
        },
        computed: {
            filteredElements() {
                let sortKey = this.sortKey;
                let filterKey = this.filterKey && this.filterKey.toLowerCase();
                let order = this.sortOrders[sortKey] || 1;
                let elements = this.elements;
                if (filterKey) {
                    elements = elements.filter((row) => {
                        return Object.keys(row).some((key) => {
                            return String(row[key]).toLowerCase().indexOf(filterKey) > -1;
                        });
                    });
                }
                if (sortKey) {
                    elements = elements.slice().sort((a, b) => {
                        a = a[sortKey];
                        b = b[sortKey];
                        return (a === b ? 0 : a > b ? 1 : -1) * order;
                    });
                }
                return elements;
            },
        },
        filters: {
            capitalize(str) {
                return str.charAt(0).toUpperCase() + str.slice(1);
            },
            upperCase(str) {
                return str.toUpperCase();
            },
        },
        methods: {
            sortBy(key) {
                this.sortKey = key;
                this.sortOrders[key] = this.sortOrders[key] * -1;
            },
            show(evt) {
                const button = evt.target;
                const tooltip = button.children[1];

                tooltip.setAttribute('data-show', '');
                this.create(tooltip, button);
            },
            hide(evt) {
                const button = evt.target;
                const tooltip = button.children[1];

                tooltip.removeAttribute('data-show');
                this.destroy();
            },
            create(tooltip, button) {
                this.popperInstance = createPopper(button, tooltip, {
                    placement: 'top',
                    modifiers: [
                        {
                            name: 'offset',
                            options: {
                                offset: [0, 7],
                            },
                        },
                    ],
                });
            },
            destroy() {
                if (this.popperInstance) {
                    this.popperInstance.destroy();
                    this.popperInstance = null;
                }
            },

            editServer(evt) {
                this.editMod = true;
                const baseParentElement = evt.target;

                /**
                 * Condition pour sélectionner le bon élément dans le DOM
                 * */
                const trElement =
                    baseParentElement.nodeName === 'TR'
                        ? baseParentElement
                        : baseParentElement.parentNode.nodeName === 'TR'
                        ? baseParentElement.parentNode
                        : baseParentElement.parentNode.parentNode.nodeName === 'TR'
                        ? baseParentElement.parentNode.parentNode
                        : baseParentElement.parentNode.parentNode.parentNode.nodeName === 'TR'
                        ? baseParentElement.parentNode.parentNode.parentNode
                        : baseParentElement.parentNode.parentNode.parentNode.parentNode.nodeName === 'TR'
                        ? baseParentElement.parentNode.parentNode.parentNode.parentNode
                        : baseParentElement.parentNode.parentNode.parentNode.parentNode.parentNode.nodeName === 'TR'
                        ? baseParentElement.parentNode.parentNode.parentNode.parentNode.parentNode
                        : baseParentElement.parentNode.parentNode.parentNode.parentNode.parentNode.parentNode;

                const buttonElement =
                    baseParentElement.nodeName === 'BUTTON'
                        ? baseParentElement
                        : baseParentElement.parentNode.nodeName === 'BUTTON'
                        ? baseParentElement.parentNode
                        : baseParentElement.parentNode.parentNode;

                const serverAdresse = buttonElement.dataset.server_adresse;
                const serverPort = buttonElement.dataset.server_port;
                const serverLogin = buttonElement.dataset.server_login;
                const serverName = buttonElement.dataset.server_name;

                this.editedServer.node = trElement;
                this.editedServer.adresse = serverAdresse;
                this.editedServer.port = serverPort;
                this.editedServer.login = serverLogin;
                this.editedServer.name = serverName;

                this.editedServer.node.setAttribute('data-edit', '');
            },
            updateServer() {
                const adresse = document.getElementById('editServerAdresseIpt').value.trim();
                const port = document.getElementById('editServerPortIpt').value.trim();
                const name = document.getElementById('editServerName').value.trim();

                if (
                    name !== this.editedServer.name ||
                    adresse !== this.editedServer.adresse ||
                    port !== this.editedServer.port
                ) {
                    EventBus.$emit('loaderRequest', true);
                    this.getServerByName(this.editedServer.name).then((server) => {
                        if (server) {
                            serverAPI
                                .update(
                                    server.id,
                                    {
                                        adresse: adresse !== this.editedServer.adresse ? adresse : '',
                                        port: port !== this.editedServer.port ? port : '',
                                        name: name !== this.editedServer.name ? name : '',
                                    },
                                    this.$store.getters.token
                                )
                                .then((result) => {
                                    if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
                                    if (!result.status) {
                                        EventBus.$emit('sendToast', {
                                            type: 'error',
                                            message: 'Modification impossible.',
                                        });
                                        throw new Error(result.data);
                                    }

                                    EventBus.$emit('loaderRequest', false);
                                    EventBus.$emit('sendToast', {
                                        type: 'success',
                                        message: 'Serveur à jour.',
                                    });
                                    this.cancelUpdate();
                                    EventBus.$emit('tableServerUpdated');
                                })
                                .catch((err) => {
                                    console.log(err);
                                    EventBus.$emit('loaderRequest', false);
                                });
                        } else {
                            EventBus.$emit('loaderRequest', false);
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Serveur non mise à jour.',
                            });
                        }
                    });
                }
            },
            async serverUpdateLogin() {
                const server = await this.getServerByName(this.editedServer.name);
                if (!server) {
                    EventBus.$emit('sendToast', {
                        type: 'error',
                        message: 'Serveur introuvable.',
                    });
                    throw new Error('Server not found');
                }

                const btnsIf =
                    server.privateKey && server.passphrase
                        ? `
                    <div class="item-btns">
                        <button type="button" class="rounded-perso-1" data-action="deletePrivateKey" data-server_id="${server.id}"> Supprimer la Clé Privé </button>
                        <button type="button" class="rounded-perso-1" data-action="deletePassphrase" data-server_id="${server.id}"> Supprimer la Passphrase </button>
                    </div>`
                        : server.privateKey && !server.passphrase
                        ? `
                    <div class="item-btns">
                        <button type="button" class="rounded-perso-1" data-action="deletePrivateKey" data-server_id="${server.id}"> Supprimer la Clé Privé </button>
                    </div>`
                        : !server.privateKey && server.passphrase
                        ? `
                    <div class="item-btns">
                        <button type="button" class="rounded-perso-1" data-action="deletePassphrase" data-server_id="${server.id}"> Supprimer la Passphrase </button>
                    </div>`
                        : '';
                const form = `
                <main class="container">
                    <form id="credentialsSSH">
                        <div class="item">
                            <svg width="20" height="20" viewBox="0 0 20 20" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path
                                    d="M10 12C15.5227 12 20 14.0893 20 16.6667V20H0V16.6667C0 14.0893 4.47733 12 10 12ZM18.6667 16.6667C18.6667 14.8267 14.7867 13.3333 10 13.3333C5.21333 13.3333 1.33333 14.8267 1.33333 16.6667V18.6667H18.6667V16.6667ZM10 0C11.2377 0 12.4247 0.491666 13.2998 1.36684C14.175 2.24201 14.6667 3.42899 14.6667 4.66667C14.6667 5.90434 14.175 7.09133 13.2998 7.9665C12.4247 8.84167 11.2377 9.33333 10 9.33333C8.76232 9.33333 7.57534 8.84167 6.70017 7.9665C5.825 7.09133 5.33333 5.90434 5.33333 4.66667C5.33333 3.42899 5.825 2.24201 6.70017 1.36684C7.57534 0.491666 8.76232 0 10 0ZM10 1.33333C9.11594 1.33333 8.2681 1.68452 7.64298 2.30964C7.01786 2.93477 6.66667 3.78261 6.66667 4.66667C6.66667 5.55072 7.01786 6.39857 7.64298 7.02369C8.2681 7.64881 9.11594 8 10 8C10.8841 8 11.7319 7.64881 12.357 7.02369C12.9821 6.39857 13.3333 5.55072 13.3333 4.66667C13.3333 3.78261 12.9821 2.93477 12.357 2.30964C11.7319 1.68452 10.8841 1.33333 10 1.33333Z"
                                    fill="#9B9CAA"
                                />
                            </svg>
                            <input name="login" type="text" placeholder="Nom d'utilisateur SSH (${
                                this.editedServer.login
                            })"  />
                        </div>
                        <div class="item">
                            <svg width="19" height="21" viewBox="0 0 19 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M15.5 7.167h2c.265 0 .52.1.707.279a.93.93 0 01.293.673v11.429a.93.93 0 01-.293.673 1.026 1.026 0 01-.707.279h-16c-.265 0-.52-.1-.707-.279a.93.93 0 01-.293-.673V8.119a.93.93 0 01.293-.673c.187-.179.442-.28.707-.28h2v-.952c0-1.515.632-2.969 1.757-4.04A6.156 6.156 0 019.5.5c1.591 0 3.117.602 4.243 1.674 1.125 1.071 1.757 2.525 1.757 4.04v.953zM2.5 9.07v9.524h14V9.071h-14zm6 3.81h2v1.905h-2V12.88zm-4 0h2v1.905h-2V12.88zm8 0h2v1.905h-2V12.88zm1-5.714v-.953a3.72 3.72 0 00-1.172-2.693A4.104 4.104 0 009.5 2.405c-1.06 0-2.078.401-2.828 1.116A3.72 3.72 0 005.5 6.214v.953h8z" fill="#9B9CAA"/></svg>
                            <input name="password" type="password" placeholder="Mot de Passe SSH" />
                        </div>
                        <div class="item">
                            <svg width="21" height="21" viewBox="0 0 21 21" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M12.49 14.871a.21.21 0 00-.294 0L9.17 17.897c-1.4 1.401-3.766 1.55-5.313 0-1.55-1.55-1.4-3.911 0-5.312l3.027-3.027a.21.21 0 000-.294L5.847 8.227a.21.21 0 00-.294 0l-3.026 3.027a5.63 5.63 0 000 7.969 5.633 5.633 0 007.969 0l3.026-3.026a.209.209 0 000-.295l-1.031-1.031zm6.735-12.344a5.63 5.63 0 00-7.969 0L8.227 5.553a.21.21 0 000 .294l1.034 1.034c.081.08.214.08.295 0l3.026-3.026c1.401-1.401 3.766-1.55 5.313 0 1.55 1.55 1.4 3.912 0 5.313l-3.027 3.026a.21.21 0 000 .294l1.037 1.037c.08.08.214.08.294 0l3.026-3.027a5.638 5.638 0 000-7.971zm-5.794 4.71a.21.21 0 00-.294 0l-5.9 5.897a.209.209 0 000 .294L8.27 14.46c.08.08.214.08.294 0l5.897-5.897a.21.21 0 000-.294l-1.03-1.031z" fill="#9B9CAA"/></svg>
                            <input name="default_path" type="text" placeholder="Chemin de connexion (${
                                server && server.default_path ? server.default_path : '/var/www/html'
                            })" />
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
                        ${btnsIf}
                    </form>
                </main>
                `;

                const _this = this;
                Swal.fire({
                    title: 'Modifier la connexion',
                    html: form,
                    showCancelButton: true,
                    cancelButtonText: 'Annuler',
                    confirmButtonText: 'Valider',
                    showLoaderOnConfirm: true,
                    onRender(popup) {
                        const btnDeletePrivateKey = document.querySelector('button[data-action="deletePrivateKey"]');
                        const btnDeletePassphrase = document.querySelector('button[data-action="deletePassphrase"]');
                        const inputAcceptedRoles = document.getElementById('acceptedRoles_user');

                        if (server.accepted_roles.indexOf('ROLE_USER') !== -1)
                            inputAcceptedRoles.toggleAttribute('checked');

                        if (btnDeletePrivateKey) btnDeletePrivateKey.addEventListener('click', _this.deletePrivateKey);
                        if (btnDeletePassphrase) btnDeletePassphrase.addEventListener('click', _this.deletePassphrase);
                    },
                    preConfirm: () => {
                        const domArray = Array.from(document.querySelector('form#credentialsSSH').children).splice(
                            0,
                            6
                        );

                        let login = domArray[0].children[1].value.trim();
                        let password = domArray[1].children[1].value.trim();
                        let defaultPath = domArray[2].children[1].value.trim();
                        let privateKey = domArray[3].children[1].value.trim();
                        let passphrase = domArray[4].children[1].value.trim();
                        let acceptedRoles = domArray[5].children[1].firstElementChild.checked
                            ? ['ROLE_USER', 'ROLE_ADMIN']
                            : ['ROLE_ADMIN'];

                        if (
                            (login && login.length !== 0) ||
                            (password && password.length !== 0) ||
                            (privateKey && privateKey.length !== 0) ||
                            (defaultPath && defaultPath.length !== 0) ||
                            (passphrase && passphrase.length !== 0) ||
                            acceptedRoles.length !== server.accepted_roles.length
                        ) {
                            acceptedRoles = JSON.stringify(acceptedRoles);
                            return serverAPI
                                .update(
                                    server.id,
                                    { login, password, defaultPath, privateKey, passphrase, acceptedRoles },
                                    this.$store.getters.token
                                )
                                .then((result) => {
                                    if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                                    if (result.status) return result.data;
                                    else {
                                        EventBus.$emit('sendToast', {
                                            type: 'error',
                                            message: result.data,
                                        });
                                    }
                                })
                                .catch((err) => console.error(err));
                        }
                        return;
                    },
                    allowOutsideClick: () => false,
                }).then((result) => {
                    if (result.value) {
                        switch (result.value) {
                            case 'server updated.':
                                EventBus.$emit('sendToast', {
                                    type: 'success',
                                    message: 'Serveur à jour.',
                                });
                                this.cancelUpdate();
                                EventBus.$emit('tableServerUpdated');
                                break;
                            default:
                                break;
                        }
                    }
                });
            },
            deleteServer(evt) {
                Swal.fire({
                    title: 'Êtes-vous sûr de vouloir supprimer le serveur ?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    cancelButtonText: 'Non',
                    confirmButtonText: 'Oui',
                }).then((result) => {
                    if (result.value) {
                        EventBus.$emit('loaderRequest', true);
                        const baseParentElement = evt.target;

                        const buttonElement =
                            baseParentElement.nodeName === 'BUTTON'
                                ? baseParentElement
                                : baseParentElement.parentNode.nodeName === 'BUTTON'
                                ? baseParentElement.parentNode
                                : baseParentElement.parentNode.parentNode;

                        const serverAdresse = buttonElement.dataset.server_adresse;
                        const serverPort = buttonElement.dataset.server_port;
                        const serverLogin = buttonElement.dataset.server_login;
                        const serverName = buttonElement.dataset.server_name;

                        this.getServerByName(serverName).then((server) => {
                            if (server) {
                                serverAPI.deleteServer(server.id, this.$store.getters.token).then((result) => {
                                    if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                                    if (result.status) {
                                        EventBus.$emit('loaderRequest', false);
                                        EventBus.$emit('sendToast', {
                                            type: 'success',
                                            message: 'Serveur bien supprimé.',
                                        });
                                        EventBus.$emit('tableServerUpdated');
                                    } else {
                                        EventBus.$emit('loaderRequest', false);
                                        EventBus.$emit('sendToast', {
                                            type: 'error',
                                            message: result.data,
                                        });
                                    }
                                });
                            }
                        });
                    }
                });
            },
            deletePrivateKey(evt) {
                const server_id = parseInt(evt.target.dataset.server_id);
                EventBus.$emit('loaderRequest', true);

                const btnConfirm = Swal.getConfirmButton();
                const btnClose = Swal.getCancelButton();
                btnConfirm.style.pointerEvents = 'none';

                serverAPI
                    .deletePrivateKey(server_id, this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null) {
                            EventBus.$emit('expiredJWT', result.tokens);
                        }
                        if (result.status) {
                            EventBus.$emit('loaderRequest', false);
                            btnClose.click();
                            EventBus.$emit('sendToast', {
                                type: 'success',
                                message: 'Clé privé bien supprimé.',
                            });
                            this.cancelUpdate();
                            EventBus.$emit('tableServerUpdated');
                        } else {
                            EventBus.$emit('loaderRequest', false);
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Clé privé non supprimé.',
                            });
                            btnConfirm.style.pointerEvents = 'auto';
                        }
                    })
                    .catch((err) => {
                        EventBus.$emit('loaderRequest', false);
                        EventBus.$emit('sendToast', {
                            type: 'error',
                            message: err,
                        });
                    });
            },
            deletePassphrase(evt) {
                const server_id = parseInt(evt.target.dataset.server_id);
                EventBus.$emit('loaderRequest', true);

                const btnConfirm = Swal.getConfirmButton();
                const btnClose = Swal.getCancelButton();
                btnConfirm.style.pointerEvents = 'none';

                serverAPI
                    .deletePassphrase(server_id, this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null) {
                            EventBus.$emit('expiredJWT', result.tokens);
                        }
                        if (result.status) {
                            EventBus.$emit('loaderRequest', false);
                            btnClose.click();
                            EventBus.$emit('sendToast', {
                                type: 'success',
                                message: 'Passphrase bien supprimé.',
                            });
                            this.cancelUpdate();
                            EventBus.$emit('tableServerUpdated');
                        } else {
                            EventBus.$emit('loaderRequest', false);
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Passphrase non supprimé.',
                            });
                            btnConfirm.style.pointerEvents = 'auto';
                        }
                    })
                    .catch((err) => {
                        EventBus.$emit('loaderRequest', false);
                        EventBus.$emit('sendToast', {
                            type: 'error',
                            message: err,
                        });
                    });
            },
            cancelUpdate() {
                this.editedServer.adresse = null;
                this.editedServer.node.removeAttribute('data-edit');
                this.editedServer.port = null;
                this.editedServer.name = null;
                this.editedServer.login = null;
                this.editMod = false;
            },

            async getServerByName(name) {
                const result = await serverAPI.getOneByName(name, this.$store.getters.token);
                if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
                return !result.status ? false : result.data;
            },
            async getDocumentsByServerID(id_server) {
                const result = await serverAPI.getDocuments(id_server, this.$store.getters.token);
                if (result.tokens !== null) {
                    EventBus.$emit('expiredJWT', result.tokens);
                }
                if (result.status) {
                    return result.data;
                }
                return false;
            },

            async viewDocuments(evt) {
                const name = evt.target.dataset.server_name;

                EventBus.$emit('loaderRequest', true);

                const server = await this.getServerByName(name);
                if (server) {
                    const documents = await this.getDocumentsByServerID(server.id);
                    if (documents) {
                        EventBus.$emit('loaderRequest', false);

                        if (documents.length === 0) {
                            return;
                        }

                        this.$store.dispatch('setDocuments', { documents, server }).then(() => {
                            this.$router.push({ name: 'AdminDocuments' });
                        });
                    } else {
                        EventBus.$emit('sendToast', {
                            type: 'error',
                            message: 'Impossible de récupérer les documents.',
                        });
                        EventBus.$emit('loaderRequest', false);
                    }
                } else {
                    EventBus.$emit('sendToast', {
                        type: 'error',
                        message: 'Impossible de récupérer le serveur.',
                    });
                    EventBus.$emit('loaderRequest', false);
                }
            },
        },
    };
</script>

<style lang="scss" scoped>
    @import '../../styles/Table';
    @import '../../styles/SelectInput';

    .tooltip {
        background: #333;
        color: white;

        font-weight: bold;
        padding: 4px 8px;
        font-size: 13px;
        border-radius: 4px;

        word-break: normal !important;

        display: none;

        z-index: 5;

        &[data-show] {
            display: block;
        }
        .arrow {
            position: absolute;
            width: 8px;
            height: 8px;
            z-index: -1;
            bottom: -4px;
            left: -4px !important;

            &::before {
                position: absolute;
                width: 8px;
                height: 8px;
                z-index: -1;

                content: '';
                transform: rotate(45deg);
                background: #333;
            }
        }
    }
    .table-grid-container {
        table {
            tbody {
                tr {
                    &[data-edit] {
                        background-color: rgba(192, 192, 192, 0.377);
                    }

                    td:nth-child(1) {
                        padding-left: 10px !important;
                    }
                    td:nth-child(2) {
                        width: 170px !important;
                        padding-left: 8px !important;
                    }

                    td:nth-child(3) {
                        max-width: 100% !important;

                        padding: 0 !important;
                    }
                    td:nth-child(4),
                    td:nth-child(5),
                    td:nth-child(6),
                    td:nth-child(7) {
                        padding: 0 !important;
                    }

                    td:nth-child(6),
                    td:nth-child(7) {
                        button {
                            height: 20px;
                            width: auto;

                            margin: 0 2.5px;

                            font-weight: bold;
                            color: var(--color-text-blue);

                            border: none;
                            position: relative;

                            background-color: var(--color-main-white);
                            transition: all 0.2s ease-in-out;
                        }
                    }
                    td:nth-child(7) {
                        button {
                            height: 20px;
                            width: 40px;

                            svg {
                                padding: 2px 0 0 0;
                                path {
                                    fill: var(--color-text-blue);
                                    opacity: 0.8;
                                }
                            }

                            &:hover {
                                transform: scale(1.05);
                                cursor: pointer;
                            }

                            &.btn-disabled {
                                &:hover {
                                    cursor: not-allowed;
                                    transform: none;
                                    background-color: rgba(192, 192, 192, 0.42) !important;
                                }
                                background-color: rgba(192, 192, 192, 0.42);
                            }

                            &[data-action='delete'],
                            &[data-action='cancel'] {
                                &:hover {
                                    background-color: var(--color-main-red);
                                }
                            }
                            &[data-action='edit'] {
                                &:hover {
                                    background-color: var(--color-main-blue-07);
                                }
                            }
                            &[data-action='validate'] {
                                &:hover {
                                    background-color: var(--color-main-green-05);
                                }
                            }
                        }
                    }
                }
            }
        }
    }
</style>
