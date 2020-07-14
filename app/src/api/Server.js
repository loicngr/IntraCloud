'use strict';

import { Api } from './Api.js';
import { delete_token, get_token, post_token } from './utils/fetchRequests';
import { removeLastChar } from '../utils/functions';

class Server {
    API = null;
    endpoint = {
        base: 'server/',
        name: 'name/',
        getAll: 'servers/',
        documents: 'documents/',
        find: 'find/',
        hostPortLogin: '{HOST}/{PORT}/{LOGIN}/',
        privateKey: 'privatekey/',
        passphrase: 'passphrase/',
        delete: 'delete/',
        id: '{ID}/',
        update: 'update/',
    };

    constructor() {
        this.API = new Api();
    }

    /**
     * Serveur By
     * */
    async getOneByID(token, id) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (token && id) {
            let endpoint = this.endpoint.base + removeLastChar(this.endpoint.id);
            endpoint = endpoint.replace('{ID}', id);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await get_token(url, token, this.API, responseRQ);
        }

        return responseRQ;
    }
    async getOneByName(name, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (name && token) {
            const endpoint = this.endpoint.base + removeLastChar(this.endpoint.name);
            const { adresse, protocol, ENCRYPTION, ENCRYPTION_KEY } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            const nameEncrypted = ENCRYPTION.encrypt(name.trim(), ENCRYPTION_KEY);
            formData.append('name', nameEncrypted);

            responseRQ = await post_token(url, formData, token, this.API, responseRQ);
        }

        return responseRQ;
    }
    async getByHostPortLogin(host, port, login, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (host && port && login && token) {
            let endpoint = this.endpoint.base + this.endpoint.find + removeLastChar(this.endpoint.hostPortLogin);
            endpoint = endpoint.replace('{HOST}', host);
            endpoint = endpoint.replace('{PORT}', port);
            endpoint = endpoint.replace('{LOGIN}', login);

            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await get_token(url, token, this.API, responseRQ);
        }

        return responseRQ;
    }

    /**
     * Serveurs
     * */
    async getAll(token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (token) {
            const endpoint = removeLastChar(this.endpoint.getAll);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await get_token(url, token, this.API, responseRQ);
        }

        return responseRQ;
    }

    /**
     * Serveur Documents
     * Récupérer des documents d'un Serveur / Supprimer des documents d'un Serveur
     * */
    async getDocuments(id_server, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (id_server && token) {
            let endpoint = this.endpoint.base + this.endpoint.id + removeLastChar(this.endpoint.documents);
            endpoint = endpoint.replace('{ID}', id_server);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await get_token(url, token, this.API, responseRQ);
        }

        return responseRQ;
    }
    async deleteDocuments(id_server, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (id_server && token) {
            let endpoint = this.endpoint.base + this.endpoint.id + removeLastChar(this.endpoint.documents);
            endpoint = endpoint.replace('{ID}', id_server);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await delete_token(url, token, this.API, responseRQ);
        }

        return responseRQ;
    }

    /**
     * Mise à jour d'un Serveur
     * @param {number} id
     * @param {{
     *     login: string|null,
     *     password: string|null,
     *     defaultPath: string|null,
     *     privateKey: string|null,
     *     passphrase: string|null,
     *     adresse: string|null,
     *     port: string|null,
     *     name: string|null,
     * }} server
     * @param {string} token
     * @return {Promise<{data: string|{}, tokens: null|{}, status: boolean}>}
     */
    async update(id, server, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (token && id) {
            let endpoint = this.endpoint.base + this.endpoint.id + removeLastChar(this.endpoint.update);
            endpoint = endpoint.replace('{ID}', id);
            const { adresse, protocol, ENCRYPTION, ENCRYPTION_KEY } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            if (server.login) {
                const loginEncrypted = ENCRYPTION.encrypt(server.login, ENCRYPTION_KEY);
                formData.append('username', loginEncrypted);
            }
            if (server.password) {
                const passwordEncrypted = ENCRYPTION.encrypt(server.password, ENCRYPTION_KEY);
                formData.append('password', passwordEncrypted);
            }
            if (server.defaultPath) {
                const defaultPathEncrypted = ENCRYPTION.encrypt(server.defaultPath, ENCRYPTION_KEY);
                formData.append('defaultPath', defaultPathEncrypted);
            }
            if (server.privateKey) {
                const privateKeyEncrypted = ENCRYPTION.encrypt(server.privateKey, ENCRYPTION_KEY);
                formData.append('privateKey', privateKeyEncrypted);
            }
            if (server.passphrase) {
                const passphraseEncrypted = ENCRYPTION.encrypt(server.passphrase, ENCRYPTION_KEY);
                formData.append('passphrase', passphraseEncrypted);
            }
            if (server.adresse) {
                const adresseEncrypted = ENCRYPTION.encrypt(server.adresse, ENCRYPTION_KEY);
                formData.append('adresse', adresseEncrypted);
            }
            if (server.port) {
                const portEncrypted = ENCRYPTION.encrypt(server.port, ENCRYPTION_KEY);
                formData.append('port', portEncrypted);
            }
            if (server.name) {
                const nameEncrypted = ENCRYPTION.encrypt(server.name, ENCRYPTION_KEY);
                formData.append('name', nameEncrypted);
            }
            if (server.acceptedRoles) {
                const acceptedRolesEncrypted = ENCRYPTION.encrypt(server.acceptedRoles, ENCRYPTION_KEY);
                formData.append('acceptedRoles', acceptedRolesEncrypted);
            }

            responseRQ = await post_token(url, formData, token, this.API, responseRQ);
        }

        return responseRQ;
    }

    /**
     * Serveur Delete
     * Supprimer des éléments d'un Serveur / Supprimer un Serveur
     * */
    async deletePrivateKey(id_server, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (id_server && token) {
            let endpoint =
                this.endpoint.base + this.endpoint.id + this.endpoint.delete + removeLastChar(this.endpoint.privateKey);
            endpoint = endpoint.replace('{ID}', id_server);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await delete_token(url, token, this.API, responseRQ);
        }

        return responseRQ;
    }
    async deletePassphrase(id_server, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (id_server && token) {
            let endpoint =
                this.endpoint.base + this.endpoint.id + this.endpoint.delete + removeLastChar(this.endpoint.passphrase);
            endpoint = endpoint.replace('{ID}', id_server);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await delete_token(url, token, this.API, responseRQ);
        }

        return responseRQ;
    }
    async deleteServer(id_server, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (id_server && token) {
            let endpoint = this.endpoint.base + removeLastChar(this.endpoint.id);
            endpoint = endpoint.replace('{ID}', id_server);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await delete_token(url, token, this.API, responseRQ);
        }

        return responseRQ;
    }

    /**
     * Serveur New
     * Création d'un nouveau serveur
     * */
    async newServer(server, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (server && token) {
            const endpoint = removeLastChar(this.endpoint.base);
            const { adresse, protocol, ENCRYPTION, ENCRYPTION_KEY } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            if (server.login) {
                const loginEncrypted = ENCRYPTION.encrypt(server.login, ENCRYPTION_KEY);
                formData.append('username', loginEncrypted);
            }
            if (server.password) {
                const passwordEncrypted = ENCRYPTION.encrypt(server.password, ENCRYPTION_KEY);
                formData.append('password', passwordEncrypted);
            }
            if (server.defaultPath) {
                const defaultPathEncrypted = ENCRYPTION.encrypt(server.defaultPath, ENCRYPTION_KEY);
                formData.append('defaultPath', defaultPathEncrypted);
            }
            if (server.privateKey) {
                const privateKeyEncrypted = ENCRYPTION.encrypt(server.privateKey, ENCRYPTION_KEY);
                formData.append('privateKey', privateKeyEncrypted);
            }
            if (server.passphrase) {
                const passphraseEncrypted = ENCRYPTION.encrypt(server.passphrase, ENCRYPTION_KEY);
                formData.append('passphrase', passphraseEncrypted);
            }
            if (server.adresse) {
                const adresseEncrypted = ENCRYPTION.encrypt(server.adresse, ENCRYPTION_KEY);
                formData.append('adresse', adresseEncrypted);
            }
            if (server.port) {
                const portEncrypted = ENCRYPTION.encrypt(server.port, ENCRYPTION_KEY);
                formData.append('port', portEncrypted);
            }
            if (server.name) {
                const nameEncrypted = ENCRYPTION.encrypt(server.name, ENCRYPTION_KEY);
                formData.append('name', nameEncrypted);
            }
            if (server.acceptedRoles) {
                const acceptedRolesEncrypted = ENCRYPTION.encrypt(server.acceptedRoles, ENCRYPTION_KEY);
                formData.append('acceptedRoles', acceptedRolesEncrypted);
            }

            responseRQ = await post_token(url, formData, token, this.API, responseRQ);
        }

        return responseRQ;
    }
}

export { Server };
