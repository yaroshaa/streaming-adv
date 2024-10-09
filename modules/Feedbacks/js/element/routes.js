import FeedbacksPage from '@/element/components/Feedbacks/FeedbacksPage';
import TagsPage from '@/element/components/Feedbacks/Settings/TagsPage';

export default [
    {
        path: 'feedbacks',
        name: 'feedbacks',
        component: FeedbacksPage,
    },
    {
        path: 'feedbacks/tags',
        name: 'feedbacks.tags',
        component: TagsPage,
    },
];
