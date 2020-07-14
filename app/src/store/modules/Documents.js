const DocumentsServer = {
    state: {
        documents: [],
        server: ''
    },
    mutations: {
        setDocuments(state, { documents, server }) {
            state.documents = documents;
            state.server = server;
        },
    },
    actions: {
        setDocuments({ commit }, { documents, server }) {
            commit('setDocuments', { documents, server });
        },
    },
    getters: {
        documentsServer: (state) => {
            return { documents: state.documents, server: state.server };
        },
    },
};

export { DocumentsServer };
