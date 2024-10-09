import Vuex from 'vuex';
import filters from '@/store/filters';
import feedbacks from '@/store/feedbacks';
import orders from '@/store/orders';
import kpiOverview from '@/store/kpiOverview';
import products from '@/store/products';
import marketingOverview from '@/store/marketingOverview';
import settings from '@/store/settings';
import dictionaries from '@/store/dictionaries';
import storeToday from '@/store/storeToday';

import Vue from 'vue';

Vue.use(Vuex);

const store = new Vuex.Store({
    modules: {
        filters,
        feedbacks,
        orders,
        kpiOverview,
        products,
        marketingOverview,
        settings,
        dictionaries,
        storeToday,
    },
});

export default store;
