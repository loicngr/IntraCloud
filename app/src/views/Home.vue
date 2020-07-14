<template>
    <div id="page-container">
        <Modal v-if="isModalVisible" @close="closeModal" :content="modalContent" />
        <Notifications v-if="ws_notification.status">
            <template v-slot:title>
                {{ ws_notification.title }}
            </template>
            <template v-slot:message>
                {{ ws_notification.message }}
            </template>
            <template v-slot:buttons>
                <button @click="ws_notification.action.click">
                    {{ ws_notification.action.text }}
                </button>
            </template>
        </Notifications>
        <QueueViewer
            v-if="uploadQueue !== null && uploadQueue >= 1"
            :currentElement="uploadQueue"
            :queueMaxElements="uploadMaxQueue"
        />
        <loading
            :active.sync="loaderContainer"
            :is-full-page="fullPage"
            :z-index="9999"
            :opacity="0.3"
            transition="fade"
        ></loading>

        <div class="page-item" @dragover.prevent @drop.stop.prevent="onDrop">
            <ServersSelect
                :isSelectLoading="serversLoading"
                :selectServersData="serversData"
                :selectedServerID="serverSTUP_id"
                @selectServer="onClickServer"
            />

            <div v-if="currentServer !== null && !serverLoading">
                <div class="bloc-item">
                    <label for="ipt_location">Emplacement</label>
                    <input
                        class="rounded-perso-1"
                        id="ipt_location"
                        type="text"
                        @input="updateStyleWidth"
                        @mouseover="updateStyleWidth"
                        @keypress="locationInputPressed"
                        :value="currentServer.location"
                        v-if="isAdmin"
                    />
                    <input
                        class="rounded-perso-1"
                        id="ipt_location_user"
                        type="text"
                        @mouseover="updateStyleWidth"
                        :value="currentServer.location"
                        v-else-if="!isAdmin"
                        readonly
                    />
                </div>



                <div class="bloc-item">
                    <label for="slc_switch_location">Se déplacer vers</label>
                    <select
                        class="rounded-perso-1"
                        name="slc_switch_location"
                        id="slc_switch_location"
                        @change="onClickLocation"
                    >
                        <option value="" selected>...</option>
                        <option :value="currentServer.home">{{ currentServer.home }}</option>
                        <option v-if="isAdmin" value="/">Racine serveur</option>
                        <option value="goBack">Retour arrière</option>
                    </select>
                </div>

                <div class="bloc-item" v-if="gridData && gridData.length >= 1">
                    <label for="ipt_search">Rechercher</label>
                    <input
                        id="ipt_search"
                        name="query"
                        v-model="searchQuery"
                        class="rounded-perso-1"
                        placeholder=" Rechercher un élément"
                    />
                </div>

                <div class="bloc-item" v-if="websocket !== null">
                    <label id="full">Utilisateur(s) connecté(s)</label>
                    <a @click="viewConnectedUsers">{{ ws_userConnected.length }}</a>
                </div>
            </div>

            <TableNav
                v-if="currentServer !== null && !serverLoading"
                @uploadFile="uploadFile"
                @newSubFolder="newSubFolder(currentServer.location)"
                @selectFilter="onChangeServerFilter($event)"
                @selectDirectoryLocally="selectDirectoryLocally"
                @toggleTableMode="serverToggleMode = $event"
            />

            <div id="serverAndDirectoryPreview">
                <LocalDirectoryPreview :bus="childBus" />
                <TableHome
                    v-if="currentServer !== null && !serverLoading"
                    :elements="gridData"
                    :columns="gridColumns"
                    :filter-key="searchQuery"
                    :isLoading="serverLoading"
                    :usersEditFiles="ws_usersEditFiles"
                    :toggleMode="serverToggleMode"
                    :bus="childBus"
                />
            </div>
        </div>
    </div>
</template>

