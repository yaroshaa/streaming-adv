<template>
    <div>
        <div>
            <store-today-line-chart :currency-symbol="this.currency_symbol">
            </store-today-line-chart>
        </div>
        <div class="el-grid">
            <store-today-filters
                :data="this.filtersData"
            >
            </store-today-filters>
            <store-today-filtered-data
                :profit="this.profit"
                :revenue="this.revenue"
                :currency-symbol="this.currency_symbol"
            ></store-today-filtered-data>

            <store-today-circle-chart
                :profit="this.profit.lastDays"
                :revenue="this.revenue.lastDays"
            ></store-today-circle-chart>
        </div>
    </div>
</template>

<script>
import {mapGetters, mapActions} from 'vuex';
import EventBus from '@/socket/eventbus';
import {ORDER_UPDATED_EVENT} from '@/socket/events';
import StoreTodayLineChart from '@/element/components/StoreToday/StoreTodayLineChart';
import StoreTodayFilters from '@/element/components/StoreToday/StoreTodayFilters';
import StoreTodayCircleChart from '@/element/components/StoreToday/StoreTodayCircleChart';
import StoreTodayFilteredData from '@/element/components/StoreToday/StoreTodayFilteredData';
import {getStoreData} from '@/service/request/store';

export default {
    name: 'StoreToday',
    components: {
        StoreTodayLineChart,
        StoreTodayFilters,
        StoreTodayCircleChart,
        StoreTodayFilteredData,
    },

    data() {
        return {
            data: [],

            filtersData: [
                {
                    key: 'product_discount_percent',
                    name: 'Discount percentage',
                    type: 'percent',
                    value: 0,
                    slug: '%',
                    slugPosition: 'right',
                },
                {
                    key: 'returned_percent',
                    name: 'Returned percentage',
                    type: 'percent',
                    value: 0,
                    slug: '%',
                    slugPosition: 'right',
                },
                {
                    key: 'new_customers_percent',
                    name: 'New customers percentage',
                    type: 'percent',
                    value: 0,
                    slug: '%',
                    slugPosition: 'right',
                },
                {
                    key: 'new_customers',
                    name: 'New customers',
                    type: 'quantity',
                    value: 0,
                },
                {
                    key: 'avg_profit',
                    name: 'Average profit per order',
                    type: 'currency',
                    value: 0,
                    slug: '€',
                    slugPosition: 'left',
                    active: true,
                },
                {
                    key: 'customers',
                    name: 'Customers',
                    type: 'quantity',
                    value: 0,
                },

                {
                    key: 'avg_total',
                    name: 'Average order value',
                    type: 'currency',
                    value: 0,
                    slug: '€',
                    slugPosition: 'left',
                },
                {
                    key: 'total_packed',
                    name: 'Total packed',
                    type: 'quantity',
                    value: 0,
                },
                {
                    key: 'orders',
                    name: 'Orders',
                    type: 'quantity',
                    value: 0,
                },
                {
                    key: 'product_discount',
                    name: 'Total discount',
                    type: 'currency',
                    value: 0,
                    slug: '€',
                    slugPosition: 'left',
                },
                {
                    key: 'products_count',
                    name: 'Different items sold',
                    type: 'quantity',
                    value: 0,
                },
                {
                    key: 'total_returned',
                    name: 'Total returned',
                    type: 'quantity',
                    value: 0,
                },
            ],

            profit: {
                singleDay: {
                    value: 0,
                    forecast: 0,
                },

                lastDays: {
                    countOfDays: 30,
                    value: 0,
                    forecast: 0,
                },
            },

            revenue: {
                singleDay: {
                    value: 0,
                    forecast: 0,
                },

                lastDays: {
                    countOfDays: 30,
                    value: 0,
                    forecast: 0,
                },
            },

            currency_symbol: '€',
        };
    },

    mounted() {
        this.getData();

        EventBus.on(
            ORDER_UPDATED_EVENT,
            () => {
                if (this.$route.name === 'store-today') {
                    this.socketUpdate();
                }
            },
            this.$options.name
        );

    },

    deactivated() {
        console.log('unmounted...')
    },

    methods: {
        ...mapActions({
            updateSrcData: 'storeToday/updateSrcData',
            updateData: 'storeToday/updateData',
            updateSingleDate: 'filters/updateSingleDate',
        }),

        getData() {
            this.loading = true;

            const date = this.dateFilter ? this.$moment(this.dateFilter) : this.$moment();

            const params = {
                date: [
                    date.startOf('day').format('YYYY-MM-DDTHH:mm'),
                    date.endOf('day').format('YYYY-MM-DDTHH:mm')
                ],

                market: this.marketFilter,
                currency: this.currencyFilter,
                date_granularity: 'Daily',
            };
            getStoreData(params)
                .then((response) => {
                    if (response.data) {
                        if (response.data.kpiData) {
                            const kpiData = response.data.kpiData;

                            for (const prop in kpiData) {
                                const index = this.filtersData.findIndex((item) => item.key === prop);
                                if (index !== -1) {
                                    if (Object.prototype.hasOwnProperty.call(kpiData, prop)) {
                                        if (typeof kpiData[prop] === 'number') {
                                            this.filtersData[index].value = kpiData[prop].toFixed(2);
                                        } else {
                                            this.filtersData[index].value = kpiData[prop] ?? 0;
                                        }
                                    }

                                    if (this.filtersData[index].type === 'currency') {
                                        this.filtersData[index].slug = response.data.currency_symbol;
                                    }
                                }

                            }
                        } else {
                            // reset values of filters
                            for (const prop in this.filtersData) {
                                if (Object.prototype.hasOwnProperty.call(this.filtersData, prop)) {
                                    this.filtersData[prop].value = 0;

                                    if (this.filtersData[prop].type === 'currency') {
                                        this.filtersData[prop].slug = response.data.currency_symbol;
                                    }
                                }
                            }
                        }


                        this.profit = response.data.profit;
                        this.revenue = response.data.revenue;
                        this.data = response.data.data;
                        this.currency_symbol = response.data.currency_symbol;

                        this.updateSrcData(response.data.srcData);


                        const data = [];
                        const activeFilter = this.filtersData.find(kpiFilter => kpiFilter.active);

                        response.data.srcData.map((item) => {
                            data.push({
                                date: item.date,
                                value: item[activeFilter.key],
                                symbol: activeFilter.slug
                            })
                        });

                        this.updateData(data);

                    }
                })
                .finally(() => {
                    this.loading = false;
                    // EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },

        socketUpdate() {
            this.getData();
            EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
        },
    },

    computed: {
        ...mapGetters({
            dateFilter: 'filters/singleDate',
            marketFilter: 'filters/markets',
            currencyFilter: 'filters/currency',
            getSrcData: 'storeToday/getSrcData',
        }),

        srcData: {
            get() {
                return this.getSrcData;
            }
        }
    },

    watch: {
        dateFilter() {
            this.getData();
        },

        marketFilter() {
            this.getData();
        },

        currencyFilter() {
            this.getData();
        },
    },
};
</script>
