const WebSocket = {
    state: {
        socket: null,
    },
    mutations: {
        setSocket(state, socket) {
            state.socket = socket;
        },
    },
    actions: {
        setSocket({ commit }, socket) {
            commit('setSocket', socket);
        },
    },
    getters: {
        socket: (state) => {
            return state.socket;
        },
    },
};

export { WebSocket };
