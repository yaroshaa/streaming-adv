import axios from 'axios';

const HOLIDAY_EVENT_ENDPOINT = '/settings/holiday-event/';

/**
 * @return {Promise}
 */
export function getHolidayEvents() {
    return axios.get(HOLIDAY_EVENT_ENDPOINT);
}

/**
 * @param {Object} params
 * @return {Promise}
 */
export function postHolidayEvent(params) {
    return axios.post(HOLIDAY_EVENT_ENDPOINT, params);
}

/**
 * @param {number} id
 * @param {Object} params
 * @return {Promise}
 */
export function putHolidayEvent(id, params) {
    return axios.put(HOLIDAY_EVENT_ENDPOINT + id, params);
}

/**
 * @param {number} id
 * @return {Promise}
 */
export function deleteHolidayEvent(id) {
    return axios.delete(HOLIDAY_EVENT_ENDPOINT + id);
}
