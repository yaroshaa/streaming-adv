import axios from 'axios';
import qs from 'qs';

/**
 * Fetch kpi overview totals
 * @param {Object} params [{filters: {...}}]
 * @return {Promise}
 */
export function getKpiTotals(params) {
    return axios.get('/kpi-overview/totals', {
        params: params,
        paramsSerializer: (params) => {
            return qs.stringify(params);
        },
    });
}
