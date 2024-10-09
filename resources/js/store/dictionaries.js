export default {
    namespaced: true,
    state: () => {
        return {
            markets: [],
        };
    },
    mutations: {
        updateMarkets(state, item) {
            state.markets = item;
        },
    },
    actions: {
        updateMarkets({commit}, item) {
            commit('updateMarkets', item);
        },
    },
    getters: {
        markets: (state) => state.markets,
    },
};
