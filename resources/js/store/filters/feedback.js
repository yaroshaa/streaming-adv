const WORDS_KEY = 'feedback_filter_words'; // Key for getting words from local storage
const WORDS_COUNT = 10; // Count of user words for autocomplete

export default {
    namespaced: true,
    state: () => {
        return {
            sources: [],
            markets: [],
            tags: [],
            words: [],

            filteredWords: [],
            filteredSources: [],
            filteredMarkets: [],
            filteredTags: [],
        };
    },
    mutations: {
        SET_SOURCES(state, sources) {
            state.sources = sources;
        },
        SET_MARKETS(state, markets) {
            state.markets = markets;
        },
        SET_TAGS(state, tags) {
            state.tags = tags;
        },
        SET_WORDS(state, words) {
            state.words = words;
        },

        SET_FILTERED_SOURCES(state, sources) {
            state.filteredSources = sources;
        },
        SET_FILTERED_MARKETS(state, markets) {
            state.filteredMarkets = markets;
        },
        SET_FILTERED_TAGS(state, tags) {
            state.filteredTags = tags;
        },
        ADD_FILTERED_TAG(state, tag) {
            let index = state.filteredTags.indexOf(tag);
            if (index === -1) {
                state.filteredTags.push(tag);
            }
        },
        REMOVE_FILTERED_TAG(state, tag) {
            let index = state.filteredTags.indexOf(tag);
            if (index !== -1) {
                state.filteredTags.splice(index, 1);
            }
        },

        /**
         * Setup active words and save "non active" words to local storage
         * @param state
         * @param words
         * @constructor
         */
        SET_FILTERED_WORDS(state, words) {
            let savedWords = words
                .concat(state.words)
                .filter((value, index, self) => value && value.length > 0 && self.indexOf(value) === index);

            localStorage.setItem(WORDS_KEY, JSON.stringify(savedWords.slice(0, WORDS_COUNT)));
            state.words = savedWords;
            state.filteredWords = words;
        },
    },
    actions: {
        setSources({commit}, sources) {
            commit('SET_SOURCES', sources);
        },
        setMarkets({commit}, sources) {
            commit('SET_MARKETS', sources);
        },
        setTags({commit}, sources) {
            commit('SET_TAGS', sources);
        },
        setFilteredSources({commit}, sources) {
            commit('SET_FILTERED_SOURCES', sources);
        },
        setFilteredMarkets({commit}, markets) {
            commit('SET_FILTERED_MARKETS', markets);
        },
        setFilteredTags({commit}, tags) {
            commit('SET_FILTERED_TAGS', tags);
        },
        addFilteredTag({commit}, tag) {
            commit('ADD_FILTERED_TAG', tag);
        },
        removeFilteredTag({commit}, tag) {
            commit('REMOVE_FILTERED_TAG', tag);
        },
        setFilteredWords({commit}, words) {
            commit('SET_FILTERED_WORDS', words);
        },

        /**
         * Init words to autocomplete, previous state from local storage
         * @param commit
         */
        initWords({commit}) {
            commit('SET_WORDS', JSON.parse(localStorage.getItem(WORDS_KEY)));
        },
    },
    getters: {
        sources: (state) => state.sources,
        markets: (state) => state.markets,
        tags: (state) => state.tags,
        words: (state) => state.words,

        filteredSources: (state) => state.filteredSources,
        filteredMarkets: (state) => state.filteredMarkets,
        filteredTags: (state) => state.filteredTags,
        filteredWords: (state) => state.filteredWords,
    },
};
