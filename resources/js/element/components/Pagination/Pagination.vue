<template>
    <div :class="{hidden: hidden}" class="pagination-container">
        <el-pagination
            class="el-pagination--transparent mt-2"
            :background="background"
            :current-page.sync="currentPage"
            :page-sizes="pageSizes"
            :page-size.sync="pageSize"
            :layout="layout"
            :total="total"
            v-bind="$attrs"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
        ></el-pagination>
    </div>
</template>

<script>
import {Pagination as ElPagination} from 'element-ui';
import {scrollTo} from '../../../utils/scroll-to';

export default {
    name: 'Pagination',

    components: {
        ElPagination,
    },

    props: {
        total: {
            required: true,
            type: Number,
        },

        page: {
            type: Number,
            default: 1,
        },

        limit: {
            type: Number,
            default: 20,
        },

        pageSizes: {
            type: Array,
            default() {
                return [10, 20, 50, 75, 100];
            },
        },

        layout: {
            type: String,
            default: 'total, sizes, prev, pager, next',
        },

        background: {
            type: Boolean,
            default: false,
        },

        autoScroll: {
            type: Boolean,
            default: true,
        },

        hidden: {
            type: Boolean,
            default: false,
        },

        pagedData: {
            type: Array,
            required: true,
            default: () => [],
        },
    },

    methods: {
        handleSizeChange(val) {
            this.$emit('pagination', {page: this.currentPage, limit: val});
            if (this.autoScroll) {
                scrollTo(0, 800);
            }
        },

        handleCurrentChange(val) {
            this.$emit('pagination', {page: val, limit: this.pageSize});
            if (this.autoScroll) {
                scrollTo(0, 800);
            }
        },
    },

    computed: {
        currentPage: {
            get() {
                return this.page;
            },

            set(val) {
                this.$emit('update:page', val);
            },
        },

        pageSize: {
            get() {
                return this.limit;
            },

            set(val) {
                this.$emit('update:limit', val);
            },
        },

    },
};
</script>

<style scoped>

.pagination-container.hidden {
    display: none;
}
</style>
