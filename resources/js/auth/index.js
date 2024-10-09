import authBearer from './bearer';
import httpAxios from '@websanova/vue-auth/dist/drivers/http/axios.1.x.esm.js';
import routerVueRouter from '@websanova/vue-auth/dist/drivers/router/vue-router.2.x.esm.js';

const authConfig = {
    auth: authBearer,
    http: httpAxios,
    router: routerVueRouter,
    rolesKey: 'role',
    authRedirect: () => (document.location.href = '/gauth'),
};

export default authConfig;
