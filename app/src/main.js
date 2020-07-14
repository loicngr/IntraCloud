import Vue from 'vue';
import App from './App.vue';
import './registerServiceWorker';
import router from './router';
import store from './store';

/**
 * Plugin pour les formulaires
 * */
import FormFields from './plugins/formField.js';

Vue.config.productionTip = false;
Vue.config.errorHandler = function (err, vm, info) {
    throw new Error(info);
};

Vue.use(FormFields);

const $vm = new Vue({
    router,
    store,
    render: (h) => h(App),
}).$mount('#app');

window.$vm = $vm;
