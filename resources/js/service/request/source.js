import axios from 'axios';

const SOURCE_ENDPOINT = '/sources/';

/**
 * @return {Promise}
 */
export function getSources() {
    return axios.get(SOURCE_ENDPOINT);
}
