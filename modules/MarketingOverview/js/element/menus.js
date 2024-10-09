export default {
    order: 1.5,
    index: 'marketing-overview',
    icon: 'el-icon-money',
    title: 'Marketing overview',
    class: 'el-menu-item',
    sub_menu: [
        {
            order: 1.5,
            icon: 'el-icon-money',
            route: 'marketing-overview',
            index: 'marketing-overview',
            title: 'Overview',
        },
        {
            route: 'marketing-overview.holiday-event',
            index: '/dashboard/marketing-overview/holiday-event',
            title: 'Holiday Events',
        },
    ],
};
