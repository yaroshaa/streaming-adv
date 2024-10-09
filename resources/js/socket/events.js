export const ORDER_CHANNEL = 'laravel_database_order-channel';
export const ORDER_UPDATED_EVENT = '.order.updated';
export const FEEDBACK_CHANNEL = 'laravel_database_feedback-channel';
export const FEEDBACK_ADDED_EVENT = '.feedback.added';
export const ANALYTIC_CHANNEL = 'laravel_database_analytic-channel';
export const ANALYTIC_TRACK_EVENT = '.analytic.track';

let events = [
    {
        channel: ORDER_CHANNEL,
        event: ORDER_UPDATED_EVENT,
    },
    {
        channel: FEEDBACK_CHANNEL,
        event: FEEDBACK_ADDED_EVENT,
    },
    {
        channel: ANALYTIC_CHANNEL,
        event: ANALYTIC_TRACK_EVENT
    }
];

/// Register modules from components/{name}/modules.js
let modules = require.context('@/element/components/', true, /events.js/);

modules.keys().forEach((filename) => {
    events = events.concat(require(`@/element/components/${filename.replace('./', '')}`).default);
});

export default events;
