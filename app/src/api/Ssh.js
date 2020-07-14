'use strict';

import { Api } from './Api.js';
import { post_token } from './utils/fetchRequests';
import { removeLastChar } from '../utils/functions';

class Ssh {
    API = null;
    endpoint = {
        base: 'ssh/',
        exec: 'exec/',
    };
    responseRQ = {
        status: false,
        data: 'Parameters not correct.',
        tokens: null,
    };

    constructor() {
        this.API = new Api();
    }

    /**
     * Execution d'une commande dans un serveur
     * @param {string} name
     * @param {string} command
     * @param {string} token
     * @return {Promise<{data: string|{}, tokens: null|{}, status: boolean}>}
     */
    async exec(name, command, token) {
        if (name && command && token) {
            const endpoint = this.endpoint.base + removeLastChar(this.endpoint.exec);
            const { adresse, protocol, ENCRYPTION, ENCRYPTION_KEY } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            formData.append('name', name);
            formData.append('command', command);

            this.responseRQ = await post_token(url, formData, token, this.API, this.responseRQ, 'ssh');

            if (this.responseRQ.status || this.responseRQ.status === 200) {
                this.responseRQ.data = ENCRYPTION.decrypt(this.responseRQ.data, ENCRYPTION_KEY);
                if (this.responseRQ.data !== 'login failed.') {
                    this.responseRQ.data = decodeURIComponent(JSON.parse(this.responseRQ.data));
                } else {
                    this.responseRQ.status = false;
                }
            }
        }

        return this.responseRQ;
    }
}

export { Ssh };
