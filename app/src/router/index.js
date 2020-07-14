import Vue from 'vue';
import VueRouter from 'vue-router';

import Home from '../views/Home.vue';
import Help from '../views/Help.vue';
import Settings from '../views/Settings.vue';
import Login from '../views/identification/Login.vue';
import Logout from '../views/identification/Logout.vue';
import ForceLogout from '../views/identification/ForceLogout.vue';
import SignUp from '../views/identification/Signup.vue';
import LostPass from '../views/identification/Lostpass.vue';
import ResetPass from '../views/identification/Reset.vue';
import AdminUsers from '../views/admin/Users.vue';
import AdminServers from '../views/admin/Servers.vue';
import AdminDocuments from '../views/admin/Documents.vue';
import Editor from '../views/admin/Editor';

Vue.use(VueRouter);
/**
 * Chargement de la variable VUE_APP_SITE_TITLE du fichier .env
 * Si, elle n'est pas défini, alors assignment de "IntraCloud" comme valeur par défaut
 * */
const site_title = process.env.VUE_APP_SITE_TITLE || 'IntraCloud';

const routes = [
    {
        path: '/',
        name: 'Home',
        component: Home,
        meta: {
            title: `${site_title} - Accueil`,
            topbar: 'Accueil',
            requiresAuth: true,
            requiresAdmin: false,
            requiresVerified: true,
        },
    },
    {
        path: '/help',
        name: 'Help',
        component: Help,
        meta: {
            title: `${site_title} - Aide`,
            topbar: 'Aide',
            requiresAuth: true,
            requiresAdmin: false,
            requiresVerified: true,
        },
    },
    {
        path: '/settings',
        name: 'Settings',
        component: Settings,
        meta: {
            title: `${site_title} - Paramètres`,
            topbar: 'Paramètres',
            requiresAuth: true,
            requiresAdmin: false,
            requiresVerified: true,
        },
    },
    {
        path: '/login',
        name: 'Login',
        component: Login,
        meta: {
            title: `${site_title} - Connexion`,
            requiresAuth: false,
            requiresAdmin: false,
            requiresVerified: true,
        },
    },
    {
        path: '/logout',
        name: 'Logout',
        component: Logout,
        meta: {
            title: `${site_title} - Déconnexion`,
            requiresAuth: true,
            requiresAdmin: false,
            requiresVerified: true,
        },
    },
    {
        path: '/forcelogout',
        name: 'ForceLogout',
        component: ForceLogout,
        meta: {
            title: `${site_title} - Déconnexion`,
            requiresAuth: true,
            requiresAdmin: false,
            requiresVerified: true,
        },
    },
    {
        path: '/signup',
        name: 'Signup',
        component: SignUp,
        meta: {
            title: `${site_title} - Inscription`,
            requiresAuth: false,
            requiresAdmin: false,
            requiresVerified: false,
        },
    },
    {
        path: '/lostpass',
        name: 'Lostpass',
        component: LostPass,
        meta: {
            title: `${site_title} - Mot de passe Perdu`,
            requiresAuth: false,
            requiresAdmin: false,
            requiresVerified: false,
        },
    },
    {
        path: '/reset/:token',
        name: 'ResetPassword',
        component: ResetPass,
        meta: {
            title: `${site_title} - Changer de mot de Passe`,
            requiresAuth: false,
            requiresAdmin: false,
            requiresVerified: false,
        },
    },
    {
        path: '/admin',
        name: 'Admin',
        redirect: '/home',
        meta: {
            title: `${site_title} - Admin`,
            topbar: 'Administration',
            requiresAuth: true,
            requiresAdmin: true,
            requiresVerified: true,
        },
    },
    {
        path: '/admin/users',
        name: 'AdminUsers',
        component: AdminUsers,
        meta: {
            title: `${site_title} - Admin - Utilisateurs`,
            topbar: 'Utilisateurs - Administration',
            requiresAuth: true,
            requiresAdmin: true,
            requiresVerified: true,
        },
    },
    {
        path: '/admin/servers',
        name: 'AdminServers',
        component: AdminServers,
        meta: {
            title: `${site_title} - Admin - Serveurs`,
            topbar: 'Serveurs - Administration',
            requiresAuth: true,
            requiresAdmin: true,
            requiresVerified: true,
        },
    },
    {
        path: '/admin/documents',
        name: 'AdminDocuments',
        component: AdminDocuments,
        meta: {
            title: `${site_title} - Admin - Documents`,
            topbar: 'Documents - Administration',
            requiresAuth: true,
            requiresAdmin: true,
            requiresVerified: true,
        },
    },
    {
        path: '/admin/editor',
        name: 'AdminEditor',
        component: Editor,
        meta: {
            title: `${site_title} - Admin - Editor`,
            topbar: '',
            requiresAuth: true,
            requiresAdmin: true,
            requiresVerified: true,
        },
    },
];

const router = new VueRouter({
    mode: 'history',
    base: process.env.BASE_URL,
    routes,
});

export default router;
