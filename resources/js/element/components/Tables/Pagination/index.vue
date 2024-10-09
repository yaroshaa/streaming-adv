<template>
    <div :class="{hidden: hidden}" class="pagination-container">
        <el-pagination
            :background="background"
            :current-page.sync="currentPage"
            :page-size.sync="pageSize"
            :layout="layout"
            :page-sizes="pageSizes"
            :total="total"
            v-bind="$attrs"
            @size-change="handleSizeChange"
            @current-change="handleCurrentChange"
        ></el-pagination>
    </div>
</template>

<script>
import {Pagination as ElPagination} from 'element-ui';
import {scrollTo} from '@/utils/scroll-to';

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
            default: 50,
        },

        pageSizes: {
            type: Array,
            default() {
                return [10, 25, 50, 100];
            },
        },

        layout: {
            type: String,
            default: 'total, sizes, prev, pager, next, jumper',
        },

        background: {
            type: Boolean,
            default: true,
        },

        autoScroll: {
            type: Boolean,
            default: true,
        },

        hidden: {
            type: Boolean,
            default: false,
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

<!-- TODO extract to theme style -->
<style scoped>
.pagination-container {
    background: #fff;
    padding: 32px 16px;
}
.pagination-container.hidden {
    display: none;
}
</style>
