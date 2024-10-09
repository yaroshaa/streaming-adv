import MarketingOverview from '@/element/components/MarketingOverview/MarketingOverview';
import HolidayEventPage from '@/element/components/MarketingOverview/Settings/HolidayEventPage';

export default [
    {
        path: 'marketing-overview',
        name: 'marketing-overview',
        component: MarketingOverview,
    },
    {
        path: 'marketing-overview/holiday-event',
        name: 'marketing-overview.holiday-event',
        component: HolidayEventPage,
    },
];
