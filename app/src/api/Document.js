'use strict';

import { Api } from './Api.js';
import { removeLastChar } from '../utils/functions';
import { delete_token, post_token } from './utils/fetchRequests';

class Document {
    API = null;
    endpoint = {
        base: 'document/',
        delete: '{ID}/',
    };

    constructor() {
        this.API = new Api();
    }

    /**
     * Téléverser un nouveau document
     * @param {string} token
     * @param {string} name
     * @param {string} location
     * @param {number} size
     * @param {number} user_id
     * @param {number} server_id
     * @param {Blob} file
     * @return {Promise<{data: string|[], tokens: null|[], status: boolean}>}
     */
    async newDocument(token, name, location, size, user_id, server_id, file) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (token && name && location && size && user_id && server_id && file) {
            const endpoint = removeLastChar(this.endpoint.base);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            formData.append('name', name);
            formData.append('location', location);
            formData.append('size', size.toString());
            formData.append('user_id', user_id.toString());
            formData.append('server_id', server_id.toString());
            formData.append('file', file);

            responseRQ = await post_token(url, formData, token, this.API, responseRQ);
        }

        return responseRQ;
    }

    /**
     * Supprimer un document par son ID
     * @param {number} id
     * @param {string} token
     * @return {Promise<{data: string|[], tokens: null|{}, status: boolean}>}
     */
    async deleteByID(id, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (token && id) {
            let endpoint = this.endpoint.base + removeLastChar(this.endpoint.delete);
            endpoint = endpoint.replace('{ID}', id.toString());
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await delete_token(url, token, this.API, responseRQ);
        }

        return responseRQ;
    }
}

export { Document };
