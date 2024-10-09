import storeToday from '@/models/storeToday';

export default {
    namespaced: true,
    state: () => {
        return {...storeToday};
    },
    mutations: {
        updateSrcData(state, data) {
            state.srcData = data;
        },

        updateData(state, data) {
            state.data = data;
        },
    },

    actions: {
        updateSrcData({commit}, data) {
            commit('updateSrcData', data);
        },

        updateData({commit}, data) {
            commit('updateData', data);
        },
    },

    getters: {
        getSrcData: (state) => state.srcData,
        getData: (state) => state.data,
    },
};
