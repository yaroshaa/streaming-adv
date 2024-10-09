<!-- eslint-disable -->
<template>
    <div class="stat-block">
        <div class="stat-item">
            <div class="el-grid el-flex el-flex-middle">
                <div class="el-width-expand">
                    <div class="stat-name">Total sales:</div>
                    <div class="stat-subtitle">
                        {{ lastTotals.total_format }}
                        from last 15 min
                    </div>
                </div>
                <div class="">
                    <span
                        class="stat-value"
                        v-text="totals.total_format"
                    ></span>
                </div>
            </div>
        </div>
        <div class="stat-item">
            <div class="el-grid el-flex el-flex-middle">
                <div class="el-width-expand">
                    <div class="stat-name">Orders per hour:</div>
                    <div class="stat-subtitle">
                        {{ lastTotals.orders_count }} from last 15 min
                    </div>
                </div>
                <div class="">
                    <span
                        class="stat-value"
                        v-text="totals.orders_count"
                    ></span>
                </div>
            </div>
        </div>
        <div class="stat-item">
            <div class="el-grid el-flex el-flex-middle">
                <div class="el-width-expand">
                    <div class="stat-name">Profit per hour:</div>
                    <div class="stat-subtitle">
                        {{ lastTotals.profit_format }}
                        from last 15 min
                    </div>
                </div>
                <div class="">
                    <span
                        class="stat-value"
                        v-text="totals.profit_format"
                    ></span>
                </div>
            </div>
        </div>
    </div>
</template>

<!-- eslint-disable -->
<script>
import {mapGetters} from 'vuex';
import EventBus from '@/socket/eventbus';
import {ORDER_UPDATED_EVENT} from '@/socket/events';
import {formatCurrency} from '@/format/format';
import qs from "qs";

export default {
    name: 'FifteenMinTotals',

    data() {
        return {
            totals: {
                profit: 0,
                profit_format: '0',
                orders_count: 0,
                total: 0,
                total_format: '0',
            },
            lastTotals: {
                profit: 0,
                profit_format: '0',
                orders_count: 0,
                total: 0,
                total_format: '0',
            },
        };
    },

    mounted() {
        this.currentCurrency = this.currencyFilter.code;

        EventBus.on(
            ORDER_UPDATED_EVENT,
            (e) => {
                if (this.$route.name === 'orders') {
                    this.update();
                }
            },
            this.$options.name
        );

        this.update();
    },

    methods: {
        update() {
            axios
                .get('/fifteen-min-totals', {
                    params: {
                        market: this.marketFilter,
                        currency: this.currencyFilter,
                        orderStatus: this.orderStatusFilter,
                    },
                    paramsSerializer: (params) => qs.stringify(params),
                })
                .then((data) => {
                    if (!data.data.totals) {
                        return;
                    }

                    this.totals = data.data.totals
                        .filter((row) => row.interval === 'current')
                        .shift();
                    this.lastTotals = data.data.totals
                        .filter((row) => row.interval === 'previous')
                        .shift();

                    this.lastTotals = {
                        profit: this.totals.profit - this.lastTotals.profit,
                        profit_format: formatCurrency(
                            this.totals.profit - this.lastTotals.profit,
                            this.currentCurrency
                        ),
                        orders_count:
                            this.totals.orders_count -
                            this.lastTotals.orders_count,
                        total: this.totals.total - this.lastTotals.total,
                        total_format: formatCurrency(
                            this.totals.total - this.lastTotals.total,
                            this.currentCurrency
                        ),
                    };
                    this.totals.profit_format = formatCurrency(
                        this.totals.profit,
                        this.currentCurrency
                    );
                    this.totals.total_format = formatCurrency(
                        this.totals.total,
                        this.currentCurrency
                    );

                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },
    },

    computed: {
        ...mapGetters({
            marketFilter: 'filters/markets',
            currencyFilter: 'filters/currency',
            orderStatusFilter: 'filters/orderStatuses',
        }),
    },

    watch: {
        marketFilter() {
            this.update();
        },

        currencyFilter(value) {
            this.currentCurrency = value.code;
            this.update();
        },

        orderStatusFilter() {
            this.update();
        },
    },
};
</script>

<style lang="scss">
.stat-block {
    padding: 30px;

    .stat-item {
        & + .stat-item {
            margin-top: 25px;
        }
        .stat-name {
            font-size: 20px;
            font-weight: 600;
            line-height: 24px;
            color: $--color-white;
        }
        .stat-subtitle {
            //color: $--color-purple-blue;
            color: black;
        }
        .stat-value {
            font-size: 38px;
            font-weight: 600;
            //color: $--color-electric-green;
            color: $--color-info;
        }
    }
}
</style>
