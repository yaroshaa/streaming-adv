export default {
    order: 6,
    index: 'feedbacks',
    icon: 'el-icon-chat-line-square',
    title: 'Feedbacks',
    sub_menu: [
        {
            route: 'feedbacks',
            index: '/dashboard/feedbacks',
            title: 'Overview',
        },
        {
            route: 'feedbacks.tags',
            index: '/dashboard/feedbacks/tags',
            title: 'Tags',
        },
    ],
};
