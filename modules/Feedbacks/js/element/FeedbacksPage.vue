<template>
    <div>
        <portal to="header-filter">
            <feedbacks-filter></feedbacks-filter>
        </portal>
        <feedbacks-list></feedbacks-list>
    </div>
</template>

<script>
import {Portal} from 'portal-vue';
import FeedbacksList from './FeedbacksList';
import FeedbacksFilter from '@/element/components/Feedbacks/FeedbacksFilter';

export default {
    name: 'FeedbacksPage',
    components: {
        FeedbacksFilter,
        FeedbacksList,
        Portal,
    },
};
</script>
