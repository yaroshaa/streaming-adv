export default {
    namespaced: true,
    state: () => {
        return {
            data: [],
        };
    },
    mutations: {
        setData(state, data) {
            state.data = data;
        },
    },
    actions: {
        setData({commit}, items) {
            commit('setData', items);
        },
    },
    getters: {
        data: (state) => state.data,
    },
};
