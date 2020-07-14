const CryptoJS = require('crypto-js');

// Source : https://gist.github.com/ve3/0f77228b174cf92a638d81fddb17189d

class Encryption {
    /**
     * Retourne le nombre de la méthode Cipher (128, 192, 256)
     *
     * @return {number} aesNumber
     * */
    get encryptMethodLength() {
        const encryptMethod = this.encryptMethod;
        const aesNumber = encryptMethod.match(/\d+/)[0];
        return parseInt(aesNumber);
    }

    /**
     * Retourne la méthode Cipher divisé par 8
     *
     * @example
     * - 32 Car 256 / 8
     *
     * @return {number}
     * */
    get encryptKeySize() {
        const aesNumber = this.encryptMethodLength;
        return aesNumber / 8;
    }

    /**
     * Méthode d'encryption
     * */
    get encryptMethod() {
        return 'AES-256-CBC';
    }

    /**
     * Décrypte une chaine de caractère
     * @param {string} encryptedString
     * @param {string} key
     * @returns {string}
     */
    decrypt(encryptedString, key) {
        const json = JSON.parse(CryptoJS.enc.Utf8.stringify(CryptoJS.enc.Base64.parse(encryptedString)));

        const salt = CryptoJS.enc.Hex.parse(json.salt);
        const iv = CryptoJS.enc.Hex.parse(json.iv);

        const encrypted = json.ciphertext;

        let iterations = parseInt(json.iterations);
        if (iterations <= 0) iterations = 999;

        const encryptMethodLength = this.encryptMethodLength / 4;
        const hashKey = CryptoJS.PBKDF2(key, salt, {
            hasher: CryptoJS.algo.SHA512,
            keySize: encryptMethodLength / 8,
            iterations: iterations,
        });

        const decrypted = CryptoJS.AES.decrypt(encrypted, hashKey, { mode: CryptoJS.mode.CBC, iv: iv });

        let decryptedResponse;
        try {
            decryptedResponse = decrypted.toString(CryptoJS.enc.Utf8);
        } catch (e) {
            throw new Error(e);
        }

        return decryptedResponse;
    }

    /**
     * Crypter une chaine de caractère
     *
     * @param {string} string
     * @param {string} key
     * @return {string}
     */
    encrypt(string, key) {
        const iv = CryptoJS.lib.WordArray.random(16);

        const salt = CryptoJS.lib.WordArray.random(256);
        const iterations = 999;
        const encryptMethodLength = this.encryptMethodLength / 4;
        const hashKey = CryptoJS.PBKDF2(key, salt, {
            hasher: CryptoJS.algo.SHA512,
            keySize: encryptMethodLength / 8,
            iterations: iterations,
        });

        const encrypted = CryptoJS.AES.encrypt(string, hashKey, { mode: CryptoJS.mode.CBC, iv: iv });
        const encryptedString = CryptoJS.enc.Base64.stringify(encrypted.ciphertext);

        const output = {
            ciphertext: encryptedString,
            iv: CryptoJS.enc.Hex.stringify(iv),
            salt: CryptoJS.enc.Hex.stringify(salt),
            iterations: iterations,
        };

        return CryptoJS.enc.Base64.stringify(CryptoJS.enc.Utf8.parse(JSON.stringify(output)));
    }
}

export { Encryption };
