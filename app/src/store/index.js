import Vue from 'vue';
import Vuex from 'vuex';

import { UserAuth } from './modules/UserAuth.js';
import { FirstLaunch } from './modules/FirstLaunch.js';
import { DocumentsServer } from './modules/Documents.js';
import { StartUp } from './modules/StartUp.js';
import { WebSocket } from './modules/WebSocket';
import { Editor } from './modules/Editor';

Vue.use(Vuex);

export default new Vuex.Store({
    state: {},
    mutations: {},
    actions: {},
    getters: {},
    modules: {
        UserAuth: UserAuth,
        FirstLaunch: FirstLaunch,
        DocumentsServer: DocumentsServer,
        StartUp: StartUp,
        Websocket: WebSocket,
        Editor: Editor,
    },
});
