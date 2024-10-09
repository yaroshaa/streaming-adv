import filter from '@/models/filters';

export default {
    namespaced: true,
    state: () => {
        return {
            currency: {
                id: 3,
                code: 'EUR',
                name: 'Euro',
            },
            filter: {...filter},
            backup: null,
        };
    },
    mutations: {
        updateMarkets(state, item) {
            state.filter.markets = item;
        },
        updateSources(state, item) {
            state.filter.sources = item;
        },
        updateOrderStatuses(state, item) {
            state.filter.orderStatuses = item;
        },
        updateCurrency(state, item) {
            state.currency = item;
        },
        updatePercentile(state, item) {
            state.filter.percentile = item;
        },
        setLocked(state, value) {
            state.filter.locked = value;
        },
        updateProduct(state, item) {
            state.filter.product = item;
        },
        updateWeight(state, item) {
            state.filter.weight = item;
        },
        updateDate(state, item) {
            state.filter.date = item;
        },
        updateSingleDate(state, item) {
            state.filter.singleDate = item;
        },
        updateDateGranularity(state, item) {
            state.filter.dateGranularity = item;
        },
        updateKpiOverviewActiveFields(state, item) {
            state.filter.kpiOverviewActiveFields = item;
        },
        updateSearch(state, item) {
            state.filter.search = item;
        },
    },
    actions: {
        updateMarkets({commit}, item) {
            commit('updateMarkets', item);
        },
        updateSources({commit}, item) {
            commit('updateSources', item);
        },
        updateOrderStatuses({commit}, item) {
            commit('updateOrderStatuses', item);
        },
        updateCurrency({commit}, item) {
            commit('updateCurrency', item);
        },
        updatePercentile({commit}, item) {
            commit('updatePercentile', item);
        },
        updateProduct({commit}, item) {
            commit('updateProduct', item);
        },
        updateWeight({commit}, item) {
            commit('updateWeight', item);
        },
        backupFilters({commit}) {
            commit('backupFilters');
        },
        restoreFilters({commit}) {
            commit('restoreFilters');
        },
        resetProduct({commit}) {
            commit('updateProduct', null);
        },
        updateDate({commit}, item) {
            commit('updateDate', item);
        },
        updateSingleDate({commit}, item) {
            commit('updateSingleDate', item);
        },
        updateDateGranularity({commit}, item) {
            commit('updateDateGranularity', item);
        },
        updateKpiOverviewActiveFields({commit}, item) {
            commit('updateKpiOverviewActiveFields', item);
        },
        updateSearch({commit}, item) {
            commit('updateSearch', item);
        },
    },
    getters: {
        markets: (state) => state.filter.markets,
        sources: (state) => state.filter.sources,
        orderStatuses: (state) => state.filter.orderStatuses,
        currency: (state) => state.currency,
        percentile: (state) => state.filter.percentile,
        isLocked: (state) => state.filter.locked,
        product: (state) => state.filter.product,
        weight: (state) => state.filter.weight,
        date: (state) => state.filter.date,
        singleDate: (state) => state.filter.singleDate,
        dateGranularity: (state) => state.filter.dateGranularity,
        kpiOverviewActiveFields: (state) => state.filter.kpiOverviewActiveFields,
        search: (state) => state.filter.search,
    },
};
