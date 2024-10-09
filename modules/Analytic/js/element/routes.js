import Analytic from '@/element/components/Analytic/Analytic';
import TopEvents from '@/element/components/Analytic/TopEvents';
import Conversion from '@/element/components/Analytic/Conversion';
import Profit from '@/element/components/Analytic/Profit';

export default [
    {
        path: 'analytic',
        name: 'analytic',
        component: Analytic,
    },
    {
        path: 'top/events',
        name: 'top.events',
        component: TopEvents,
    },
    {
        path: 'conversion',
        name: 'conversion',
        component: Conversion,
    },
    {
        path: 'profit',
        name: 'profit',
        component: Profit,
    },
];
