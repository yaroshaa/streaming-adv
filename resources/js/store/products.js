export default {
    namespaced: true,
    state: () => {
        return {
            products: [],
            dynamic: [],
            product: {}
        };
    },

    mutations: {
        setProducts(state, products) {
            state.products = products;
        },
        setProductDynamic(state, dynamic) {
            state.dynamic = dynamic;
        },
        setCurrentProduct(state, product) {
            state.product = product;
        }
    },

    actions: {

        setProducts({commit}, items) {
            commit('setProducts', items);
        },
        setProductDynamic({commit}, items) {
            commit('setProductDynamic', items);
        },
        setCurrentProduct({commit}, product) {
            commit('setCurrentProduct', product);
        }
    },
    getters: {
        products: (state) => state.products,
        dynamic: (state) => state.dynamic,
        currentProduct: (state) => state.product
    },
};
