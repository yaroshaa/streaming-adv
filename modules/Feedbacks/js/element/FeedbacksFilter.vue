<template>
    <div class="filters-block">
        <el-select
            class="filters-block-element"
            v-model="inputMarkets"
            multiple
            placeholder="Market"
            collapse-tags
            value-key="remote_id"
            clearable
        >
            <el-option
                v-for="market in markets"
                :label="market.name"
                :value="market"
                :key="market.remote_id"
            >
            </el-option>
        </el-select>
        <el-select
            class="filters-block-element"
            v-model="inputSources"
            multiple
            placeholder="Source"
            collapse-tags
            value-key="remote_id"
            clearable
        >
            <el-option
                v-for="source in sources"
                :label="source.name"
                :value="source"
                :key="source.remote_id"
            >
            </el-option>
        </el-select>

        <el-select
            v-model="inputWords"
            class="filters-block-element"
            multiple
            clearable
            filterable
            collapse-tags
            allow-create
            placeholder="Select"
        >
            <el-option
                v-for="word in words"
                :key="word"
                :label="word"
                :value="word"
            >
            </el-option>
            <div
                v-if="tags.length > 0"
                class="select--after-content"
                slot="after-content"
            >
                <p class="text-muted">Saved settings:</p>
                <div class="el-flex el-flex-center flex-wrap">
                    <div
                        v-for="tag in tags"
                        :key="tag.id"
                        :class="[
                            `tag-wrap-${tag.color}`,
                            filteredTags.indexOf(tag) !== -1
                                ? `tag-wrap-${tag.color}--active`
                                : '',
                        ]"
                        @click="toggleTag(tag)"
                    >
                        <button class="tag">
                            <span>{{ tag.name }}</span>
                        </button>
                    </div>
                </div>
            </div>
        </el-select>
        <div class="tags-wrapper">
            <el-tag
                :key="tag.id"
                v-for="tag in filteredTags"
                closable
                class="rounded-pill tag-line"
                size="mini"
                :type="tag.color"
                :disable-transitions="false"
                @close="removeTag(tag)"
            >
                {{ tag.name }}
            </el-tag>
        </div>
    </div>
</template>

<script>
import {Option, Tag} from 'element-ui';
import {Select} from '@/element/element-ui';
import {mapActions, mapGetters} from 'vuex';
import {getMarkets} from '@/service/request/market.js';
import {getSources} from '@/service/request/source.js';
import {getTags} from '@/service/request/tag.js';

export default {
    name: 'FeedbacksFilter',
    components: {
        ElSelect: Select,
        ElOption: Option,
        ElTag: Tag,
    },

    mounted() {
        getMarkets().then((response) => {
            this.setMarkets(response.data);
        });
        getSources().then((response) => {
            this.setSources(response.data);
        });
        getTags().then((response) => {
            this.setTags(response.data.data);
        });
        this.initWords();
    },

    methods: {
        ...mapActions({
            setSources: 'feedbacks/filter/setSources',
            setMarkets: 'feedbacks/filter/setMarkets',
            setTags: 'feedbacks/filter/setTags',

            setFilteredSources: 'feedbacks/filter/setFilteredSources',
            setFilteredMarkets: 'feedbacks/filter/setFilteredMarkets',
            setFilteredTags: 'feedbacks/filter/setFilteredTags',
            removeFilteredTag: 'feedbacks/filter/removeFilteredTag',
            addFilteredTag: 'feedbacks/filter/addFilteredTag',
            setFilteredWords: 'feedbacks/filter/setFilteredWords',
            initWords: 'feedbacks/filter/initWords',
        }),

        toggleTag(tag) {
            if (this.filteredTags.indexOf(tag) === -1) {
                this.addFilteredTag(tag);
            } else {
                this.removeTag(tag);
            }
        },

        removeTag(tag) {
            this.removeFilteredTag(tag);
        },
    },

    computed: {
        ...mapGetters({
            sources: 'feedbacks/filter/sources',
            markets: 'feedbacks/filter/markets',
            tags: 'feedbacks/filter/tags',
            words: 'feedbacks/filter/words',

            filteredSources: 'feedbacks/filter/filteredSources',
            filteredMarkets: 'feedbacks/filter/filteredMarkets',
            filteredTags: 'feedbacks/filter/filteredTags',
            filteredWords: 'feedbacks/filter/filteredWords',
        }),

        inputSources: {
            get() {
                return this.filteredSources;
            },

            set(value) {
                this.setFilteredSources(value);
            },
        },

        inputMarkets: {
            get() {
                return this.filteredMarkets;
            },

            set(value) {
                this.setFilteredMarkets(value);
            },
        },

        inputTags: {
            get() {
                return this.filteredTags;
            },

            set(value) {
                this.setFilteredTags(value);
            },
        },

        inputWords: {
            get() {
                return this.filteredWords;
            },

            set(value) {
                this.setFilteredWords(value);
            },
        },
    },
};
</script>

<style lang="scss">
.tag-line .el-tag__close {
    font-size: 18px !important;
    color: $--color-base-dark !important;

    &:hover {
        color: $--color-white !important;
        background: none !important;
    }
}
</style>

<style lang="scss" scoped>
.select--after-content {
    padding: 17px;
    max-width: 265px;
}

.el-tag {
    margin: 8px;
    cursor: pointer;
    padding: 6px 12px;
    height: 26px;
    line-height: 100%;
}
</style>
