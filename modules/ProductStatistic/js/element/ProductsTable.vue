<!-- eslint-disable -->
<template>
    <div>
        <el-table
            ref="singleTable"
            class="clickable-rows el-table--transparent"
            :data="pagedTableData"
            :current-row-key="getCurrentRowKey"
            row-key="product_id"
            :row-class-name="tableRowClassName"
            v-loading="loading"
            element-loading-text="Loading..."
            element-loading-spinner="el-icon-loading"
            element-loading-background="rgba(0, 0, 0, 0.4)"
            @current-change="getStatistic"
            fit
        >
            <el-table-column
                v-for="filterItem in tableColumns"
                v-if="filterItem.show"
                :width="filterItem.width"
                :label="filterItem.label"
                :key="filterItem.key">

                <template
                    #default="scope"
                >
                    <div class="cell-value" >
                         <span v-if="filterItem.hasOwnProperty('modify')">
                             {{
                                filterItem.modify(
                                    scope.row[filterItem.key],
                                    currencyFilter.code
                                    )
                             }}
                         </span>
                        <span v-else>
                            {{
                                scope.row[filterItem.key]
                            }}
                        </span>
                        <i class="el-icon-arrow-right caret-right" v-if="filterItem.hasOwnProperty('caret') && !scope.row['no_orders']"></i>
                    </div>
                </template>
            </el-table-column>
        </el-table>
        <pagination
            v-show="products.length > 0"
            :total="products.length"
            :paged-data="products"
            :page.sync="page"
            :limit.sync="limit"
            @pagination="setPage">
        </pagination>
    </div>
</template>

<script>
import {Table, TableColumn} from 'element-ui';
import {mapActions, mapGetters} from 'vuex';
import Pagination from '@/element/components/Pagination/Pagination';

export default {
    name: 'ProductTable',

    components:{
        ElTable: Table,
        ElTableColumn: TableColumn,
        Pagination,
    },

    data() {
        return {
            tableKey: 0,
            prev: null,
            page: 1,
            limit: 20,
        };
    },

    props: {
        tableColumns: {
            type: Array,
            required: true,
            default: () => [],
        },

        loading: {
            type: Boolean,
            required: true,
            default: false,
        },
    },

    methods: {
        ...mapActions({
            setCurrentProduct: 'products/setCurrentProduct',
        }),

        setPage(args){
            this.page = args.page;
            this.limit = args.limit;
        },

        getStatistic(row) {
            if (row.no_orders) {
                return;
            }

            this.setCurrentProduct(row);
        },

        tableRowClassName({row}) {
            let className = '';
            if (!row.no_orders && row.product_id === this.currentProduct.product_id) {
                className ='current-row';
            } else if (row.no_orders) {
                className ='disabled-row';
            }

            return className;
        },
    },

    computed: {
        ...mapGetters({
            currentProduct: 'products/currentProduct',
            products: 'products/products',
            currencyFilter: 'filters/currency',
        }),

        getCurrentRowKey() {
            return this.currentProduct.product_id;
        },

        pagedTableData() {
            // eslint-disable-next-line vue/max-len
            return this.productsData.slice(this.limit * this.page - this.limit, this.limit * this.page)
        },

        productsData() {
            return this.products;
        },
    },

    watch: {
        products(d) {
            this.getStatistic(d.find((i) => !i.no_orders));
        }
    }
}
</script>

<style lang="scss">
.el-table {
    thead {
        th {
            border-bottom-width: 2px !important;
        }
    }
    th {
        font-size: 16px;
        line-height: 24px;
        .cell {
            word-break: break-word;
        }
    }
}

.product-statistic-table {
    .cell-value {
        font-size: 16px;
        font-weight: 600;
        line-height: 24px;
    }
}

.clickable-rows {
    tbody tr {
        &.disabled-row {
            td {
                background: rgba(106, 106, 159, 0.11) !important;
                cursor: default !important;
            }
        }
        cursor: pointer;
        td {
            .caret-right {
                position: absolute;
                display: inline-block;
                right:10px;
                top: 17px
            }
        }
    }

    .current-row {
        td {
            background: #2c3e50 !important;
        }
    }

    .disabled-row {
        td {
            background: rgba(106, 106, 159, 0.11) !important;
            cursor: default !important;
        }
    }

    .el-table__expanded-cell {
        cursor: default;
    }
}
</style>
