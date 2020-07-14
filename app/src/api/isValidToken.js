'use strict';

import { Api } from './Api.js';

/**
 * Vérification du token
 * Requête pour vérifier si le token fonctionne toujours
 * @param {string} token
 * @return {Promise<{data: string, status: boolean}>}
 */
async function isValidToken(token) {
    const responseRQ = {
        status: false,
        data: 'Parameters not correct.',
    };

    if (token) {
        const api = new Api();
        const endpoint = 'me';
        const url = `${api.protocol}://${api.adresse}/${endpoint}`;

        try {
            const responseAPI = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'Content-type': 'application/json',
                    Authorization: 'bearer ' + token,
                },
                mode: 'cors',
            });

            const responseData = await responseAPI.json();

            let decryptedMessage = api.ENCRYPTION.decrypt(responseData, api.ENCRYPTION_KEY);
            decryptedMessage = JSON.parse(decodeURIComponent(decryptedMessage));

            if (responseAPI.ok) {
                if (decryptedMessage.code !== undefined) {
                    if (decryptedMessage.code === 401) {
                        responseRQ.status = false;
                        responseRQ.data = 'Expired JWT Token';
                    }
                } else {
                    if (decryptedMessage.status === 200) {
                        responseRQ.status = true;
                        responseRQ.data = decryptedMessage.data;
                    } else {
                        responseRQ.status = false;
                        responseRQ.data = decryptedMessage.data;
                    }
                }
            } else {
                responseRQ.data = decryptedMessage.message;
            }
        } catch (error) {
            responseRQ.data = error;
        }
    }
    return responseRQ;
}

export { isValidToken };
