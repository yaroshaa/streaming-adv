import {formatCurrency} from '@/format/format';

export default [

    // {
    //     label: 'ID', // TODO Later you need to delete
    //     key: 'product_id',
    //     width: 80,
    //     show: true,
    // },

    {
        label: 'Remote ID',
        key: 'remote_id',
        show: false,
    },
    {
        label: 'Product name',
        key: 'name',
        show: true,
    },
    {
        label: 'Price',
        key: 'price',
        width: 100,
        show: true,
        modify: (value, currency) => formatCurrency(value, currency),
    },
    {
        label: 'Weight',
        key: 'weight',
        width: 100,
        show: true,
        caret: () => {}
    },
];
