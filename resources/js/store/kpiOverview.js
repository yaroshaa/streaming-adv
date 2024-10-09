export default {
    namespaced: true,
    state: () => {
        return {
            data: [],
            dataBackup: [],
        };
    },
    mutations: {
        setData(state, data) {
            state.data = data;
        },
        compare(state, selected) {
            if (state.dataBackup.length === 0) {
                state.dataBackup = [...state.data];
            }

            state.data = selected;
        },
        resetCompare(state) {
            if (state.dataBackup.length > 0) {
                state.data = [...state.dataBackup];
                state.dataBackup = [];
            }
        },
    },
    actions: {
        setData({commit}, items) {
            commit('setData', items);
        },
        compare({commit}, selected) {
            commit('compare', selected);
        },
        resetCompare({commit}) {
            commit('resetCompare');
        },
    },
    getters: {
        data: (state) => state.data,
        isCompared: (state) => state.dataBackup.length > 0,
    },
};
