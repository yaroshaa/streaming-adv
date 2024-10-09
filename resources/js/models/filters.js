import moment from 'moment';

const filters = {
    markets: [],
    sources: [],
    orderStatuses: [],
    orderRemoteId: null,
    percentile: null,
    locked: false,
    product: {},
    weight: null,
    date: [moment().subtract(1, 'month').startOf('day').toDate(), moment().endOf('day').toDate()],
    singleDate: new Date(),
    dateGranularity: 'Daily',
    kpiOverviewActiveFields: null,
    search: null,
};

export default filters;
