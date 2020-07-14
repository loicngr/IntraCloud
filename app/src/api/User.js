'use strict';

import { Api } from './Api.js';
import { removeLastChar } from '../utils/functions';
import { delete_token, get_blank, get_token, post_blank, post_obj, post_token } from './utils/fetchRequests';

/**
 * Classe Utilisateur
 *
 * Le Token peut signifier :
 *  - JWT (Json Web Token) - La clé que Symfony génère pour reconnaître l'utilisateur
 *  - Le mot de passe temporaire créé par Symfony lors d'une réinitialisation d'un mot de passe
 */
class User {
    endpoint = {
        auth: 'login_check/',
        new: 'user/',
        user: 'user/',
        users: 'users/',
        id: '{ID}/',
        email: 'email/{EMAIL}/',
        me: 'me/',
        update: 'update/',
        updateResetPassword: 'resetpassword/{TOKEN}/',
        updatePassword: 'password/',
        updateEmail: 'email/',
        resetPassword: 'password/reset/',
        token: {
            base: 'token/',
            new: '/',
            checkValidResetToken: 'generated/{TOKEN}/',
            delete: '{ID}/',
            inProcess: 'check/resetpassword/',
        },
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
     * Connexion d'un utilisateur
     * @param {string} email
     * @param {string} password
     * @return {Promise<{data: string|{}, status: boolean}>}
     */
    async auth(email, password) {
        let responseRQ = {
            status: false,
            data: 'Email Or Password not correct.',
        };

        if (email && password) {
            const endpoint = removeLastChar(this.endpoint.auth);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await post_obj(url, { email: email, password: password }, responseRQ);
        }

        return responseRQ;
    }

    /**
     * Récupérer tous les utilisateurs
     * @param {string} token
     * @return {Promise<{data: string|{}, tokens: null|{}, status: boolean}>}
     */
    async getAll(token) {
        if (token) {
            const endpoint = removeLastChar(this.endpoint.users);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            this.responseRQ = await get_token(url, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Récupérer un utilisateur avec son ID
     * @param {number} id
     * @param {string} token
     * @return {Promise<{data: string|{}, tokens: null|{}, status: boolean}>}
     */
    async userByID(id, token) {
        if (id && token) {
            let endpoint = this.endpoint.user;
            endpoint = endpoint.replace('{ID}', id);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            this.responseRQ = await get_token(url, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Récupérer un utilisateur avec son email
     * @param {string} email
     * @param {string} token
     * @return {Promise<{data: string|{}, tokens: null|{}, status: boolean}>}
     */
    async byEmail(email, token) {
        if (email && token) {
            let endpoint = this.endpoint.user + removeLastChar(this.endpoint.email);
            endpoint = endpoint.replace('{EMAIL}', email);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            this.responseRQ = await get_token(url, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Retourne l'utilisateur grace au JWT
     * @param {string} token (Jwt)
     * @return {Promise<{data: string|{}, tokens: null|{}, status: boolean}>}
     */
    async me(token) {
        if (token) {
            const endpoint = removeLastChar(this.endpoint.me);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            this.responseRQ = await get_token(url, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Créé un nouvel utilisateur
     * @param {string} firstname
     * @param {string} lastname
     * @param {string} email
     * @param {string} password
     * @return {Promise<{data: string|{}, status: boolean}>}
     */
    async create(firstname, lastname, email, password) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
        };

        if (firstname && lastname && email && password) {
            const endpoint = removeLastChar(this.endpoint.new);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            formData.append('firstname', firstname);
            formData.append('lastname', lastname);
            formData.append('email', email);
            formData.append('password', password);

            responseRQ = await post_blank(url, formData, this.API, responseRQ);
        }

        return responseRQ;
    }

    /**
     * Vérifie si la demande de réinitialisation existe + valide
     * @param {string} token
     * @return {Promise<{data: string|{}, status: boolean}>}
     */
    async checkValidResetToken(token) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
        };

        if (token) {
            let endpoint = this.endpoint.token.base + removeLastChar(this.endpoint.token.checkValidResetToken);
            endpoint = endpoint.replace('{TOKEN}', token);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            responseRQ = await get_blank(url, this.API, responseRQ);
        }

        return responseRQ;
    }

    /**
     * Si l'utilisateur se connect à son compte tout en ayant une
     * demande pour réinitialiser son mot de passe active.
     * @param {string} token
     * @return {Promise<{data: string|{}, tokens: null|{}, status: boolean}>}
     */
    async resetPasswordProcess(token) {
        if (token) {
            const endpoint = this.endpoint.user + removeLastChar(this.endpoint.token.inProcess);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            this.responseRQ = await get_token(url, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    async createToken(token) {
        if (token) {
            const endpoint = this.endpoint.token.base + removeLastChar(this.endpoint.token.new);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            this.responseRQ = await get_token(url, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Supprime une demande de réinitialisation
     * @param {number} id
     * @param {string} token
     * @return {Promise<{data: string|{}, tokens: null|{}, status: boolean}>}
     */
    async deletePasswordToken(id, token) {
        if (id && token) {
            let endpoint = this.endpoint.token.base + removeLastChar(this.endpoint.token.delete);
            endpoint = endpoint.replace('{ID}', id.toString());
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            this.responseRQ = await delete_token(url, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Supprime un utilisateur
     * @param {number} id
     * @param {string} token
     * @return {Promise<{data: string|[], tokens: null|[], status: boolean}>}
     */
    async deleteUser(id, token) {
        if (id && token) {
            let endpoint = this.endpoint.user + removeLastChar(this.endpoint.id);
            endpoint = endpoint.replace('{ID}', id.toString());
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            this.responseRQ = await delete_token(url, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Met à jour le mot de passe via la page de réinitialisation du mot de passe
     * @param {string} token (Mot de passe généré par Symfony)
     * @param {string} password
     * @param {string} email
     * @return {Promise<{data: string|{}, tokens: null|{}, status: boolean}>}
     */
    async updateResetPassword(token, password, email) {
        if (token && password && email) {
            let endpoint =
                this.endpoint.user + this.endpoint.update + removeLastChar(this.endpoint.updateResetPassword);
            endpoint = endpoint.replace('{TOKEN}', token);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            formData.append('password', password);
            formData.append('email', email);

            this.responseRQ = await post_blank(url, formData, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Mise à jour d'un utilisateur
     * @param {{
     *     id: null|number,
     *     firstname: null|string,
     *     lastname: null|string,
     *     email: null|string,
     *     role: null|string,
     *     password: null|string
     * }} user
     * @param {string} token
     * @return {Promise<{data: string|[], tokens: null|[], status: boolean}>}
     */
    async updateUser(user, token) {
        if (token && user.id) {
            let endpoint = this.endpoint.user + this.endpoint.update + removeLastChar(this.endpoint.id);
            endpoint = endpoint.replace('{ID}', user.id.toString());
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            if (user.firstname !== null) formData.append('firstname', user.firstname);
            if (user.lastname !== null) formData.append('lastname', user.lastname);
            if (user.email !== null) formData.append('email', user.email);
            if (user.role !== null) formData.append('role', user.role);
            if (user.password !== null) formData.append('password', user.password);

            this.responseRQ = await post_token(url, formData, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Mise à jour du mot de passe Utilisateur
     * @param {number} id
     * @param {string} current_password
     * @param {string} new_password
     * @param {string} token
     * @return {Promise<{data: string|[], tokens: null|[], status: boolean}>}
     */
    async updatePassword(id, current_password, new_password, token) {
        if (
            token &&
            id &&
            current_password &&
            current_password.length >= 3 &&
            new_password &&
            new_password.length >= 5
        ) {
            let endpoint =
                this.endpoint.user +
                this.endpoint.update +
                this.endpoint.id +
                removeLastChar(this.endpoint.updatePassword);
            endpoint = endpoint.replace('{ID}', id.toString());
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            formData.append('current', current_password);
            formData.append('new', new_password);

            this.responseRQ = await post_token(url, formData, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Mise à jour de l'email utilisateur
     * @param {number} id
     * @param {string} current_email
     * @param {string} new_email
     * @param {string} token
     * @return {Promise<{data: string|[], tokens: null|[], status: boolean}>}
     */
    async updateEmail(id, current_email, new_email, token) {
        if (token && id && current_email && current_email.length >= 7 && new_email && new_email.length >= 7) {
            let endpoint =
                this.endpoint.user +
                this.endpoint.update +
                this.endpoint.id +
                removeLastChar(this.endpoint.updateEmail);
            endpoint = endpoint.replace('{ID}', id.toString());

            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            formData.append('current', current_email);
            formData.append('new', new_email);

            this.responseRQ = await post_token(url, formData, token, this.API, this.responseRQ);
        }

        return this.responseRQ;
    }

    /**
     * Demande de réinitialisation d'un mot de passe
     * @param {string} email
     * @return {Promise<{data: string|[], status: boolean}>}
     */
    async resetPassword(email) {
        let responseRQ = {
            status: false,
            data: 'Parameters not correct.',
        };

        if (email) {
            const endpoint = this.endpoint.user + removeLastChar(this.endpoint.resetPassword);
            const { adresse, protocol } = this.API;
            const url = `${protocol}://${adresse}/${endpoint}`;

            const formData = new FormData();
            formData.append('email', email);

            responseRQ = await post_blank(url, formData, this.API, responseRQ);
        }

        return responseRQ;
    }
}

export { User };
