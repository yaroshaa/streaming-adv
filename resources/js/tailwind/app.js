require('./../bootstrap');

import Vue from 'vue';
import lang from 'element-ui/lib/locale/lang/en';
import locale from 'element-ui/lib/locale';
import App from './App.vue';
import router from '@/../../modules/MarketingOverview/js/tailwind/router';
import axios from 'axios';
import VueAxios from 'vue-axios';
import auth from '@websanova/vue-auth';
import authConfig from '@/auth/index';
import * as VueGoogleMaps from 'vue2-google-maps';
import store from '@/storage/store';
import EchoServer from '@/socket/socket';

axios.defaults.headers.common['Accept'] = 'application/json';
axios.defaults.baseURL = '/api/';

Vue.use(VueAxios, axios);
Vue.use(auth, authConfig);

locale.use(lang);
Vue.use(require('vue-moment'));

Vue.prototype.$isUserVerified = () => this.$auth && this.$auth.user() && this.$auth.user().verified;

EchoServer.install = function (Vue) {
    Vue.prototype.$echo = EchoServer;
};

Vue.use(EchoServer);

Vue.use(VueGoogleMaps, {
    load: {
        key: 'AIzaSyDTZPZNM6zmomH6ONB8YHKeoY-vV2RgYZw',
        libraries: 'places', // This is required if you use the Autocomplete plugin
    },
    installComponents: true,
});

window.Vue = new Vue({
    render: (h) => h(App),
    router,
    store,
}).$mount('#app');
