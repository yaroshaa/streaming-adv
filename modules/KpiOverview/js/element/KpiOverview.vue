<template>
    <div class="">
        <div class="kpi-line_chart">
            <kpi-line-chart :loading="loading"></kpi-line-chart>
        </div>
        <div class="el-margin kpi-table">
            <kpi-table
                :table-columns="table.columns"
                :data-loading="loading"
                :query-page.sync="query.page"
                :query-limit.sync="query.limit"
            ></kpi-table>
        </div>
    </div>
</template>

<script>
import KpiLineChart from './KpiLineChart';
import KpiTable from './KpiTable';
import {mapGetters, mapActions} from 'vuex';
import EventBus from '@/socket/eventbus';
import {ORDER_UPDATED_EVENT} from '@/socket/events';
import kpiFields from '@/data/kpiFields';
import {getKpiTotals} from '@/service/request/kpi.js';

export default {
    name: 'KpiOverview',

    components: {
        KpiLineChart,
        KpiTable,
    },

    data() {
        return {
            loading: false,
            query: {
                page: 1,
                limit: 20,
            },

            groupByWeek: false,
            groupByMonth: false,
            table: {
                // Table for helper relation specify columns with code
                columnsMap: {
                    avgOrderValue: 0,
                    avgProfitPerOrder: 1,
                    totalDiscount: 2,
                    discountPercentage: 3,
                    totalPacked: 4,
                    totalReturned: 5,
                    returnPercentage: 6,
                    customers: 7,
                    newCustomers: 8,
                    newCustomersPercentage: 9,
                    orders: 10,
                    differentItemsSold: 11,
                    periodDate: 12,
                },

                columns: kpiFields,
            },
        };
    },

    mounted() {
        EventBus.on(
            ORDER_UPDATED_EVENT,
            () => {
                if (this.$route.name !== 'kpi-overview') {
                    return;
                }

                this.getData();
            },
            this.$options.name
        );

        this.getData();
    },

    methods: {
        ...mapActions({
            kpiSetData: 'kpiOverview/setData',
        }),

        getData() {
            this.loading = true;
            const params = {
                currency: this.currencyFilter,
                date: this.dateFilter,
                date_granularity: this.dateGranularityFilter,
            };
            getKpiTotals(params)
                .then((response) => {
                    if (response.data) {
                        this.kpiSetData(response.data.data);
                    }
                })
                .finally(() => {
                    this.loading = false;
                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },
    },

    computed: {
        ...mapGetters({
            currencyFilter: 'filters/currency',
            dateFilter: 'filters/date',
            dateGranularityFilter: 'filters/dateGranularity',
        }),
    },

    watch: {
        currencyFilter() {
            this.getData();
        },

        dateFilter() {
            this.getData();
        },

        dateGranularityFilter() {
            this.getData();
        },
    },
};
</script>

<style lang="scss"></style>
