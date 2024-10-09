<template>
    <div
        v-infinite-scroll="load"
        infinite-scroll-disabled="loading"
        infinite-scroll-immediate="false"
        infinite-scroll-delay="500"
        class="feedbacks-infinite-wrapper"
    >
        <transition-group name="animation-opacity" tag="div" class="card-view">
            <div
                class="card"
                v-for="feedback in feedbacks"
                :key="feedback.unique_id"
            >
                <div class="card--header">
                    <div class="header-item">
                        <img
                            class="image-img"
                            v-if="feedback.market_icon_link"
                            :src="feedback.market_icon_link"
                        />
                        {{ feedback.market_name }}
                    </div>
                    <div class="header-item">
                        <img
                            class="image-img"
                            v-if="feedback.source_icon_link"
                            :src="feedback.source_icon_link"
                        />
                        {{ feedback.source_name }}
                    </div>

                    {{ formatDate(feedback.created_at) }}
                    <div class="right" v-if="feedback.url">
                        <a
                            class="el-button el-button--mini is-round el-button--purple"
                            target="_blank"
                            :href="feedback.url"
                        >Go over <i class="el-icon-right el-rotate-315"></i></a>
                    </div>
                </div>
                <div
                    class="card--content"
                    v-html="cutText(highlight(feedback.message, actualWordsHighlighter))"
                ></div>
                <el-button v-if="feedback.message.length > 255" type="text" @click="open(feedback)">Click to full view</el-button>

            </div>
        </transition-group>

        <p v-if="loading" class="loader-message">Loading...</p>
        <p v-if="noMore" class="loader-message">No more</p>
    </div>
</template>

<script>
import {mapActions, mapGetters} from 'vuex';
import {Button, MessageBox} from 'element-ui';
import EventBus from '@/socket/eventbus';
import {FEEDBACK_ADDED_EVENT} from '@/socket/events';
import {highlight} from '@/core/helper.js';
import moment from 'moment';
import {getFeedbacks} from '@/service/request/feedback';
import qs from 'qs';

export default {
    name: 'FeedbacksList',

    components: {
        ElButton: Button
    },

    mounted() {
        EventBus.on(
            FEEDBACK_ADDED_EVENT,
            (e) => {
                if (this.$route.name === 'feedbacks') {
                    let feedback = e.feedback;

                    let remoteIds = (arr) =>
                        arr.map(({remote_id}) => remote_id);

                    let sourceIds = remoteIds(this.sourceFilter);
                    let marketIds = remoteIds(this.marketFilter);

                    let checkSource =
                        sourceIds.length === 0 ||
                        sourceIds.includes(feedback.source_id);
                    let checkMarket =
                        marketIds.length === 0 ||
                        marketIds.includes(feedback.market_id);

                    let messageInLowerCase = feedback.message.toLocaleLowerCase();
                    let checkWords = this.actualWords.some(
                        (word) =>
                            messageInLowerCase.indexOf(
                                word.toLocaleLowerCase()
                            ) !== -1
                    );

                    if (
                        checkSource &&
                        checkMarket &&
                        (this.actualWords.length === 0 || checkWords)
                    ) {
                        this.addFeedbackToStart(feedback);
                    }
                }

                EventBus.unlock(FEEDBACK_ADDED_EVENT, this.$options.name);
            },
            this.$options.name
        );

        this.resetState();
        this.load();
    },

    methods: {
        open(feedback) {
            MessageBox({
                title: feedback.source_name + ' ' + feedback.market_name  + ' ' + this.formatDate(feedback.created_at),
                message: this.highlight(feedback.message, this.actualWordsHighlighter),
                confirmButtonText: 'Close',
                dangerouslyUseHTMLString: true,
            })
        },

        cutText(text) {
            if (text.length <= 255) {
                return text;
            }
            return text.replace(/^(.{255}[^\s]*).*/, '$1') + '...';
        },

        highlight: highlight,
        ...mapActions({
            setLoading: 'feedbacks/setLoading',
            setNoMore: 'feedbacks/setNoMore',
            addFeedbackToStart: 'feedbacks/addFeedbackToStart',
            addFeedbacks: 'feedbacks/addFeedbacks',
            setFeedbacks: 'feedbacks/setFeedbacks',
            setFrom: 'feedbacks/setFrom',
            resetState: 'feedbacks/resetState',
        }),

        formatDate(datetime) {
            return moment(datetime).format('HH:mm:ss');
        },

        load() {
            if (this.noMore) {
                return;
            }

            this.setLoading(true);

            getFeedbacks({
                params: {
                    words: this.actualWords,
                    market: this.marketFilter,
                    source: this.sourceFilter,
                    from: this.from,
                },
                paramsSerializer: params => {
                    return qs.stringify(params)
                }
            }).then((data) => {
                let items = Object.values(data.data);
                if (items.length === 0) {
                    this.setNoMore(true);
                } else {
                    if (this.from === '') {
                        this.setFeedbacks(items);
                    } else {
                        this.addFeedbacks(items);
                    }
                    this.setFrom(
                        moment(items[items.length - 1].created_at).toISOString(
                            true
                        )
                    );
                }
                this.setLoading(false);
            });
        },

        initialLoad() {
            this.resetState();
            this.load();
        },
    },

    computed: {
        ...mapGetters({
            marketFilter: 'feedbacks/filter/filteredMarkets',
            sourceFilter: 'feedbacks/filter/filteredSources',
            filteredTags: 'feedbacks/filter/filteredTags',
            filteredWords: 'feedbacks/filter/filteredWords',
            feedbacks: 'feedbacks/feedbacks',
            loading: 'feedbacks/loading',
            noMore: 'feedbacks/noMore',
            from: 'feedbacks/from',
        }),

        actualWords() {
            return [
                this.filteredWords,
                this.filteredTags.map(({keywords}) => keywords),
            ]
                .flat(2)
                .filter((el, i, a) => i === a.indexOf(el));
        },

        actualWordsHighlighter() {
            let arr = [];

            this.filteredWords.forEach((item) => {
                arr[item.toLocaleLowerCase()] = 'highlight-orange';
            });

            this.filteredTags.forEach((item) => {
                item.keywords.forEach((keyword) => {
                    arr[keyword.toLocaleLowerCase()] =
                        'highlight-' + item.color;
                });
            });

            return arr;
        },

        disabled() {
            return this.loading || this.noMore;
        },
    },

    watch: {
        filteredWords() {
            this.initialLoad();
        },

        filteredTags() {
            this.initialLoad();
        },

        marketFilter() {
            this.initialLoad();
        },

        sourceFilter() {
            this.initialLoad();
        },
    },
};
</script>

<style lang="scss" scoped>
.feedbacks-infinite-wrapper {
    height: calc(100vh - 150px);
    overflow: auto;
}

.loader-message {
    text-align: center;
    //color: #fff;
    color: #000;
}
</style>
