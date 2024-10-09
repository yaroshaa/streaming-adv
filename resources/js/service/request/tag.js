import axios from 'axios';

const TAG_ENDPOINT = '/settings/tag/';

/**
 * @return {Promise}
 */
export function getTags() {
    return axios.get(TAG_ENDPOINT);
}

/**
 * @param {Object} params
 * @return {Promise}
 */
export function postTag(params) {
    return axios.post(TAG_ENDPOINT, params);
}

/**
 * @param {number} id
 * @param {Object} params
 * @return {Promise}
 */
export function putTag(id, params) {
    return axios.put(TAG_ENDPOINT + id, params);
}

/**
 * @param {number} id
 * @return {Promise}
 */
export function deleteTag(id) {
    return axios.delete(TAG_ENDPOINT + id);
}
