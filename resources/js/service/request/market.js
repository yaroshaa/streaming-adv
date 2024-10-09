import axios from 'axios';

const MARKET_ENDPOINT = '/markets/';

/**
 * @return {Promise}
 */
export function getMarkets() {
    return axios.get(MARKET_ENDPOINT);
}
