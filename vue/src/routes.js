import Vue from 'vue';
import Router from 'vue-router';

Vue.use(Router);

import Master from "./components/layout/Master";
import Login from "./components/auth/Login";
import Account from './components/account/Account'
import Transaction from './components/transaction/Transaction'
import Branch from './components/branch/Branch'
import AssignAccount from './components/account/AssignAccount'
import Expense from './components/expense/Expense'
import NotFound from './components/not-found/NotFound'
import Analytic from './components/analytics/Analytic'
import Rojmel from './components/analytics/Rojmel'

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
                path: '/analytics',
                name: 'analytics',
                component: Analytic,
                meta: {
                    guarded : true
                }
            },
            {
                path: '/rojmel',
                name: 'rojmel',
                component: Rojmel,
                meta: {
                    guarded : true
                }
            },
            {
                path: '/branches',
                name: 'branches',
                component: Branch,
                meta: {
                    guarded : true
                }
            },
            {
                path: '/accounts',
                name: 'accounts',
                component: Account,
                meta: {
                    guarded : true
                }
            },
            {
                path: '/transactions',
                name: 'transactions',
                component: Transaction,
                meta: {
                    guarded : true
                }
            },
            {
                path: '/assign-account',
                name: 'assign-account',
                component: AssignAccount,
                meta: {
                    guarded : true
                }
            },
            {
                path: '/expense',
                name: 'expense',
                component: Expense,
                meta: {
                    guarded : true
                }
            }
        ]
    },
    {
      path: '/:notFound(.*)',
      component: NotFound
    }

];
const router = new Router({
    routes: routes,
    mode: 'history'

});

router.beforeEach((to, from, next) => {
    if (!Object.prototype.hasOwnProperty.call(to.meta, 'guarded')) next();
    else if(localStorage.getItem('access_token')){
        next();
    }
    else next({ path: '/' });
})

export default router;
