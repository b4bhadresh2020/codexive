import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

import Master from "./components/layout/Master";
import Login from "./components/auth/Login";
import Account from './components/account/Account'
import Transaction from './components/transaction/Transaction'
import Branch from './components/branch/Branch'
import AssignAccount from './components/account/AssignAccount'

let routes = [
    {
        path: '/',
        name: 'login',
        component: Login
    },
    {
        path: '/dashbord',
        name: 'master',
        component: Master,
        children: [
            {
                path: '/branches',
                name: 'branches',
                component: Branch
            },
            {
                path: '/accounts',
                name: 'accounts',
                component: Account
            },
            {
                path: '/transactions',
                name: 'transactions',
                component: Transaction
            },
            {
                path: '/assign-account',
                name: 'assign-account',
                component: AssignAccount
            }
        ]
    },

];
const router = new Router({
    routes: routes,
    mode: 'history'

});
export default router;
