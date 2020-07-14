'use strict';

import { refreshToken } from '../refreshToken';

async function delete_token(url, token, api, responseRQ) {
    let tryStatus = false;

    async function tryThis() {
        tryStatus = true;
        let responseAPI;

        try {
            responseAPI = await fetch(url, {
                method: 'DELETE',
                headers: {
                    Accept: 'application/json',
                    'Content-type': 'application/json',
                    Authorization: 'bearer ' + token,
                },
                mode: 'cors',
            });
        } catch (error) {
            responseRQ.data = error;
            console.error(error);
            return responseRQ;
        }

        const responseData = await responseAPI.json();

        if (responseData.code !== undefined && responseData.message !== undefined) {
            responseRQ.status = false;
            responseRQ.data = 'Expired JWT Token';
            return responseRQ;
        }

        let decryptedMessage = api.ENCRYPTION.decrypt(responseData, api.ENCRYPTION_KEY);
        decryptedMessage = JSON.parse(decodeURIComponent(decryptedMessage));

        responseRQ.status = false;
        responseRQ.data = decryptedMessage.message;

        if (responseAPI.ok) {
            responseRQ.data = decryptedMessage.data;
            if (decryptedMessage.status === 200) responseRQ.status = true;
        } else responseRQ.data = decryptedMessage.message;

        return responseRQ;
    }

    responseRQ = await tryThis();
    if (tryStatus && responseRQ.data === 'Expired JWT Token' && !responseRQ.status) {
        const responseRefresh = await refreshToken(localStorage.getItem(api.tokens.refresh));
        token = responseRefresh.data.token;
        responseRQ.tokens = responseRefresh.data;
        await tryThis();
    }

    return responseRQ;
}

async function post_blank(url, formData, api, responseRQ) {
    try {
        const responseAPI = await fetch(url, {
            method: 'POST',
            body: formData,
            mode: 'cors',
        });

        const responseData = await responseAPI.json();

        let decryptedMessage = api.ENCRYPTION.decrypt(responseData, api.ENCRYPTION_KEY);
        decryptedMessage = JSON.parse(decodeURIComponent(decryptedMessage));

        responseRQ.status = false;
        responseRQ.data = decryptedMessage.message;

        if (responseAPI.ok) {
            responseRQ.data = decryptedMessage.data;
            if (decryptedMessage.status === 200) responseRQ.status = true;
        } else responseRQ.data = decryptedMessage.message;
    } catch (error) {
        responseRQ.data = error;
    }

    return responseRQ;
}

async function post_obj(url, obj, responseRQ) {
    try {
        const responseAPI = await fetch(url, {
            method: 'POST',
            body: JSON.stringify(obj),
            mode: 'cors',
            headers: {
                Accept: 'application/json',
                'Content-type': 'application/json',
            },
        });

        const responseData = await responseAPI.json();
        responseRQ.data = responseData;

        if (responseAPI.ok) responseRQ.status = true;
    } catch (error) {
        responseRQ.data = error;
    }

    return responseRQ;
}

async function post_token(url, formData, token, api, responseRQ, type = '') {
    let tryStatus = false;

    async function tryThis() {
        tryStatus = true;
        let responseAPI;

        try {
            responseAPI = await fetch(url, {
                method: 'POST',
                body: formData,
                headers: {
                    Authorization: 'bearer ' + token,
                },
                mode: 'cors',
            });
            if (!responseAPI.ok) {
				responseRQ.status = false;
				responseRQ.data = "Erreur 500 du serveur.";
				throw Error(responseAPI.statusText);
			}
        } catch (error) {
            responseRQ.data = error;
            console.error(error);
            return responseRQ;
        }

        const responseData = await responseAPI.json();
        if (responseData.code !== undefined && responseData.message !== undefined) {
            responseRQ.status = false;
            responseRQ.data = 'Expired JWT Token';
            return responseRQ;
        }

        let decryptedMessage = responseData;
        if (type !== 'ssh' && type !== 'sftp') {
            decryptedMessage = api.ENCRYPTION.decrypt(responseData, api.ENCRYPTION_KEY);
            decryptedMessage = JSON.parse(decodeURIComponent(decryptedMessage));
        }

        responseRQ.status = false;
        responseRQ.data = decryptedMessage.message;

        if (responseAPI.ok) {
            responseRQ.data = decryptedMessage.data;
            if (decryptedMessage.status === 200) responseRQ.status = true;
        } else responseRQ.data = decryptedMessage.message;

        return responseRQ;
    }

    responseRQ = await tryThis();
    if (tryStatus && responseRQ.data === 'Expired JWT Token' && !responseRQ.status) {
        const responseRefresh = await refreshToken(localStorage.getItem(api.tokens.refresh));
        token = responseRefresh.data.token;
        responseRQ.tokens = responseRefresh.data;
        await tryThis();
    }

    return responseRQ;
}

async function get_token(url, token, api, responseRQ) {
    let tryStatus = false;

    async function tryThis() {
        tryStatus = true;
        let responseAPI;
        try {
            responseAPI = await fetch(url, {
                method: 'GET',
                headers: {
                    Accept: 'application/json',
                    'Content-type': 'application/json',
                    Authorization: 'bearer ' + token,
                },
            });
        } catch (error) {
            responseRQ.data = error;
            console.error(error);
            return responseRQ;
        }

        const responseData = await responseAPI.json();

        if (responseData.code !== undefined && responseData.message !== undefined) {
            responseRQ.status = false;
            responseRQ.data = 'Expired JWT Token';
            return responseRQ;
        }

        let decryptedMessage = api.ENCRYPTION.decrypt(responseData, api.ENCRYPTION_KEY);
        decryptedMessage = JSON.parse(decodeURIComponent(decryptedMessage));

        responseRQ.status = false;
        responseRQ.data = decryptedMessage.message;

        if (responseAPI.ok) {
            responseRQ.data = decryptedMessage.data;
            if (
                decryptedMessage.status === 200 ||
                (decryptedMessage.status === 404 && decryptedMessage.data === 'not in process.')
            )
                responseRQ.status = true;
        } else responseRQ.data = decryptedMessage.message;

        return responseRQ;
    }

    responseRQ = await tryThis();
    if (tryStatus && responseRQ.data === 'Expired JWT Token' && !responseRQ.status) {
        const responseRefresh = await refreshToken(localStorage.getItem(api.tokens.refresh));
        token = responseRefresh.data.token;
        responseRQ.tokens = responseRefresh.data;
        await tryThis();
    }

    return responseRQ;
}

async function get_blank(url, api, responseRQ) {
    try {
        const responseAPI = await fetch(url, {
            method: 'GET',
            mode: 'cors',
        });

        const responseData = await responseAPI.json();

        let decryptedMessage = api.ENCRYPTION.decrypt(responseData, api.ENCRYPTION_KEY);
        decryptedMessage = JSON.parse(decodeURIComponent(decryptedMessage));

        responseRQ.status = false;
        responseRQ.data = decryptedMessage.message;

        if (responseAPI.ok) {
            responseRQ.data = decryptedMessage.data;
            if (decryptedMessage.status === 200) responseRQ.status = true;
        } else responseRQ.data = decryptedMessage.message;
    } catch (error) {
        responseRQ.data = error;
    }

    return responseRQ;
}

export { delete_token, post_blank, post_token, post_obj, get_token, get_blank };
