<template>
    <div class="el-grid el-grid-lg">
        <div class="el-width-3-10">
            <div class="el-grid">
                <div class="el-width-1-4">
                    <h1>Tags</h1>
                </div>
                <div class="el-width-3-4 el-text-right">
                    <a class="new-tag-button" @click="addNewTag">+ New Tag</a>
                </div>
            </div>
            <div class="el-flex el-flex-center flex-wrap mt-3">
                <div
                    v-for="tag in tags"
                    :key="tag.id"
                    :class="[
                        `tag-wrap-${tag.color}`,
                        isActive(tag) ? `tag-wrap-${tag.color}--active` : '',
                    ]"
                    @click="selectTag(tag)"
                >
                    <button class="tag">
                        <span>{{ tag.name }}</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="el-width-7-10">
            <TagFormComponent
                ref="form"
                @remove="onRemove"
                @save="onSave"
            ></TagFormComponent>
        </div>
    </div>
</template>

<script>
import TagFormComponent from '@/element/components/Feedbacks/Settings/TagFormComponent';
import {BACKGROUND_COLOR_ELECTRIC_GREEN} from '@/config/colors.js';
import {Notification} from 'element-ui';
import {errorResponseToString} from '@/core/helper';
import {deleteTag, getTags, postTag, putTag} from '@/service/request/tag';

export default {
    name: 'TagsPage',
    components: {
        TagFormComponent: TagFormComponent,
    },

    data() {
        return {
            tags: [],
            currentTag: null,
            emptyTag: {
                id: 0,
                name: '',
                color: BACKGROUND_COLOR_ELECTRIC_GREEN,
                keywords: [],
            },
        };
    },

    provide: {
        currentTag: () => this.currentTag,
    },

    mounted() {
        getTags().then((response) => {
            this.tags = response.data.data;
        });
        this.selectTag(this.emptyTag);
    },

    methods: {
        isActive(tag) {
            return this.currentTag && this.currentTag.id === tag.id;
        },

        selectTag(tag) {
            let currentTag = JSON.parse(JSON.stringify(tag));
            this.currentTag = currentTag;
            this.$refs.form.setTag(currentTag);
        },

        addNewTag() {
            this.selectTag(this.emptyTag);
        },

        onSave(tag) {
            let index = this.findIndexOfTag(tag);

            if (index === -1) {
                postTag(tag)
                    .then((response) => {
                        let savedTag = response.data;
                        this.tags.push(savedTag);
                        this.selectTag(savedTag);
                        Notification.success({
                            title: 'Saved',
                            message: 'Tag added',
                        });
                    })
                    .catch((e) => {
                        Notification.error({
                            title: 'Error',
                            message: errorResponseToString(e),
                        });
                    });
            } else {
                putTag(tag.id, tag)
                    .then((response) => {
                        let savedTag = response.data.data;
                        this.tags.splice(index, 1, savedTag);
                        this.selectTag(savedTag);
                        Notification.success({
                            title: 'Updated',
                            message: 'Tag saved',
                        });
                    })
                    .catch((e) => {
                        Notification.error({
                            title: 'Error',
                            message: errorResponseToString(e),
                        });
                    });
            }
        },

        onRemove(tag) {
            let index = this.findIndexOfTag(tag);
            if (index !== -1) {
                deleteTag(tag.id)
                    .then(() => {
                        this.tags.splice(index, 1);
                        this.selectTag(this.emptyTag);
                        Notification.success({
                            title: 'Deleted',
                            message: 'Tag deleted',
                        });
                    })
                    .catch((e) => {
                        Notification.success({
                            title: 'Error',
                            message: errorResponseToString(e),
                        });
                    });
            }
        },

        findIndexOfTag(tag) {
            return this.tags.map((tag) => tag.id).indexOf(tag.id);
        },
    },
};
</script>

<style lang="scss" scoped>
.new-tag-button {
    color: #991bfa;
    font-size: 14px;
    line-height: 24px;
    cursor: pointer;
}
</style>
