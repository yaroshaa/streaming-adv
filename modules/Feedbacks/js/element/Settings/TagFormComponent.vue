<template>
    <form class="form">
        <h1>Settings</h1>
        <div class="form-group">
            <el-input
                class="text-input"
                placeholder="Tag name"
                v-model="name"
            ></el-input>
            <label class="color-picker">
                <el-tooltip placement="top-start">
                    <div slot="content" class="circle-color-list">
                        <CircleColorComponent
                            @click.native="selectColor(colorClass)"
                            v-for="colorClass in namesOfColors"
                            :color-class="colorClass"
                            :key="colorClass"
                            :active="color === colorClass"
                        ></CircleColorComponent>
                    </div>
                    <CircleColorComponent :color-class="color">
                    </CircleColorComponent>
                </el-tooltip>
                Tag color
            </label>
        </div>

        <h2>Keywords</h2>

        <div class="form-group">
            <el-input
                class="text-input"
                v-model="keywordInput"
                ref="saveTagInput"
                placeholder="Enter a keyword"
                @keyup.enter.native="handleInputConfirm"
                @blur="handleInputConfirm"
            >
            </el-input>
            <label class="text-muted">
                Click "Enter" to add
            </label>
            <div class="keywords-wrapper">
                <el-tag
                    :key="keyword"
                    v-for="keyword in keywords"
                    closable
                    class="rounded-pill"
                    size="mini"
                    type="blueish-black"
                    :disable-transitions="false"
                    @close="handleClose(keyword)"
                >
                    {{ keyword }}
                </el-tag>
            </div>
        </div>

        <el-button
            type="primary-submit"
            size="large"
            class="el-width-sm rounded-sm"
            @click="saveTag"
        >
            Save changes
        </el-button>
        <el-button
            v-show="id !== 0"
            type="danger-submit"
            size="large"
            class="el-width-xs rounded-sm remove-button"
            icon="el-icon-delete"
            @click="removeTag"
        >
            Delete tag
        </el-button>
    </form>
</template>

<script>

import CircleColorComponent from '@/element/components/Feedbacks/Settings/CircleColorComponent';
import {Button, Input, Tag, Tooltip} from 'element-ui';
import * as colors from '@/config/colors.js';

export default {
    name: 'TagFormComponent',
    components: {
        CircleColorComponent,
        ElInput: Input,
        ElTooltip: Tooltip,
        ElTag: Tag,
        ElButton: Button
    },

    data() {
        return {
            id: 0,
            name: '',
            color: 'electric-green',
            keywords: [],

            namesOfColors: colors,

            newKeyword: '',
            keywordInput: ''
        }
    },

    inject: [
        'currentTag',
    ],

    methods: {
        getData() {
            return {
                'id': this.id,
                'name': this.name,
                'color': this.color,
                'keywords': this.keywords,
            };
        },

        saveTag() {
            this.$emit('save', this.getData());
        },

        removeTag() {
            this.$emit('remove', this.getData())
        },

        selectColor(color) {
            this.color = color;
        },

        handleClose(tag) {
            this.keywords.splice(this.keywords.indexOf(tag), 1);
        },

        showInput() {
            this.$nextTick(() => {
                this.$refs.saveTagInput.$refs.input.focus();
            });
        },

        handleInputConfirm() {
            let keywordInput = this.keywordInput;
            if (keywordInput && !this.keywords.includes(keywordInput)) {
                this.keywords.push(keywordInput);
            }

            this.keywordInput = '';
        },

        setTag(tag) {
            this.id = tag.id;
            this.name = tag.name;
            this.color = tag.color;
            this.keywords = tag.keywords;
        }
    }
}
</script>


<style lang="scss" scoped>

.form-group {
    margin-bottom: 35px;

    label {
        padding-left: 40px;

        &.text-muted {
            color: $--color-purple-blue !important;
            font-size: 14px;
        }

        &.color-picker {
            color: $--color-white;
            font-size: 16px;
            line-height: 40px;

            .circle-color {
                margin-right: 13px;
            }
        }
    }
}

.circle-color-list {
    .circle-color {
        margin: 0 5px;
    }
}

.text-input {
    width: 234px;
}

.keywords-wrapper {
    margin-top: 20px;
    margin-bottom: 75px;

    .el-tag--blueish-black {
        height: auto;
        margin: 11px 10px 11px 0;
        padding: 5px 15px;
    }
}

.remove-button {
    margin-left: 12px;
}

</style>
