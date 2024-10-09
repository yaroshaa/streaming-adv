import settings from '@/models/settings';

export default {
    namespaced: true,
    state: () => settings,
    mutations: {
        switchMenu(state) {
            state.menuIsCollapsed = !state.menuIsCollapsed;
        },
    },
    actions: {
        switchMenu({commit}, item) {
            commit('switchMenu', item);
        },
    },
    getters: {
        menuIsCollapsed: (state) => state.menuIsCollapsed,
    },
};
