import Vue from 'vue';
import Router from 'vue-router';
Vue.use(Router);

import Master from "./components/Master";
import Login from "./components/Login";

let routes = [
    {
        path: '/',
        name: 'login',
        component: Login
    },
    {
        path: '/dashbord',
        name: 'master',
        component: Master
    },
];
const router = new Router({
    routes: routes,
    mode: 'history'

});
export default router;
