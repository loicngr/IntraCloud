'use strict';

import { Api } from './Api.js';

/**
 * Actualise un token donné en paramètre
 * @param {string} token Le token d'actualisation
 * @return {Promise<{data: string, status: boolean}>}
 */
async function refreshToken(token) {
    const responseRQ = {
        status: false,
        data: 'Parameters not correct.',
    };

    if (token) {
        const api = new Api();
        const endpoint = 'token/refresh';
        const { adresse, protocol } = api;
        const url = `${protocol}://${adresse}/${endpoint}`;

        const formData = new FormData();
        formData.append('refresh_token', token);

        try {
            const responseAPI = await fetch(url, {
                method: 'POST',
                body: formData,
            });
            if (responseAPI.ok) {
                const responseData = await responseAPI.json();

                if (responseData.code !== undefined) {
                    if (responseData.code === 401) {
                        responseRQ.status = false;
                        responseRQ.data = 'Expired JWT Refresh Token';
                    }
                } else {
                    responseRQ.status = true;
                    responseRQ.data = responseData;
                }
            } else {
                responseRQ.data = 'Response error.';
            }
        } catch (error) {
            responseRQ.data = error;
        }
    }
    return responseRQ;
}

export { refreshToken };
