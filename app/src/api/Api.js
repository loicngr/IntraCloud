import { Encryption } from './utils/Encryption';

/**
 * Classe API
 */
class Api {
    adresse = process.env.VUE_APP_API_BASE_IP;
    protocol = process.env.VUE_APP_API_BASE_PROTOCOL;
    tokens = {
        base: 'user_token',
        refresh: 'user_refresh_token',
    };

    ENCRYPTION = new Encryption();

    constructor() {
        this.check_env();
    }

    check_env() {
        if (!process.env.VUE_APP_ENCRYPTION_KEY || process.env.VUE_APP_ENCRYPTION_KEY.length === 0) {
            throw new Error('"VUE_APP_ENCRYPTION_KEY" variable is not defined!!!');
        }
        this.ENCRYPTION_KEY = process.env.VUE_APP_ENCRYPTION_KEY;
    }
}

export { Api };