<script>
    import Swal from 'sweetalert2';

    import { EventBus } from '../components/EventBus.js';

    import Loading from 'vue-loading-overlay';
    import 'vue-loading-overlay/dist/vue-loading.css';

    import { csv as d3CSV } from 'd3';
    import { Base64 } from 'js-base64';
    import { formatDate, filterUnixLS } from '../utils/functions';

    import { Ssh } from '../api/Ssh.js';
    import { Sftp } from '../api/Sftp.js';
    import { Document } from '../api/Document.js';
    import { Server } from '../api/Server.js';

    import LocalDirectoryPreview from '../components/Home/LocalDirectoryPreview';
    import ServersSelect from '../components/Home/ServersSelect';
    import queueViewer from '../components/Home/queueViewer';
    import Notifications from '../components/Notifications';
    import TableNav from '../components/Home/TableNav';
    import TableHome from '../components/Home/Table';
    import modal from '../components/modal';
    import Vue from 'vue';

    const serverAPI = new Server();
    const sshAPI = new Ssh();
    const sftpAPI = new Sftp();
    const documentAPI = new Document();

    export default {
        name: 'Home',
        components: {
            TableHome,
            ServersSelect,
            Loading,
            QueueViewer: queueViewer,
            Notifications,
            TableNav,
            Modal: modal,
            LocalDirectoryPreview,
        },
        data() {
            return {
                loaderContainer: false,
                fullPage: true,
                serversLoading: true,
                serversData: [],
                childBus: new Vue(),
                currentServer: null,
                serverLoading: false,
                serverToggleMode: 2,
                uploadQueue: null,
                uploadMaxQueue: null,
                searchQuery: '',
                gridColumns: ['type', 'name', 'date', 'size'],
                gridData: [],
                serverSTUP_id: -1,
                directoryLocalHandler: null,
                backServerFolderKey: false,
                isModalVisible: false,
                modalContent: null,
                ws_userConnected: [],
                ws_usersEditFiles: [],
                ws_onMessageEvent: false,
                created_socketIT: null,
				moveElementServer_selectedFolder: null,
				moveElementServer_selectedFolderPrevious: null,
                ws_notification: {
                    status: false,
                    title: null,
                    message: null,
                    action: {
                        click: null,
                        text: null,
                    },
                },
            };
        },
        computed: {
            websocket() {
                /**
                 * Retourne l'instance du WebSocket
                 * */
                return this.$store.getters.socket;
            },
            isAdmin() {
                /**
                 * Retourne si l'utilisateur est un Administrateur ou pas
                 * */
                return this.$store.getters.isAdmin;
            },
        },
        watch: {
            websocket(newValue) {
                if (newValue) this.ws_initEvent(newValue);
            },
        },
        methods: {
            selectDirectoryLocally() {
                this.childBus.$emit('openPreviewDirectorySelector');
            },
            /**
             * Mise à jour automatique de la longueur d'un input via son contenu
             * */
            updateStyleWidth(evt) {
                const { value } = evt.target;

                if (value.length < 20) {
                    evt.target.style.width = '250px';
                } else if (value.length >= 20 && value.length < 30) {
                    evt.target.style.width = '350px';
                } else if (value.length >= 30 && value.length < 40) {
                    evt.target.style.width = '450px';
                } else if (value.length >= 40 && value.length < 50) {
                    evt.target.style.width = '550px';
                } else if (value.length >= 50 && value.length < 60) {
                    evt.target.style.width = '650px';
                } else if (value.length >= 60 && value.length < 70) {
                    evt.target.style.width = '750px';
                }
            },
            /**
             * Quand la touche "Entrer" est appuyé dans l'input de l'emplacement serveur
             * */
            locationInputPressed(evt) {
                const { code } = evt;
                const { value } = evt.target;

                if (code === 'Enter' && this.isAdmin) {
                    this.goFolderServer(value);
                }
            },

            /**
             * Modal
             */
            showModal({ title, html, footer }) {
                this.isModalVisible = true;
                this.modalContent = { title, html, footer };
            },
            closeModal() {
                this.isModalVisible = false;
            },

            onChangeServerFilter(optionValue) {
                // Envoi d'un évènement pour le composant enfant
                this.childBus.$emit('sortKey', optionValue);
            },

            /**
             * Fonctions pour upload des éléments
             * */
            uploadFile(evt) {
                /**
                 * Si l'evennement provient du bouton, ou de l'input
                 * */
                const inputElement = evt.target;
                switch (inputElement.nodeName) {
                    case 'BUTTON':
                        // On simule un clique sur l'input pour uploader des fichiers
                        inputElement.children[0].click();
                        break;
                    default:
                        inputElement.addEventListener('change', this.uploadFileEvent_OnChange.bind(this));
                        break;
                }
            },
            uploadFileEvent_OnChange(evt) {
                const files = evt.target.files;
                this.uploadQueue = files.length;
                this.uploadMaxQueue = files.length;
                this.loaderContainer = true;
                for (let file of files) {
                    this.uploadFileEvent_OnFileContent(file);
                }
            },
            uploadFileEvent_OnFileContent(blob) {
                const blobName = blob.name.replace(new RegExp(' ', 'g'), '_');
                documentAPI
                    .newDocument(
                        this.$store.getters.token,
                        blobName,
                        this.currentServer.location,
                        blob.size,
                        this.$store.getters.user.id,
                        this.currentServer.id,
                        blob
                    )
                    .then((result) => {
                        this.uploadQueue--;
                        if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                        if (result.status) {
                            if (this.uploadQueue === 0) {
                                this.loaderContainer = false;
                                this.goFolderServer(this.currentServer.location);
                                this.postedNewElementServer(this.currentServer.location);
                                EventBus.$emit('sendToast', {
                                    type: 'success',
                                    message: `Fichier(s) bien envoyé(s).`,
                                    timer: 5000,
                                });
                                this.uploadQueue = null;
                                this.uploadMaxQueue = null;
                            }
                        } else {
                            this.uploadQueue = null;
                            this.uploadMaxQueue = null;
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: result.data,
                                timer: 5000,
                            });
                            throw new Error(result.data);
                        }
                    });
            },

            /**
             * Quand un fichier est dropé sur le tableau
             * */
            onDrop(evt) {
                const files = evt.dataTransfer.files;
                evt.preventDefault();

                if (files && files.length !== 0) {
                    this.uploadFileEvent_OnChange({
                        target: {
                            files: files,
                        },
                    });
                }
            },

            /**
             * Fonctions pour gérer le WebSocket de la connexion SHH entre les utilisateurs
             * */
            ws_OnMessage(e) {
                const result = JSON.parse(e.data);
                const user = this.$store.getters.user;

                if (result) {
                    const socket = this.$store.getters.socket;
                    switch (result.type) {
                        case 'close':
                            this.ws_removeUser(result.email, 'ssh');
                            break;
                        case 'close_edit_file':
                            this.ws_removeUser(result.email, 'edit_file');
                            break;
                        case 'pong_ssh':
                            if (this.currentServer !== null) {
                                // Si l'utilisateur est bien connecté dans un serveur
                                if (this.currentServer.name === result.server.name) {
                                    // Si l'utilisateur est connecté dans le même serveur
                                    if (this.currentServer.location === result.server.location) {
                                        this.ws_addUser(result.email, 'ssh');
                                    } else {
                                        this.ws_removeUser(result.email, 'ssh');
                                    }
                                }
                            }
                            break;
                        case 'ping_ssh':
                            if (this.currentServer !== null) {
                                // Si l'utilisateur est bien connecté dans un serveur
                                if (this.currentServer.name === result.server.name) {
                                    // Si l'utilisateur est connecté dans le même serveur
                                    if (this.currentServer.location === result.server.location) {
                                        // Si il est déjà dans notre tableau mais pas dans le même dossier
                                        this.ws_addUser(result.email, 'ssh');

                                        // Si l'utilisateur a une connexion pas terrible, on attend que le buffer se vide
                                        const interval = setInterval(() => {
                                            if (socket.bufferedAmount === 0) {
                                                socket.send(
                                                    JSON.stringify({
                                                        type: 'pong_ssh',
                                                        email: user.email,
                                                        server: this.currentServer,
                                                    })
                                                );
                                                clearInterval(interval);
                                            }
                                        }, 100);
                                    } else {
                                        this.ws_removeUser(result.email, 'ssh');
                                    }
                                }
                            }
                            break;
                        case 'refresh_ssh': {
                            if (this.currentServer !== null && this.currentServer.name === result.server.name) {
                                if (
                                    this.currentServer.location === result.server.location &&
                                    result.email !== user.email
                                ) {
                                    this.ws_sendNotification('refresh');
                                }
                            }
                            break;
                        }
                        case 'ping_edit_file':
                            if (this.currentServer !== null && this.currentServer.name === result.server.name) {
                                // Même serveur
                                if (this.currentServer.location === result.server.location) {
                                    // Même dossier
                                    this.ws_addUser(result.email, 'edit_file', result.filename);
                                }
                            }
                            break;
                        case 'pong_edit_file':
                            if (this.currentServer !== null && this.currentServer.name === result.server.name) {
                                // Même serveur
                                if (this.currentServer.location === result.server.location) {
                                    // Même dossier
                                    this.ws_addUser(result.email, 'edit_file', result.filename);
                                } else {
                                    this.ws_removeUser(result.email, 'edit_file');
                                }
                            }
                            break;
                        default:
                            break;
                    }
                }
            },
            ws_initEvent(socket) {
                if (this.ws_onMessageEvent) return;
                socket.addEventListener('message', this.ws_OnMessage);
            },
            ws_makePing() {
                const socket = this.$store.getters.socket || null;
                // Si le socket est bien créé
                if (socket) {
                    const user = this.$store.getters.user;
                    /**
                     * Qui est dans le même dossier ?
                     * Qui édite un fichier dans le même dossier ?
                     * */
                    socket.send(
                        JSON.stringify({
                            type: 'ping_ssh',
                            email: user.email,
                            server: this.currentServer,
                        })
                    );
                    // On s'ajoute nous même dans le tableau
                    this.ws_addUser(user.email, 'ssh');
                }
            },
            ws_goFolder() {
                this.ws_userConnected = [];
                this.ws_usersEditFiles = [];
                this.ws_makePing();
            },
            ws_removeUser(email, type = '') {
                /**
                 * Fonction qui supprime des utilisateurs dans les tableaux
                 *
                 * ssh          = supprime un utilisateur dans le tableau qui affiche les -
                 *                  - utilisateur connectés a un dossier du serveur.
                 * edit_file    = supprime un utilisateur dans le tableau qui affiche les -
                 *                  - utilisateurs en train d'éditer des fichiers.
                 * */
                switch (type) {
                    case 'ssh':
                        if (this.ws_userConnected.length !== 0) {
                            const indexUser = this.ws_userConnected.indexOf(email);
                            if (indexUser !== -1) this.ws_userConnected.splice(indexUser, 1);
                        }
                        break;
                    case 'edit_file':
                        const userLength = this.ws_usersEditFiles.length;

                        // La boucle vérifie si le même utilisateur est déjà dans le tableau
                        for (let i = 0; i < userLength; i++) {
                            if (this.ws_usersEditFiles[i].email === email) {
                                // Si oui, on le supprime
                                this.ws_usersEditFiles.splice(i, 1);
                                break;
                            }
                        }
                        break;
                    default:
                        break;
                }
            },
            ws_addUser(email, type = '', filename = null) {
                /**
                 * Fonction qui ajoute des utilisateurs dans les tableaux
                 *
                 * ssh          = ajoute un utilisateur dans le tableau qui affiche les -
                 *                  - utilisateur connectés a un dossier du serveur.
                 * edit_file    = ajoute un utilisateur dans le tableau qui affiche les -
                 *                  - utilisateurs en train d'éditer des fichiers.
                 * */
                switch (type) {
                    case 'ssh':
                        if (this.ws_userConnected.indexOf(email) === -1) this.ws_userConnected.push(email);
                        break;
                    case 'edit_file':
                        // occ = Occurrences
                        let occ = false;
                        const userLength = this.ws_usersEditFiles.length;

                        // La boucle vérifie si le même utilisateur est déjà dans le tableau
                        for (let i = 0; i < userLength; i++) {
                            if (this.ws_usersEditFiles[i].email === email) {
                                occ = true;
                                break;
                            }
                        }
                        // Si non, on l'ajoute
                        if (!occ) this.ws_usersEditFiles.push({ email: email, filename: filename });
                        break;
                    default:
                        break;
                }
            },
            ws_disconnectMe() {
                const socket = this.$store.getters.socket || null;
                const user = this.$store.getters.user;

                /**
                 * Si on trouve le socket
                 * et
                 * qu'il soit encore ouvert
                 * */
                if (socket && socket.readyState === 1) {
                    socket.send(
                        JSON.stringify({
                            type: 'close',
                            email: user.email,
                        })
                    );
                }
            },
            ws_refreshLocation() {
                this.goFolderServer(this.currentServer.location !== null ? this.currentServer.location : 'goBack');
                this.ws_clearNotification();
            },
            ws_clearNotification() {
                this.ws_notification.status = false;
                this.ws_notification.title = null;
                this.ws_notification.message = null;
                this.ws_notification.action.click = null;
                this.ws_notification.action.text = null;
            },
            ws_sendNotification(type) {
                switch (type) {
                    case 'refresh': {
                        this.ws_notification.status = true;
                        this.ws_notification.title = `Modification dans le serveur : ${this.currentServer.name}`;
                        this.ws_notification.message =
                            'Une modification a eu lieu dans le même dossier que le vôtre, voulez-vous actualiser le dossier ? ';
                        this.ws_notification.action.click = this.ws_refreshLocation;
                        this.ws_notification.action.text = `Actualiser le dossier "${this.getFolderNameServer(
                            this.currentServer.location
                        )}"`;
                        break;
                    }
                    default:
                        break;
                }
            },
            viewConnectedUsers(evt) {
                Swal.fire({
                    title: `${this.ws_userConnected.length === 1 ? 'Utilisateur connecté' : 'Utilisateurs connectés'}`,
                    html: `${this.ws_userConnected.join(' <br/> ')}`,
                });

                evt.preventDefault();
            },

            /**
             * Quand on clique sur un Server dans le sélecteur des serveurs
             * */
            onClickServer(evt) {
                const serverSelect = evt.target.selectedOptions[0];

                const serverLogin = serverSelect.dataset.login || null;
                const serverAdresse = serverSelect.dataset.adresse || null;
                const serverPort = serverSelect.dataset.port || null;
                const serverID = serverSelect.dataset.id || null;
                const serverName = serverSelect.value || null;

                if (!(serverLogin === null || serverAdresse === null || serverPort === null || serverID === null)) {
                    this.loadServerByID(serverAdresse, serverPort, serverLogin, serverID, serverName);
                    evt.preventDefault();
                    return;
                }

                this.currentServer = null;
                this.gridData = [];
                this.searchQuery = '';
                evt.preventDefault();
            },

            async getDefaultPathServer(serverID) {
                const result = await serverAPI.getOneByID(this.$store.getters.token, serverID);
                if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                let { default_path } = result.data;
                if (default_path.charAt(default_path.length - 1) !== '/') default_path += '/';
                return default_path;
            },

            /**
             * Chargement d'un serveur par son ID
             * */
            async loadServerByID(serverHost, serverPort, serverLogin, serverID, serverName) {
                this.serverLoading = true;
                this.loaderContainer = true;

                const serverDefaultPath = await this.getDefaultPathServer(serverID);
                if (!serverDefaultPath) return;

                const command = `file --mime-type ${serverDefaultPath}* && echo "--split--" && ls -lh ${serverDefaultPath}`;

                /**
                 * Vérification du délai de connexion au serveur
                 *
                 * si le délai dépasse les 20 secondes,
                 * on arrête les loaders et affiche un message
                 * */
                let timeoutStatus = false;
                const timeoutTimer = 20; // secondes
                const timeoutInstance = setTimeout(() => {
                    if (this.currentServer === null) {
                        this.loaderContainer = false;
                        this.serverLoading = false;
                        timeoutStatus = true;
                        EventBus.$emit('sendToast', {
                            type: 'error',
                            message: 'Délai de connexion au serveur trop long, merci de réessayer.',
                        });
                    }
                }, timeoutTimer * 1000);

                sshAPI
                    .exec(serverName, command, this.$store.getters.token)
                    .then((result) => {
                        clearTimeout(timeoutInstance);

                        if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
                        if (timeoutStatus) return;

                        if (!result || !result.status || result.data.length <= 0) {
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Connexion au serveur impossible.',
                            });
                            this.loaderContainer = false;
                            this.serverLoading = false;
                            throw new Error(result.data + ' --> maybe bad encryption key?');
                            return;
                        }

                        const responseAPI = JSON.stringify(result.data)
                            .split('\\n')
                            .filter((element) => element !== '"');

                        const totalLsIndex = responseAPI.findIndex((element) => element.includes('--split--'));
                        if (!totalLsIndex) {
                            this.loaderContainer = false;
                            this.serverLoading = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: "Une erreur c'est produite",
                            });
                            return;
                        }

                        const ArrayResponse = {
                            filesType: responseAPI.splice(0, totalLsIndex),
                            lsResult: responseAPI.splice(2, responseAPI.length),
                        };
                        if (ArrayResponse.filesType[0].charAt(0) === '"')
                            ArrayResponse.filesType[0] = ArrayResponse.filesType[0].slice(
                                1,
                                ArrayResponse.filesType[0].length
                            );

                        this.currentServer = {
                            id: serverID,
                            host: serverHost,
                            name: serverName,
                            port: serverPort,
                            login: serverLogin,
                            home: serverDefaultPath,
                            location: serverDefaultPath,
                        };

						this.searchQuery = '';
                        this.parseSshResponse('list', ArrayResponse);
                        this.ws_makePing();
                    })
                    .catch((err) => {
                        clearTimeout(timeoutInstance);
                        this.loaderContainer = false;
                        this.serverLoading = false;
                        throw err;
                    });
            },

            /**
             * Fonction pour parser des données SSH
             * */
            parseSshResponse(type, responseData, filename) {
                if (responseData) {
                    switch (type) {
                        case 'base64 image': {
                            const [mime, image64] = responseData.split('-|-').map((elem) => elem.trim());
                            const imgB64 = `data:${mime};base64,${image64}`;

                            const parentElement = document.createElement('div');

                            const imgElement = document.createElement('img');
                            imgElement.style.width = '100%';
                            imgElement.src = imgB64;

                            parentElement.appendChild(imgElement);

                            this.showModal({
                                title: 'Prévisualisation Image',
                                html: imgElement,
                                footer: {
                                    buttons: ['Fermer,close'],
                                },
                            });
                            this.loaderContainer = false;
                            break;
                        }
                        case 'file': {
                            const preElement = document.createElement('samp');
                            preElement.style.whiteSpace = 'pre';
                            preElement.style.textAlign = 'left';
                            preElement.textContent = responseData;
                            this.showModal({
                                title: 'Prévisualisation',
                                html: preElement,
                                footer: {
                                    buttons: ['Fermer,close'],
                                },
                            });

                            this.loaderContainer = false;
                            break;
                        }
                        case 'file csv': {
                            const fileCSV = `data:text/csv;base64,${escape(responseData)}`;

                            const parentElement = document.createElement('div');

                            const tableElement = document.createElement('table');
                            const table_theadElement = document.createElement('thead');
                            const table_tbodyElement = document.createElement('tbody');

                            tableElement.appendChild(table_theadElement);
                            tableElement.appendChild(table_tbodyElement);
                            parentElement.appendChild(tableElement);

                            fetch(fileCSV)
                                .then((res) => res.blob())
                                .then((blob) => {
                                    d3CSV(URL.createObjectURL(blob)).then((fileParsed) => {
                                        /**
                                         * J'ai fais un système de Promise pour éviter de surcharger le navigateur
                                         * de données a traitées en même temps.
                                         * */
                                        const PROMISE_theadTr = new Promise(async (successCB) => {
                                            const thead_trElement = document.createElement('tr');
                                            for await (let elem of fileParsed.columns) {
                                                const thElement = document.createElement('th');
                                                thElement.innerText = elem;
                                                thead_trElement.appendChild(thElement);
                                            }
                                            table_theadElement.appendChild(thead_trElement);

                                            setTimeout(() => {
                                                successCB();
                                            }, 1500);
                                        });

                                        PROMISE_theadTr.then(() => {
                                            const PROMISE_tbodyTr = new Promise(async (successCB) => {
                                                for await (let row of fileParsed) {
                                                    const trElement = document.createElement('tr');
                                                    for (let td in row) {
                                                        const tdElement = document.createElement('td');
                                                        tdElement.innerText = row[td];
                                                        trElement.appendChild(tdElement);
                                                    }
                                                    table_tbodyElement.appendChild(trElement);
                                                }
                                                setTimeout(() => {
                                                    successCB();
                                                }, 1500);
                                            });

                                            PROMISE_tbodyTr.then(() => {
                                                this.showModal({
                                                    title: 'Prévisualisation CSV',
                                                    html: parentElement,
                                                    footer: {
                                                        buttons: ['Fermer,close'],
                                                    },
                                                });
                                                this.loaderContainer = false;
                                            });
                                        });
                                    });
                                });

                            break;
                        }
                        case 'file pdf': {
                            fetch(`data:application/pdf;base64,${escape(responseData)}`)
                                .then((res) => res.blob())
                                .then((blob) => {
                                    window.open(URL.createObjectURL(blob), '_blank');
                                    this.loaderContainer = false;
                                });
                            break;
                        }
                        case 'stat':
                            const ulParentElement = document.createElement('ul');
                            ulParentElement.style.textAlign = 'left';

                            responseData.forEach((row, index) => {
                                const liElement = document.createElement('li');

                                if (index === 2) {
                                    return;
                                }
                                if (index === 3) {
                                    const arraySplited = row.split(' ') || null;
                                    if (arraySplited) {
                                        if (
                                            row.toLowerCase().indexOf('uid') !== -1 &&
                                            row.toLowerCase().indexOf('gid') !== -1
                                        ) {
                                            let UID = row.toLowerCase().split('uid')[1].split('gid')[0].trim();
                                            let GID = row.toLowerCase().split('gid')[1].trim();
                                            let octalPerm = row
                                                .toLowerCase()
                                                .split('uid')[0]
                                                .trim()
                                                .split(':')[1]
                                                .trim();

                                            UID = UID.slice(1, UID.length - 1)
                                                .trim()
                                                .slice(1, UID.length - 1)
                                                .trim()
                                                .split('/')
                                                .pop()
                                                .trim();
                                            GID = GID.slice(1, GID.length - 1)
                                                .trim()
                                                .slice(1, GID.length - 1)
                                                .trim()
                                                .split('/')
                                                .pop()
                                                .trim();
                                            octalPerm = octalPerm.slice(1, octalPerm.length - 1).trim();

                                            row = `Droits d'accès :  <ul>
                                                <li>Permissions: <b>${octalPerm}</b></li>
                                                <li>Utilisateur ID: <b>${UID}</b></li>
                                                <li>Groupe ID: <b>${GID}</b></li>
                                            </ul>`;
                                        }
                                    }
                                } else if (index === 4 || index === 5 || index === 6) {
                                    const arrayDate = row.split(' ') || null;
                                    if (arrayDate) {
                                        const liKey = arrayDate.shift();
                                        arrayDate.pop();
                                        const rowDate = arrayDate.join(' ');
                                        row = liKey + ' ' + formatDate(rowDate);
                                    }
                                }

                                liElement.innerHTML = row;
                                ulParentElement.appendChild(liElement);
                            });
                            this.loaderContainer = false;
                            Swal.fire({
                                title: 'Informations',
                                html: ulParentElement,
                            });
                            break;
                        case 'list':
                            this.gridData = [];
                            filterUnixLS(responseData).then((result) => {
                                if (!result) {
                                    EventBus.$emit('sendToast', {
                                        type: 'error',
                                        message: "Une erreur c'est produite",
                                    });
                                    this.serverLoading = false;
                                    this.loaderContainer = false;
                                    return;
                                }

                                result.forEach((element) =>
                                    this.gridData.push({
                                        type: element.type,
                                        name: element.name,
                                        date: `${element.day} ${element.month} - ${element.hour}`,
                                        size: element.size,
                                    })
                                );
                                this.serverLoading = false;
                                this.loaderContainer = false;
                            });
                            break;
                        default:
                            break;
                    }
                }
            },

            checkIsError(string = '') {
                string = (string && string?.toLowerCase());
                if (string && string.indexOf('cannot open') !== -1 || string.indexOf('dossier)') !== -1) {
                    return true;
                }
                return false;
            },

			async constructPathTree() {
				const serverDefaultPath = await this.getDefaultPathServer(this.currentServer.id);
				if (!serverDefaultPath) return;

				const command = `tree ${serverDefaultPath} -JdflR`;

				sshAPI
						.exec(this.currentServer.name, command, this.$store.getters.token)
						.then(async (result) => {
							if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
							if (!result.status) throw Error(result.data);

							if (result.data.indexOf('bash: tree') !== -1) {
								EventBus.$emit('sendToast', {
									type: 'error',
									message: "Le module `tree` n'est pas installé sur le serveur.",
									timer: 7000
								});
								throw new Error("Le module `tree` n'est pas installé sur le serveur.");
								return;
							} else {
								const treeResult = JSON.parse(result.data);
								const refElement = document.getElementById("serverTree");
								const ulRefElement = document.createElement('ul');

								refElement.appendChild(ulRefElement);

								refElement.style.maxHeight = '500px';
								refElement.style.width = '100%';
								refElement.style.overflow = 'auto';
								refElement.style.backgroundColor = 'silver';

								const makeTree = async (treeResult, parentElement) => {
									const tmpEntries = [];

									for await (const entries of treeResult) {
										const liElement = document.createElement('li');

										if (entries?.error) {
											/// TODO - Fix that "recursive, not followed"
											throw new Error(entries.error);
											return;
										}
										const entryName = entries.name.split('/').pop().trim();
										const entryPath = entries.name;

										tmpEntries.push(entryName);
										parentElement.appendChild(liElement);

										if (entries?.contents && entries.contents.length !== 0) {
											const detailsElement = document.createElement('details');
											const summaryElement = document.createElement('summary');
											const ulElement = document.createElement('ul');

											summaryElement.style.cursor = 'pointer';
											summaryElement.addEventListener('click', this.moveElementServerOnClick);
											summaryElement.innerText = entryName;
											summaryElement.setAttribute('data-path', entryPath);

											detailsElement.appendChild(summaryElement);
											liElement.appendChild(detailsElement);

											const [childTmpEntries, ulChildElement] = await makeTree(entries.contents, ulElement);
											detailsElement.appendChild(ulChildElement);
											tmpEntries.push(childTmpEntries);
										} else {
											liElement.innerText = entryName;
											liElement.setAttribute('data-path', entryPath);
											liElement.style.cursor = 'pointer';
											liElement.addEventListener('click', this.moveElementServerOnClick);
										}
									}

									return [tmpEntries, parentElement];
								}
								await makeTree(treeResult[0].contents, ulRefElement);
								const loaderTreeElement = document.getElementById("loaderTree");
								if (loaderTreeElement) {
									loaderTreeElement.remove();
								}
							}

						});
			},

            onClickLocation(evt) {
                const location = evt.target.selectedOptions[0].value;
                this.goFolderServer(location);
                evt.target.options[0].selected = true;
                evt.preventDefault();
            },
            goFolderServer(location = 'goBack') {
                if (location === 'goBack') {
                    location = this.currentServer.location.split('/');
                    if (location[location.length - 1] === '') location.pop();
                    location.pop();
                    location = location.length === 1 && location[0] === '' ? '/' : location.join('/');
                }
				this.searchQuery = '';

                if (location.charAt(location.length - 1) !== '/') location += '/';
                if ((location && this.checkPathServer(location)) || (location && this.isAdmin)) {
                    this.serverLoading = true;
                    this.loaderContainer = true;

                    const command = `file --mime-type "${location}"* && echo "--split--" && ls -lh "${location}"`;

                    sshAPI
                        .exec(this.currentServer.name, command, this.$store.getters.token)
                        .then((result) => {
                            if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                            if (result.status) {
                                if (result.data.length > 0) {
                                    const responseAPI = JSON.stringify(result.data)
                                        .split('\\n')
                                        .filter((element) => element !== '"');

                                    const totalLsIndex = responseAPI.findIndex((element) =>
                                        element.includes('--split--')
                                    );
                                    if (!totalLsIndex) {
                                        this.loaderContainer = false;
                                        this.serverLoading = false;
                                        EventBus.$emit('sendToast', {
                                            type: 'error',
                                            message: "Une erreur c'est produite",
                                        });
                                        return;
                                    }
                                    const ArrayResponse = {
                                        filesType: responseAPI.splice(0, totalLsIndex),
                                        lsResult: responseAPI.splice(2, responseAPI.length),
                                    };

                                    /**
									 * TODO - fix
									 * Préviens des erreurs lors de l'ouverture d'un dossier
									 * Mais pose problème si un dossier est vide...*/

                                    if (ArrayResponse.filesType.length !== 0 && ArrayResponse.lsResult.length === 0 && this.checkIsError(ArrayResponse.filesType[0])) {
										/*EventBus.$emit('sendToast', {
											type: 'error',
											message: "Une erreur c'est produite pendant l'ouverture du dossier.",
										});
										this.serverLoading = false;
										this.loaderContainer = false;
										return;*/
										ArrayResponse.filesType = [];
									}
									if (ArrayResponse.filesType.length !== 0 && ArrayResponse.lsResult.length !== 0) {
										if (ArrayResponse.lsResult[0]?.includes('total ')) ArrayResponse.lsResult.shift();
										if (ArrayResponse.filesType[0].charAt(0) === '"')
											ArrayResponse.filesType[0] = ArrayResponse.filesType[0].slice(
													1,
													ArrayResponse.filesType[0].length
											);
									}

                                    this.currentServer.location = location;
                                    this.parseSshResponse('list', ArrayResponse);
                                    this.ws_goFolder();
                                }
                            } else if (result.data === 'command not valid.') {
                                this.serverLoading = false;
                                this.loaderContainer = false;
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: 'Commande invalide.',
                                });
                            }
                        })
                        .catch((err) => {
                            console.error(err);
                            this.serverLoading = false;
                            this.loaderContainer = false;
                        });
                } else {
                    console.log("vous avez pas les droit d'allez ici : " + location);
                }
            },
            infosElementServer(location) {
                this.loaderContainer = true;
                const command = `stat "${location}"`;

                if (location.indexOf(' -> ') !== -1) {
                    EventBus.$emit('sendToast', {
                        type: 'error',
                        message: "Impossible d'éffectuer cette action sur un lien symbolique.",
                        timer: 7000,
                    });
                    this.loaderContainer = false;
                    return;
                }

                sshAPI
                    .exec(this.currentServer.name, command, this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                        if (result.status) {
                            if (result.data && result.data.length > 0) {
                                let responseAPI = [];
                                JSON.stringify(result.data)
                                    .split('\\n')
                                    .forEach((elem) => {
                                        let element = elem.trim();
                                        if (element.charAt(0) === '"') element = element.slice(1, element.length);
                                        if (element.charAt(element.length) === '"')
                                            element = element.slice(0, element.length - 1);
                                        element = element.trim();
                                        if (element !== '' && element !== '"') responseAPI.push(element);
                                    });
                                this.parseSshResponse('stat', responseAPI);
                            }
                        } else if (result.data === 'command not valid.') {
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Commande invalide.',
                            });
                        }
                    })
                    .catch((err) => {
                        console.error(err);
                        this.loaderContainer = false;
                    });
            },

            /**
             * Fonction pour récupérer le type mime d'un élément
             * */
            async mimeTypeServer(location) {
                const command = `file --mime-type -b ${location}`;

                const mimeType = await sshAPI.exec(this.currentServer.name, command, this.$store.getters.token);
                if (mimeType.tokens !== null) EventBus.$emit('expiredJWT', mimeType.tokens);
                if (mimeType.status) return mimeType.data;
                else if (mimeType.data === 'command not valid.') {
                    this.loaderContainer = false;
                    EventBus.$emit('sendToast', {
                        type: 'error',
                        message: 'Commande invalide.',
                    });
                }
            },

            /**
             * Fonctions pour Prévisualiser Des éléments
             * */
            previewFileServer(location) {
                this.loaderContainer = true;
                /**
                 * J'utilise la suite de caractère "-|-" pour
                 * ensuite pouvoir séparer ma chaîne avec ce délimiteur
                 * */
                const command = `file --mime-type -b "${location}" && echo "-|-" && base64 -w 0 "${location}"`;

                sshAPI
                    .exec(this.currentServer.name, command, this.$store.getters.token)
                    .then((result) => {
                        /**
                         * Si, les tokens on été mise à jour pendant la requête,
                         * Alors on les mets à jour.
                         * */
                        if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                        if (result.status) {
                            const rawData = result.data;
                            /**
                             * J'utilise ici mon délimiteur "-|-" pour séparer mes éléments qui sont
                             * contenu dans une même chaîne de caractères
                             * */
                            const [fileMime, file64] = rawData.split('-|-').map((elem) => elem.trim());
                            const dataDecoded = Base64.decode(file64);

                            switch (fileMime) {
                                case 'application/pdf':
                                    this.parseSshResponse('file pdf', file64);
                                    break;
                                case 'text/csv':
                                    this.parseSshResponse('file csv', file64, location.split('/').pop());
                                    break;
                                case 'text/plain':
                                    if (location.indexOf('.csv') !== -1) {
                                        this.parseSshResponse('file csv', file64, location.split('/').pop());
                                    } else {
                                        this.parseSshResponse('file', dataDecoded);
                                    }
                                    break;
                                case 'application/zip':
                                    Swal.fire(
                                        'Prévisualisation',
                                        'Prévisualisation impossible pour le type de fichier application/zip.'
                                    );
                                    this.loaderContainer = false;
                                    break;
                                default:
                                    this.parseSshResponse('file', dataDecoded);
                                    break;
                            }
                        } else if (result.data === 'command not valid.') {
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Commande invalide.',
                            });
                        }
                    })
                    .catch((err) => {
                        this.loaderContainer = false;
                        throw new Error(err);
                    });
            },
            previewImageServer(location) {
                this.loaderContainer = true;
                const command = `file --mime-type -b "${location}" && echo "-|-" && base64 -w 0 "${location}"`;
                sshAPI
                    .exec(this.currentServer.name, command, this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
                        if (result.status) {
                            this.parseSshResponse('base64 image', result.data, location.split('/').pop());
                        } else if (result.data === 'command not valid.') {
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Commande invalide.',
                            });
                        }
                    })
                    .catch((err) => {
                        this.loaderContainer = false;
                        throw new Error(err);
                    });
            },

            getFolderNameServer(location) {
                return location.split('/').pop();
            },
            postedNewElementServer(server) {
                const socket = this.$store.getters.socket || null;
                if (socket) {
                    const user = this.$store.getters.user;
                    socket.send(
                        JSON.stringify({
                            type: 'refresh_ssh',
                            email: user.email,
                            server: !this.currentServer || !this.currentServer.name ? server : this.currentServer,
                        })
                    );
                }
            },

            downloadBlob(url, name) {
                return fetch(url)
                    .then((res) => res.blob())
                    .then((blob) => {
                        const aElement = document.createElement('a');
                        document.body.appendChild(aElement);
                        aElement.style.display = 'none';
                        const url = URL.createObjectURL(blob);
                        aElement.href = url;
                        aElement.download = name;
                        aElement.click();
                        URL.revokeObjectURL(url);
                        document.body.removeChild(aElement);
                        return true;
                    });
            },

            /**
             * Fonction pour Télécharger des éléments
             * */
            downloadFileServer(location) {
                this.loaderContainer = true;
                const filename = location.split('/').pop();
                const command = `base64 -w 0 ${location}`;

                sshAPI
                    .exec(this.currentServer.name, command, this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null) {
                            EventBus.$emit('expiredJWT', result.tokens);
                        }
                        if (result.status) {
                            const rawData = result.data;

                            this.mimeTypeServer(location).then((mime) => {
                                const contentType = mime.toLowerCase().trim();

                                const url = `data:${contentType};base64,${rawData}`;
                                this.downloadBlob(url, filename).then(() => {
                                    this.loaderContainer = false;
                                });
                            });
                        } else if (result.data === 'command not valid.') {
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Commande invalide.',
                            });
                        }
                    })
                    .catch((err) => {
                        console.error(err);
                        this.loaderContainer = false;
                    });
            },

            /**
             * Fonction pour Télécharger un dossier en .zip
             * */
            downloadZipFileServer(location) {
                this.loaderContainer = true;
                const folderName = this.getFolderNameServer(location);
                let locationWithoutFolder = location.split('/');
                locationWithoutFolder.pop();
                locationWithoutFolder = locationWithoutFolder.join('/');

                sftpAPI
                    .zipFolder(
                        this.currentServer.name,
                        folderName,
                        folderName,
                        locationWithoutFolder,
                        this.$store.getters.token
                    )
                    .then((result) => {
                        if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                        if (!result.status) {
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Impossible de télécharger le dossier.',
                                timer: 7000,
                            });
                            throw new Error(result.data);
                            return;
                        }

                        const url = `data:application/zip;base64,${result.data}`;
                        this.downloadBlob(url, folderName)
                            .then(() => {
                                this.loaderContainer = false;
                            })
                            .catch((err) => {
                                EventBus.$emit('sendToast', {
                                    type: 'error',
                                    message: "Une erreur c'est produite.",
                                    timer: 7000,
                                });
                                console.error(err);
                                this.loaderContainer = false;
                            });
                    })
                    .catch((err) => {
                        console.error(err);
                        this.loaderContainer = false;
                    });
            },

            /**
             * Fonction pour supprimer des éléments
             * */
            deleteElementServer(location) {
                this.loaderContainer = true;
                const command = `rm -r ${location}`;

                sshAPI
                    .exec(this.currentServer.name, command, this.$store.getters.token)
                    .then((result) => {
                        if (result.tokens !== null) {
                            EventBus.$emit('expiredJWT', result.tokens);
                        }

                        if (result.status) {
                            this.loaderContainer = false;
                            this.goFolderServer(this.currentServer.location);
                            this.postedNewElementServer(this.currentServer.location);
                        } else if (result.data === 'command not valid.') {
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Commande invalide.',
                            });
                        }
                    })
                    .catch((err) => {
                        console.error(err);
                        this.loaderContainer = false;
                    });
            },

            /**
             * Editer un fichier
             * */
            editFile(location) {
                this.loaderContainer = true;
                /**
                 * J'utilise la suite de caractère "-|-" pour
                 * ensuite pouvoir séparer ma chaîne avec ce délimiteur
                 * */
                const command = `file --mime-type -b "${location}" && echo "-|-" && base64 -w 0 "${location}"`;

                sshAPI
                    .exec(this.currentServer.name, command, this.$store.getters.token)
                    .then((result) => {
                        /**
                         * Si, les tokens on été mise à jour pendant la requête,
                         * Alors on les mets à jour.
                         * */
                        if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                        if (result.status) {
                            const rawData = result.data;
                            /**
                             * J'utilise ici mon délimiteur "-|-" pour séparer mes éléments qui sont
                             * contenu dans une même chaîne de caractères
                             * */
                            const [fileMime, file64] = rawData.split('-|-').map((elem) => elem.trim());
                            const url = `data:${fileMime};base64,${file64}`;
                            fetch(url)
                                .then((res) => res.blob())
                                .then((blob) => {
                                    this.$store
                                        .dispatch('setEditorFile', {
                                            blob: blob,
                                            mime: fileMime,
                                            location: location,
                                            server: this.currentServer,
                                        })
                                        .then(() => {
                                            this.$router.push({ name: 'AdminEditor' });
                                        });
                                });
                        } else if (result.data === 'command not valid.') {
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Commande invalide.',
                            });
                        }
                    })
                    .catch((err) => {
                        this.loaderContainer = false;
                        throw new Error(err);
                    });
            },

            saveFile(blob, location, server) {
                this.loaderContainer = true;

                const blobName = blob.name ? blob.name.replace(new RegExp(' ', 'g'), '_') : location.split('/').pop();
                let locationWithoutFile = location.split('/');
                locationWithoutFile.pop();
                locationWithoutFile = locationWithoutFile.join('/');
                documentAPI
                    .newDocument(
                        this.$store.getters.token,
                        blobName,
                        locationWithoutFile,
                        blob.size,
                        this.$store.getters.user.id,
                        server.id,
                        blob
                    )
                    .then((result) => {
                        if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                        if (result.status) {
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'success',
                                message: `Fichier sauvegardé`,
                                timer: 2000,
                            });
                            this.postedNewElementServer(server);
                        } else {
                            this.loaderContainer = false;
                            EventBus.$emit('sendToast', {
                                type: 'error',
                                message: 'Fichier non sauvegardé',
                                timer: 2500,
                            });
                            throw new Error(result.data);
                        }
                    });
            },

            /**
             * Fonction pour renommer des éléments
             * */
            renameElementServer(location) {
                let baseElementDir = location.split('/');
                const baseElementName = baseElementDir.pop();
                baseElementDir = baseElementDir.join('/');

                Swal.fire({
                    title: 'Renommer',
                    input: 'text',
                    inputValue: baseElementName,
                    html:
                        "<i style='font-size: .7rem'>*Si c'est un fichier ou une image, n'oubliez pas son extension. (monImage.<b>png</b>)</i>",
                    showCancelButton: true,
                    cancelButtonText: 'Annuler',
                    confirmButtonText: 'Valider',
                    showLoaderOnConfirm: true,
                    preConfirm: (name) => {
                        let elementName = name.trim();
                        elementName.replace(new RegExp(' ', 'g'), '_');

                        const command = `mv ${location} ${baseElementDir}/${elementName}`;

                        return sshAPI
                            .exec(this.currentServer.name, command, this.$store.getters.token)
                            .then((result) => {
                                if (result.tokens !== null) {
                                    EventBus.$emit('expiredJWT', result.tokens);
                                }
                                if (result.status) return;
                                if (result.data === 'command not valid.') {
                                    this.loaderContainer = false;
                                    EventBus.$emit('sendToast', {
                                        type: 'error',
                                        message: 'Commande invalide.',
                                    });
                                }
                            })
                            .catch((err) => {
                                console.error(err);
                                this.loaderContainer = false;
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                }).then((result) => {
                    if (result.value) {
                        this.goFolderServer(baseElementDir);
                        this.postedNewElementServer(this.currentServer.location);
                    }
                });
            },

			moveElementServerOnClick(evt) {
            	const path = evt.target?.dataset?.path;
            	if (!path) throw new Error("Path not found.");

            	if (this.moveElementServer_selectedFolderPrevious) {
					this.moveElementServer_selectedFolderPrevious.style.borderBottom = 'none';
					this.moveElementServer_selectedFolderPrevious = null;
				}

            	this.moveElementServer_selectedFolderPrevious = evt.target;
				evt.target.style.borderBottom = '2px solid blue';
				this.moveElementServer_selectedFolder = path;
			},

            /**
             * Fonction pour déplacer un élément
             * */
            moveElementServer(location) {
                let baseElementDir = location.split('/');
                baseElementDir.pop();
                baseElementDir = baseElementDir.join('/');

				// TODO - Pouvoir sélectionner un dossier dans la liste qui se génère
				Swal.fire({
					title: 'Déplacement',
					html: "<div id='loaderTree'>Chargement ... </div>" +
							"<div id='serverTree' style='text-align: left'></div>",
					showCancelButton: true,
					cancelButtonText: 'Annuler',
					confirmButtonText: 'Valider',
					showLoaderOnConfirm: true,
					onRender: () => {
						this.constructPathTree();
					},
					onClose: () => {
						const refElement = document.getElementById("serverTree");
						if (refElement) refElement.innerHTML = "";
					},
					preConfirm: () => {
						const elementName = this.moveElementServer_selectedFolder;
						if (!elementName || elementName.length === 0) return false;

						const command = `mv "${location}" "${elementName}"`;

						return sshAPI
								.exec(this.currentServer.name, command, this.$store.getters.token)
								.then((result) => {
									if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
									if (result.status) return;
									if (result.data === 'command not valid.') {
										this.loaderContainer = false;
										EventBus.$emit('sendToast', {
											type: 'error',
											message: 'Commande invalide.',
										});
									}
								})
								.catch((err) => {
									console.error(err);
									this.loaderContainer = false;
								});
					},
					allowOutsideClick: () => !Swal.isLoading(),
				}).then((result) => {
					if (result.value) {
						EventBus.$emit('sendToast', {
							type: 'success',
							message: 'Dossier/Fichier bien déplacer.',
						});
						this.goFolderServer(baseElementDir);
						this.postedNewElementServer(this.currentServer.location);
					}
				});

                /** Il faut renseigner la valeur en brute dans l'input
                Swal.fire({
                    title: 'Déplacement',
                    input: 'text',
                    inputValue: location,
                    html:
                        "<i style='font-size: .7rem'>" +
							"*Il vous faut bien spécifier tout le chemin du dossier." + "<br>" +
							"*Si c'est un fichier ou une image, n'oubliez pas son extension. (monImage.<b>png</b>)" +
							"</i>",
                    showCancelButton: true,
                    cancelButtonText: 'Annuler',
                    confirmButtonText: 'Valider',
                    showLoaderOnConfirm: true,
                    preConfirm: (name) => {
                        const elementName = name.trim();

                        const command = `mv ${location} ${elementName}`;

                        return sshAPI
                            .exec(this.currentServer.name, command, this.$store.getters.token)
                            .then((result) => {
                                if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);
                                if (result.status) return;
                                if (result.data === 'command not valid.') {
                                    this.loaderContainer = false;
                                    EventBus.$emit('sendToast', {
                                        type: 'error',
                                        message: 'Commande invalide.',
                                    });
                                }
                            })
                            .catch((err) => {
                                console.error(err);
                                this.loaderContainer = false;
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                }).then((result) => {
                    if (result.value) {
                        this.goFolderServer(baseElementDir);
                        this.postedNewElementServer(this.currentServer.location);
                    }
                });
                */
            },

            /**
             * Retourne Vrai ou Faux, en fonction de si l'utilisateur
             * est encore dans le bon dossier.
             * */
            checkPathServer(location) {
                return !!(location && location.indexOf(this.currentServer.home) !== -1);
            },

            /**
             * Fonction pour créer des sous dossiers
             * */
            newSubFolder(location) {
                Swal.fire({
                    title: "Création d'un sous-dossier",
                    input: 'text',
                    showCancelButton: true,
                    cancelButtonText: 'Annuler',
                    confirmButtonText: 'Valider',
                    showLoaderOnConfirm: true,
                    preConfirm: (name) => {
                        let folderName = name.trim();
                        folderName.replace(new RegExp(' ', 'g'), '_');

                        const command = `mkdir "${location}/${folderName}"`;

                        return sshAPI
                            .exec(this.currentServer.name, command, this.$store.getters.token)
                            .then((result) => {
                                if (result.tokens !== null) {
                                    EventBus.$emit('expiredJWT', result.tokens);
                                }
                                if (result.status) {
                                    if (result.data.length === 0) return folderName;
                                    return { data: result.data };
                                } else if (result.data === 'command not valid.') {
                                    this.loaderContainer = false;
                                    EventBus.$emit('sendToast', {
                                        type: 'error',
                                        message: 'Commande invalide.',
                                    });
                                }
                            })
                            .catch((err) => {
                                console.error(err);
                                this.loaderContainer = false;
                            });
                    },
                    allowOutsideClick: () => !Swal.isLoading(),
                }).then((result) => {
                    if (result.value) {
                        if (result.value.data) {
                            EventBus.$emit('sendToast', {
                                message: result.value.data,
                                type: 'error',
                                timer: 7000,
                            });
                        } else {
                            EventBus.$emit('sendToast', {
                                message: 'Dossier bien créé.',
                                type: 'success',
                                timer: 7000,
                            });
                            this.goFolderServer(this.currentServer.location);
                            this.postedNewElementServer(this.currentServer.location);
                        }
                    }
                });
            },

            /**
             * Fonction des écouteurs d'évenements
             * */
            initEventsListeners() {
                EventBus.$on('SSH_openDir', (folder) => {
                    if (this.currentServer && this.currentServer.location) {
                        let location = this.currentServer.location;
                        if (location.charAt(location.length - 1) !== '/') location += '/';
                        location += folder.name;
                        this.goFolderServer(location);
                    }
                });
                EventBus.$on('SSH_editFile', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        const location =
                            (this.currentServer.location !== '/' ? this.currentServer.location : '') +
                            (this.currentServer.location.charAt(this.currentServer.location.length - 1) !== '/')
                                ? '/'
                                : '' + element.name;
                        this.editFile(location);
                    }
                });
                EventBus.$on('SSH_saveFile', (element) => {
                    const blob = element.blob;
                    const location = element.location;
                    const server = element.server;

                    this.saveFile(blob, location, server);
                });
                EventBus.$on('SSH_infosElement', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        const location =
                            (this.currentServer.location !== '/' ? this.currentServer.location : '') +
                            '/' +
                            element.name;
                        this.infosElementServer(location);
                    }
                });
                EventBus.$on('SSH_renameElement', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        let location;
                        if (this.currentServer.location !== '/') {
                            location = this.currentServer.location;
                        }
                        const locationLength = location.length;
                        if (location.charAt(locationLength - 1) !== '/') {
                            location += '/';
                        }

                        location += element.name;

                        this.renameElementServer(location);
                    }
                });
                EventBus.$on('SSH_moveElement', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        let location;
                        if (this.currentServer.location !== '/') {
                            location = this.currentServer.location;
                        }
                        const locationLength = location.length;
                        if (location.charAt(locationLength - 1) !== '/') {
                            location += '/';
                        }

                        location += element.name;

                        this.moveElementServer(location);
                    }
                });
                EventBus.$on('SSH_createFolder', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        const location =
                            (this.currentServer.location !== '/' ? this.currentServer.location : '') +
                            '/' +
                            element.name;
                        this.newSubFolder(location);
                    }
                });
                EventBus.$on('SSH_goBack', () => {
                    if (this.currentServer && this.currentServer.location) {
                        this.goFolderServer('goBack');
                    }
                });
                EventBus.$on('SSH_previewFile', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        const location =
                            (this.currentServer.location !== '/' ? this.currentServer.location : '') +
                            '/' +
                            element.name;
                        this.previewFileServer(location);
                    }
                });
                EventBus.$on('SSH_downloadElement', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        const location =
                            (this.currentServer.location !== '/' ? this.currentServer.location : '') +
                            '/' +
                            element.name;
                        this.downloadFileServer(location);
                    }
                });
                EventBus.$on('SSH_downloadZIP', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        const location =
                            (this.currentServer.location !== '/' ? this.currentServer.location : '') +
                            '/' +
                            element.name;
                        this.downloadZipFileServer(location);
                    }
                });
                EventBus.$on('SSH_previewImage', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        const location =
                            (this.currentServer.location !== '/' ? this.currentServer.location : '') +
                            '/' +
                            element.name;
                        this.previewImageServer(location);
                    }
                });
                EventBus.$on('SSH_deleteElement', (element) => {
                    if (this.currentServer && this.currentServer.location) {
                        const location = this.currentServer.location !== '/' ? this.currentServer.location : '';
                        const locationElement = `"${location}/${element.name}"`;

                        Swal.fire({
                            icon: 'question',
                            text: `Êtes-vous sûr de vouloir supprimer "${element.name}" ?`,
                            confirmButtonText: 'Oui, supprimer.',
                            cancelButtonText: 'Non, ne pas supprimer.',
                            confirmButtonColor: '#f25e5e',
                            showCancelButton: true,
                        }).then((value) => {
                            if (value.value !== undefined && value.value) {
                                this.deleteElementServer(locationElement);
                            }
                        });
                    }
                });
                EventBus.$on('SSH_deleteElements', (elements) => {
                    if (this.currentServer && this.currentServer.location) {
                        const location = this.currentServer.location !== '/' ? this.currentServer.location : '';
                        let locationElements = elements.map((element) => `"${location}/${element}"`).join(' ');
                        Swal.fire({
                            icon: 'question',
                            text: `Êtes-vous sûr de vouloir supprimer les éléments sélectionnés ?`,
                            confirmButtonText: 'Oui, supprimer.',
                            cancelButtonText: 'Non, ne pas supprimer.',
                            confirmButtonColor: '#f25e5e',
                            showCancelButton: true,
                        }).then((value) => {
                            if (value.value !== undefined && value.value) {
                                this.deleteElementServer(locationElements);
                            }
                        });
                    }
                });

                EventBus.$on('closeBrowser', () => this.ws_disconnectMe());

                const socket = this.$store.getters.socket || null;
                if (socket) this.ws_initEvent(socket);
                else this.created_socketInterval();

                /**
                 * écouteurs d'évènements sur les Touches du clavier
                 * LeftControl + LeftArrow = Retour arrière Dossier SSH
                 * */
                window.addEventListener('keydown', this.keyboardAction_keydown);
                window.addEventListener('keyup', this.keyboardAction_keyup);
            },
            keyboardAction_keydown(evt) {
                if (evt.code === 'ControlLeft') this.backServerFolderKey = true;
                if (this.backServerFolderKey && evt.code === 'ArrowLeft') this.goFolderServer('goBack');
            },
            keyboardAction_keyup(evt) {
                if (evt.code === 'ControlLeft') this.backServerFolderKey = false;
            },
            destroyEventsListeners() {
                window.removeEventListener('keydown', this.keyboardAction_keydown);
                window.removeEventListener('keyup', this.keyboardAction_keyup);

                const socket = this.$store.getters.socket || null;
                if (socket) socket.removeEventListener('message', this.ws_OnMessage);
            },

            findServerByID(id, array) {
                return array.find((server) => server.id === id);
            },

            /**
             * On vérifie toutes les secondes, si le socket est enregistré dans VueX
             * */
            created_socketInterval() {
                this.created_socketIT = setInterval(() => {
                    const socket = this.$store.getters.socket || null;
                    if (socket) {
                        clearInterval(this.created_socketIT);
                        this.created_socketIT = null;
                        this.ws_initEvent(socket);
                    }
                }, 1000);
            },
        },
        mounted() {
            this.initEventsListeners();

            /* Récupère tous les serveurs */
            serverAPI
                .getAll(this.$store.getters.token)
                .then(async (result) => {
                    if (result.tokens !== null) EventBus.$emit('expiredJWT', result.tokens);

                    if (result.status) {
                        const servers = await result.data.filter((server) => {
                            if (!this.isAdmin && server.accepted_roles.indexOf('ROLE_USER') !== -1) return server;
                            else if (this.isAdmin) return server;
                        });
                        this.serversData = servers;
                        this.serversLoading = false;

                        /**
                         * Si un serveur a été défini comme par Défaut
                         * On le recherche dans la liste,
                         * Si on le trouve on se connecte dessus
                         * */
                        const findSTUP_server = this.findServerByID(this.$store.getters.STUP_server, servers);
                        if (findSTUP_server !== undefined) {
                            this.serverSTUP_id = findSTUP_server.id;
                            this.loadServerByID(
                                findSTUP_server.adresse,
                                findSTUP_server.port,
                                findSTUP_server.login,
                                findSTUP_server.id,
                                findSTUP_server.name
                            );
                        } else this.$store.dispatch('setSTUP_Server', -1);
                    }
                })
                .catch((err) => console.error(err));
        },
        beforeDestroy() {
            this.ws_userConnected = [];
            this.currentServer = null;
            this.destroyEventsListeners();
            this.ws_disconnectMe();
        },
    };
</script>

<style lang="scss" scoped>
    #serverAndDirectoryPreview {
        width: auto;

        display: flex;
        flex-wrap: wrap;

        #tableServer {
            width: calc(100% - 300px);
            min-width: 300px;
        }
    }
    #csvTable {
        table {
            border-collapse: collapse !important;
            border: 2px black solid !important;
            font: 12px sans-serif !important;
        }

        td {
            border: 1px black solid !important;
            padding: 5px !important;
        }
    }
</style>
