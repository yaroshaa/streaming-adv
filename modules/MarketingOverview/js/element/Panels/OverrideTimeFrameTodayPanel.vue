<template>
    <div class="mo-override-tf-today-panel">
        <div class="mo-override-tf-today-panel__checkbox">
            Override timeframe (today)
            <el-checkbox></el-checkbox>
        </div>
        <el-table
            :data="tableData"
            class="el-table__override-time-frame-today"
            :style="{width: 340 + 'px'}"
            :row-class-name="tableRowClassName"
        >
            <el-table-column
                v-for="(item, index) in tableHeader"
                :key="item.prop"
                :fixed="index < 2"
                :prop="item.prop"
                :width="index !== 0 ? 60 : 100"
            >
                <template slot="header">
                    <img v-if="item.image" :src="item.image" :title="item.title" />
                </template>
            </el-table-column>
        </el-table>
    </div>
</template>

<script>
import {Table, TableColumn, Checkbox} from 'element-ui';
import {mapGetters} from 'vuex';

export default {
    name: 'OverrideTimeFrameTodayPanel',

    components: {
        ElTable: Table,
        ElTableColumn: TableColumn,
        ElCheckbox: Checkbox
    },

    data() {
        return {
            tableLoaded: false,
            tableHeader: [],
            tableData: []
        }
    },

    methods: {
        tableRowClassName({row}) {
            let classList = [];
            if (row.bolder) {
                classList.push('row-bolder')
            }
            if (row.separator) {
                classList.push('row-separator')
            }

            return classList.join(' ');
        }
    },

    computed: {
        ...mapGetters({
            storageData: 'marketingOverview/data',
        }),
    },

    watch: {
        'storageData': function (val) {
            this.tableHeader = val.overview_table.header;
            this.tableData = val.overview_table.data;
            this.tableLoaded = true;
        }
    }
}
</script>

<style lang="scss">
.mo-override-tf-today-panel {
    padding: 15px;

    &__checkbox {
        font-size: 12px;
        opacity: .9;
        float: right;
        color: #505050;
    }

    .el-table__override-time-frame-today {
        font-family: Open Sans, sans-serif;
        font-size: 13px;
        background-color: transparent !important;

        * {
            background-color: transparent !important;
        }

        &:before {
            background-color: transparent !important;

        }

        .el-table {
            &__fixed {
                //background-color: #191932 !important;
                background-color: #d4dbe3 !important;
                color: black;

                &:before {
                    background-color: transparent;
                }
            }

            &__header-wrapper, &__fixed-header-wrapper {
                border-bottom: 1px solid #666666;
            }

            &__empty-block {
                width: 100% !important;
            }

            &__empty-text {
                z-index: 999;
            }

            &__row {
                td {
                    padding: 5px 0;
                    border-color: transparent;
                    text-align: right;

                    &:first-child {
                        text-align: left;
                    }

                    .cell {
                        //color: #A6A6A6;
                        color: #000000;
                        padding: 0 5px 0 0;
                    }

                    &:nth-child(2) {
                        .cell {
                            //color: #fff !important;
                         }
                    }

                    &:nth-child(2) {
                        .cell {
                            //text-align: left;
                        }
                    }
                }

                &.row-separator {
                    td {
                        border-color: #666666;
                        margin-bottom: 5px;
                    }

                    & + tr {
                        td {
                            padding-top: 15px;
                        }
                    }
                }

                &.row-bolder {
                    td {
                        .cell {
                            font-weight: bold;
                        }
                    }
                }
            }

            &__header {
                th {
                    border-spacing: 1px !important;
                    border-color: #666666 !important;
                    border: none;

                    .cell {
                        display: flex;
                        justify-content: center;

                        img {
                            max-width: 21px;
                            max-height: 21px;
                        }
                    }
                }
            }
        }
    }
}
</style>
