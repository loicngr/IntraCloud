const StartUp = {
    state: {
        server: localStorage.getItem('startup_server') === null ? -1 : parseInt(localStorage.getItem('startup_server')),
    },
    mutations: {
        setSTUP_Server(state, value) {
            state.server = parseInt(value);
        },
    },
    actions: {
        setSTUP_Server({ commit }, value) {
            localStorage.setItem('startup_server', value);
            commit('setSTUP_Server', value);
        },
    },
    getters: {
        STUP_server: (state) => {
            return state.server;
        },
    },
};

export { StartUp };
