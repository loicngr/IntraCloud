import { User } from '../../src/api/User';

const test_user = {
    email: 'admin@email.fr',
    password: 'password',
    tokens: {
        basic:
            'eyJ0eXAiOiJKV1QiLCJhbGciOiJSUzI1NiJ9.eyJpYXQiOjE1ODk3OTIzODcsImV4cCI6MTU4OTc5OTU4Nywicm9sZXMiOlsiUk9MRV9VU0VSIl0sInVzZXJuYW1lIjoiYWRtaW5AZW1haWwuZnIifQ.270nX5UJtcA-RLSrY4RzD5SN9SUNc6VuzhV20HhdkCALzstq7duv1x_1Bhq2wStjAKBjUTMc7IaK_a0qRPvGcPoad0YgiRzBVKg2xRBmsn6zqnCkR3va2Oy49LnDURfTVtdn2p3zuhJ_YjJYgGDzW1YQKGRH5BxFYVQxA8RC7ufVUMWaYKXbJxVwXzNINjp1_hwRp1QbAhz1692Igc6H7rB_HtZ-rwnERG1QT8rnCV52HnxTOUH2_BOZK6r2YgM0Ht6JsBcli_tFaSClT5IW7teRQoAX-yffHfJvg32i70SYlBVsTOlN3MYiB53lQaGl_iMzcVb538I6YedBez_UcDD4BQTpIXvijRrBYMHvPf3Z5QjPblpHIfsouhPcaFSvtmeFCGl8zNv1iaeljIVoZuz6HTKKDWNtJGoQuA5VLPnXNROO1qXGaJab7eMVOlvJjrFirLxdxdcct2X1HfwYO9UiQ0njQW7iELieDvB0gj9FZ-zb6MvPQpjsY6FwHHYAxtSRCTuEwZZ4PbWFdqe_3rXW0rUkHIQvSoqstw7PKog6QIBolKCY0iE1JSgRiBWf5ADZYffavutDJva6RXo1FkIKMWb-6Ta3AsW8OKDT7EUe-ogqfJ7DXqJrZvOT9jAoaMPZvLuhxty9XTqP4JLbkytRd_nmdFgsy58dzAu1K6Q',
        refresh:
            '38183fc9ef59f49f2c680b4d51e125435c0e5828f1f67eb3afdc6495a0e23ae0fba050752fcda48da33b3bf6c8274a4ba0a87c1cb12c6aa724d6cf9e4430b055',
    },
};
const userAPI = new User();

/**
 * Connexion d'un utilisateur
 * */
async function login() {
    const authUser = await userAPI.auth(test_user.email, test_user.password);
    return authUser;
}

export { login };
