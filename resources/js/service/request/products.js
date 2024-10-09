import axios from 'axios';
import qs from 'qs';

/**
 * Fetch products list
 * @param {Object} params [{filters: {...}}]
 * @return {Promise}
 */
export function getProductsList(params) {
    return axios.get('/product-statistic', {
        params: params,
        paramsSerializer: (params) => {
            return qs.stringify(params);
        },
    });
}

/**
 * Fetch Product Data Dynamic
 * @param {Object} params [{filters: {...}}]
 * @param id
 * @return {Promise}
 */
export function getDataDynamic(params) {
    return axios.get('/product-statistic/data-dynamics', {
        params: params,
        paramsSerializer: (params) => {
            return qs.stringify(params);
        },
    });
}



