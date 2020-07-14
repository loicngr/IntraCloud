'use strict';

import { Api } from './Api.js';
import { post_token } from './utils/fetchRequests';
import { removeLastChar } from '../utils/functions';

class Sftp {
    API = null;
    endpoint = {
        base: 'sftp/',
        zip: 'zip/',
    };

    constructor() {
        this.API = new Api();
    }

    /**
     * Télécharge un dossier au format ZIP
     * @param {string} name
     * @param {string} zipTarget
     * @param {string} zipFilename
     * @param {string} folderPath
     * @param {string} token
     * @return {Promise<{data: string, tokens: null, status: boolean}>}
     */
    async zipFolder(name, zipTarget, zipFilename, folderPath, token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
            tokens: null,
        };

        if (name && zipTarget && zipFilename && folderPath && token) {
            const endpoint = this.endpoint.base + removeLastChar(this.endpoint.zip);
            const { adresse, protocol, ENCRYPTION, ENCRYPTION_KEY } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            formData.append('name', name);
            formData.append('zipTarget', zipTarget);
            formData.append('zipFilename', zipFilename);
            formData.append('folderPath', folderPath);

            responseRQ = await post_token(url, formData, token, this.API, responseRQ, 'sftp');
            if (responseRQ.status || responseRQ.status === 200)
                responseRQ.data = await ENCRYPTION.decrypt(responseRQ.data, ENCRYPTION_KEY);
        }

        return responseRQ;
    }
}

export { Sftp };
