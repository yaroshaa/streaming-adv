import axios from 'axios';

const FEEDBACK_ENDPOINT = '/feedbacks/';

/**
 * @param {Object} params
 * @return {Promise}
 */
export function getFeedbacks(params) {
    return axios.get(FEEDBACK_ENDPOINT, params);
}

/**
 * @param {Object} params
 * @return {Promise}
 */
export function postFeedback(params) {
    return axios.post(FEEDBACK_ENDPOINT, params);
}
