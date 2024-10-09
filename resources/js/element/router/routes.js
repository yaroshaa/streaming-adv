import AuthPage from '@/element/components/Auth/AuthPage';
import GenerateMock from '@/element/components/GenerateMock';
import AccessDenied from '@/element/components/AccessDenied';
import Main from '@/element/components/Main';
import Login from '@/element/components/Auth/Login';
import Register from '@/element/components/Auth/Register';
import GAuthPostback from '@/element/components/Auth/GAuthPostback';
import WarehousesList from '@/../../modules/MarketingOverview/js/tailwind/WarehousePackingStation/WarehousesList';

let dashboardRoutes = [
    {
        path: 'warehouse-packing-station',
        name: 'warehouse-packing-station',
        component: WarehousesList,
    },
];

/// Register routes from components/{name}/routes.js
let componentRoutes = require.context('@/element/components/', true, /routes.js/);
componentRoutes.keys().forEach((filename) => {
    dashboardRoutes = dashboardRoutes.concat(require(`@/element/components/${filename.replace('./', '')}`).default);
});

const routes = [
    {
        path: '/',
        redirect: {
            name: 'orders',
        },
    },
    {
        path: '/g-auth-postback',
        meta: {
            auth: false,
        },
        component: GAuthPostback,
    },
    {
        component: AuthPage,
        path: 'auth',
        meta: {
            auth: false,
        },
        children: [
            {
                path: '/login',
                name: 'login',
                component: Login,
            },
            {
                path: '/register',
                name: 'register',
                component: Register,
            },
        ],
    },
    {
        path: '/generate-mock',
        component: GenerateMock,
    },
    {
        path: '/access-denied',
        component: AccessDenied,
        meta: {
            auth: true,
        },
    },
    {
        path: '/dashboard',
        component: Main,
        meta: {
            auth: true,
        },
        children: dashboardRoutes,
    },
];

export default routes;
