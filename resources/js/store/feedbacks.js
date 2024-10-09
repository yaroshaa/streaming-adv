import filter from './filters/feedback.js';

const COUNT_FOR_ROTATE = 100;

export default {
    namespaced: true,
    modules: {
        filter,
    },
    state: () => {
        return {
            feedbacks: [],
            loading: false,
            noMore: false,
            from: '',
        };
    },
    mutations: {
        addFeedbackToStart(state, feedback) {
            let feedbacks = state.feedbacks;
            if (feedbacks.length >= COUNT_FOR_ROTATE) {
                feedbacks.splice(-1, 1);
                if (state.noMore) {
                    state.noMore = false;
                }
            }
            feedbacks.unshift(feedback);
            state.feedbacks = feedbacks;
        },
        addFeedbacks(state, feedbacks) {
            state.feedbacks = state.feedbacks.concat(feedbacks);
        },
        setFeedbacks(state, feedbacks) {
            state.feedbacks = feedbacks;
        },
        setLoading(state, flag) {
            state.loading = flag;
        },
        setNoMore(state, flag) {
            state.noMore = flag;
        },
        setFrom(state, from) {
            state.from = from;
        },
    },
    actions: {
        addFeedbackToStart({commit}, feedback) {
            commit('addFeedbackToStart', feedback);
        },
        addFeedbacks({commit}, feedbacks) {
            commit('addFeedbacks', feedbacks);
        },
        setFeedbacks({commit}, feedbacks) {
            commit('setFeedbacks', feedbacks);
        },
        setLoading({commit}, flag) {
            commit('setLoading', flag);
        },
        setNoMore({commit}, flag) {
            commit('setNoMore', flag);
        },
        setFrom({commit}, from) {
            commit('setFrom', from);
        },
        resetState({commit}) {
            commit('setFeedbacks', []);
            commit('setLoading', false);
            commit('setNoMore', false);
            commit('setFrom', '');
        },
    },
    getters: {
        feedbacks: (state) => state.feedbacks,
        loading: (state) => state.loading,
        noMore: (state) => state.noMore,
        from: (state) => state.from,
    },
};
