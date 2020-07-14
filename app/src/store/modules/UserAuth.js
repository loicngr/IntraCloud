const LOGIN = 'LOGIN';
const LOGIN_SUCCESS = 'LOGIN_SUCCESS';
const LOGOUT = 'LOGOUT';

const UserAuth = {
    state: {
        isLoggedIn: !!localStorage.getItem('user_token'),
        userToken: {
            token: localStorage.getItem('user_token'),
            refresh: localStorage.getItem('user_refresh_token'),
        },
        user: {
            id: null,
            email: null,
            firstname: null,
            lastnale: null,
            roles: [],
        },
    },
    mutations: {
        setUser(state, user) {
            state.user = user;
        },
        setEmail(state, email) {
            state.user.email = email;
        },
        setToken(state, tokens) {
            state.userToken.token = tokens.token;
            state.userToken.refresh = tokens.refresh_token;
            localStorage.setItem('user_token', tokens.token);
            localStorage.setItem('user_refresh_token', tokens.refresh_token);
        },
        [LOGIN](state, token, refreshToken) {
            state.pending = true;
        },
        [LOGIN_SUCCESS](state) {
            state.isLoggedIn = true;
            state.pending = false;
        },
        [LOGOUT](state) {
            state.isLoggedIn = false;
        },
    },
    actions: {
        setUser({ commit }, user) {
            commit('setUser', user);
        },
        setEmail({ commit }, email) {
            commit('setEmail', email);
        },
        setToken({ commit }, tokens) {
            commit('setToken', tokens);
        },
        login({ commit }, creds) {
            commit(LOGIN, creds.token, creds.refreshToken);
            commit('setToken', { token: creds.token, refresh_token: creds.refreshToken });
            return new Promise((resolve) => {
                setTimeout(() => {
                    commit(LOGIN_SUCCESS);
                    resolve();
                }, 1000);
            });
        },
        logout({ commit }) {
            localStorage.removeItem('user_token');
            localStorage.removeItem('user_refresh_token');
            commit(LOGOUT);
        },
    },
    getters: {
        isLoggedIn: (state) => {
            return state.isLoggedIn;
        },
        token: (state) => {
            return state.userToken.token !== null ? state.userToken.token : false;
        },
        refreshToken: (state) => {
            return state.userToken.refresh !== null ? state.userToken.refresh : false;
        },
        isAdmin: (state) => {
            return state.user.roles.includes('ROLE_ADMIN') || state.user.roles.includes('ROLE_SUPER_ADMIN');
        },
        isVerified: (state) => {
            return !state.user.roles.includes('ROLE_NOT_VERIFIED');
        },
        user: (state) => {
            return state.user;
        },
    },
};

export { UserAuth };
