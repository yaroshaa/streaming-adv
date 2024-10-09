import axios from 'axios';
import qs from 'qs';

/**
 * Fetch data for store today page.
 * @param params
 * @returns {Promise<AxiosResponse<T>>}
 */
export function getStoreData(params) {
    return axios.get('/store-today/data', {
        params: params,
        paramsSerializer: (params) => {
            return qs.stringify(params);
        },
    });
}
