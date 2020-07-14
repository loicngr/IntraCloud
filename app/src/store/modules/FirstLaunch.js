const FirstLaunch = {
    state: {
        isFirstLaunch: localStorage.getItem('tuto_firstLaunch') === null ? true : false,
        steps: {
            names: [
                'leftNavbar_Navigation',
                'page_home_server_connection',
            ],
            index: -1,
        },
    },
    mutations: {
        firstLaunch(state, value) {
            state.isFirstLaunch = value;
        },
        nextStep(state) {
            state.steps.index = state.steps.index + 1;
        },
    },
    actions: {
        setFirstLaunch({ commit }, value) {
            commit('firstLaunch', value);
        },
        nextStep({ state, commit }) {
            let stepIndex = state.steps.index;
            if (stepIndex + 1 < state.steps.names.length) {
                commit('nextStep');
                return state.steps.names[stepIndex + 1];
            } else {
                localStorage.setItem('tuto_firstLaunch', false);
                state.isFirstLaunch = false;
                return false;
            }
        },
    },
    getters: {
        isFirstLaunch: (state) => {
            return state.isFirstLaunch;
        },
    },
};

export { FirstLaunch };
