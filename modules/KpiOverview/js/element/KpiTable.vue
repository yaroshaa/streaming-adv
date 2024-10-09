<template>
    <div class="">
        <div class="compare-buttons">
            <button
                v-show="selectedRows.length > 1"
                class="el-button el-button--mini is-round el-button--purple"
                @click="rowsCompare()"
            >
                Compare
            </button>
            <button
                v-show="isCompared"
                class="el-button el-button--mini is-round el-button--purple"
                @click="resetCompare()"
            >
                Reset
            </button>
        </div>
        <el-table
            class="kpi-table el-table--transparent"
            v-loading="dataLoading"
            :data="dataList"
            fit
            @header-click="handleHeaderClick"
            @selection-change="handleSelectionChange"
        >
            <el-table-column
                v-for="filterItem in tableColumns"
                :type="filterItem.type"
                :width="filterItem.width"
                :label="filterItem.label"
                :key="filterItem.key"
            >
                <template
                    #default="scope"
                    v-if="filterItem.type !== 'selection'"
                >
                    <div class="cell-value">
                        {{
                            filterItem.hasOwnProperty('modify')
                                ? filterItem.modify(
                                    scope.row[filterItem.key],
                                    currencyFilter.code
                                )
                                : scope.row[filterItem.key]
                        }}
                    </div>
                    <span
                        v-if="dataPrevious[scope.$index][filterItem.key] !== ''"
                        :class="
                            dataPrevious[scope.$index][filterItem.key] > 0
                                ? 'caret-up'
                                : 'caret-down'
                        "
                        v-text="
                            formatPercent(
                                dataPrevious[scope.$index][filterItem.key]
                            )
                        "
                    >
                    </span>
                </template>
            </el-table-column>
        </el-table>

        <pagination
            v-show="dataTotal > 0"
            :total="dataTotal"
            :page.sync="queryPage"
            :limit.sync="queryLimit"
            @pagination="handlePagination"
        ></pagination>
    </div>
</template>

<script>
import {Table, TableColumn} from 'element-ui';
import Pagination from '@/element/components/Tables/Pagination';
import {formatPercent} from '@/format/format';
import {mapGetters, mapActions} from 'vuex';
import kpiFields from '@/data/kpiFields';

export default {
    name: 'KpiTable',

    components: {
        ElTable: Table,
        ElTableColumn: TableColumn,
        Pagination,
    },

    data() {
        return {
            tableKey: 0,
            prev: null,
            dataTotal: 0,
            selectedRows: [],
        };
    },

    props: {
        tableColumns: {
            type: Array,
            required: true,
            default: () => [],
        },

        dataLoading: {
            type: Boolean,
            required: true,
            default: false,
        },

        queryPage: {
            type: Number,
            required: true,
            default: 1,
        },

        queryLimit: {
            type: Number,
            required: true,
            default: 20,
        },
    },

    methods: {
        ...mapActions({
            updateActiveField: 'filters/updateKpiOverviewActiveFields',
            kpiCompare: 'kpiOverview/compare',
            kpiResetCompare: 'kpiOverview/resetCompare',
        }),

        formatPercent,

        handleSelectionChange(selected) {
            this.selectedRows = selected;
        },

        handleHeaderClick(column) {
            this.updateActiveField(
                kpiFields.find((x) => x.label === column.label).key
            );
        },

        handlePagination() {
            // Todo: check work sync in parent component
            this.$emit('handlePagination');
        },

        compareWithPrevious(row, key, rowNum) {
            if (rowNum + 1 >= this.dataList.length || key === 'date') {
                return '';
            }

            if (
                0 === this.dataList[rowNum][key] ||
                null === this.dataList[rowNum][key]
            ) {
                return '';
            }

            let diff =
                this.dataList[rowNum][key] - this.dataList[rowNum + 1][key];

            if (diff === 0) {
                return '';
            }

            return diff / this.dataList[rowNum][key];
        },

        rowsCompare() {
            this.kpiCompare(this.selectedRows);
        },

        resetCompare() {
            this.kpiResetCompare();
        },
    },

    computed: {
        ...mapGetters({
            currencyFilter: 'filters/currency',
            dataList: 'kpiOverview/data',
            isCompared: 'kpiOverview/isCompared',
        }),

        dataPrevious: function () {
            let prev = [];

            this.dataList.forEach((val, i) => {
                prev[i] = {};
                this.tableColumns.forEach(
                    (value) =>
                        (prev[i][value.key] = this.compareWithPrevious(
                            this.dataList[i],
                            value.key,
                            i
                        ))
                );
            });

            return prev;
        },
    },
};
</script>

<style lang="scss">
.page-row {
    .page-content {
        width: 95%;
    }
}

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
    .cell {
        .el-checkbox {
            margin: 0;
        }
    }
}

.kpi-table {
    .cell-value {
        font-size: 16px;
        font-weight: 600;
        line-height: 24px;
    }
    .el-caret {
        margin-right: -5px;
        vertical-align: text-bottom;
    }
    .color-red {
        color: $--color-danger;
    }
    .color-green {
        color: $--color-electric-green;
    }
    .caret-up,
    .caret-down {
        font-size: 12px;
        position: relative;
        &:before {
            font-size: 10px;
            margin-right: 8px;
            transform: scale(1, 0.6);
        }
    }
    .caret-up {
        color: $--color-electric-green;
        &:before {
            content: '▲';
        }
    }
    .caret-down {
        color: $--color-danger;
        &:before {
            content: '▼';
        }
    }
}

.compare-buttons {
    min-height: 35px;
}
</style>
