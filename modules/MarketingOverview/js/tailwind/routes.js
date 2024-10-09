import WarehousesList from './WarehousePackingStation/WarehousesList';

const routes = [
    {
        path: '/',
        redirect: {
            name: 'orders',
        },
    },
    {
        path: '/warehouse-packing-station',
        name: 'warehouse-packing-station',
        component: WarehousesList,
    },
];

export default routes;
