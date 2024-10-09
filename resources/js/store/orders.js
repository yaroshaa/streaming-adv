/* prettier-ignore */

const sortOrders = (a, b) => {
    const bm = new Date(b.updated_at);
    const am = new Date(a.updated_at);

    if (am.getTime() > bm.getTime()) {
        return -1;
    }

    return am.getTime() < bm.getTime() ? 1 : 0;
};

const filterIncome = (orders, income) => {
    const filtered = orders.filter((order) => !income.find((x) => Number(x.order_id) === Number(order.order_id)));

    return income.concat(filtered).sort(sortOrders);
};

export default {
    namespaced: true,
    state: () => {
        return {
            orders: [],
            ordersBackup: [],
        };
    },
    mutations: {
        addOrders(state, orders) {
            if (state.ordersBackup.length > 0) {
                state.ordersBackup = filterIncome(state.ordersBackup, orders);
            } else {
                state.orders = filterIncome(state.orders, orders);
                if (state.orders.length > 100) {
                    state.orders.splice(100);
                    console.log('clear feed');
                }
            }
        },
        setOrders(state, orders) {
            state.orders = orders.sort(sortOrders);
        },
        filter(state, filter) {
            if (state.ordersBackup.length === 0) {
                state.ordersBackup = [...state.orders];
            }

            state.orders = state.orders.filter(filter);
        },
        resetFilters(state) {
            if (state.ordersBackup.length > 0) {
                state.orders = [...state.ordersBackup];
                state.ordersBackup = [];
            }
        },
    },
    actions: {
        addOrders({commit}, items) {
            commit('addOrders', items);
        },
        setOrders({commit}, items) {
            commit('setOrders', items);
        },
        filter({commit}, filter) {
            commit('filter', filter);
        },
        resetFilters({commit}) {
            commit('resetFilters');
        },
    },
    getters: {
        orders: (state) => state.orders,
        isFiltered: (state) => state.ordersBackup.length > 0,
    },
};
