/* prettier-ignore */
/* eslint-disable */

import store from "@/storage/store";

const MarketsClass = function () {
    let _marketsPromise;
    return {
        async receive() {
            if (_marketsPromise) {
                return _marketsPromise;
            }

            _marketsPromise = new Promise((resolve, reject) => {
                if (store.getters['dictionaries/markets'].length === 0) {
                    axios.get('markets').then((response) => {
                        store.dispatch('dictionaries/updateMarkets', response.data);
                        _marketsPromise = null;
                        resolve(store.getters['dictionaries/markets']);
                    });

                    return;
                }

                _marketsPromise = null;
                resolve(store.getters['dictionaries/markets']);
            });

            return _marketsPromise;
        },
    };
};

const Markets = new MarketsClass();

export default Markets;
