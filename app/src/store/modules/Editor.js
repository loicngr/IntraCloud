const Editor = {
    state: {
        blob: null,
        mime: null,
        location: null,
        server: null,
    },
    mutations: {
        setEditorFile(state, { blob, mime, location, server }) {
            state.blob = blob;
            state.mime = mime;
            state.location = location;
            state.server = server;
        },
    },
    actions: {
        setEditorFile({ commit }, { blob, mime, location, server }) {
            commit('setEditorFile', { blob, mime, location, server });
            return new Promise((resolve) => {
                setTimeout(() => {
                    resolve();
                }, 500);
            });
        },
    },
    getters: {
        getEditorFile: (state) => {
            if (!state.blob || !state.mime || !state.location || !state.server) {
                return false;
            }

            return { blob: state.blob, mime: state.mime, location: state.location, server: state.server };
        },
    },
};

export { Editor };
