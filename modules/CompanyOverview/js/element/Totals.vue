<!-- eslint-disable -->
<template>
    <div id="totals">
        <div class="profit-block totals-block">
            <div>
                <div class="title">PROFIT:</div>
                <div class="diff" :class="profit.sign === 1 ? 'color-green' : profit.sign === -1 ? 'color-red' : ''">
                    <el-tooltip
                        :content="
                            (profit.sign === 1 ? '+ ' : profit.sign === -1 ? '- ' : '') +
                                profit.diffNum +
                                ' from previous period'
                        "
                        placement="bottom"
                        effect="light"
                    >
                        <p>
                            <i
                                :class="
                                    profit.sign === 1
                                        ? 'el-icon-caret-top'
                                        : profit.sign === -1
                                            ? 'el-icon-caret-bottom'
                                            : ''
                                "
                            ></i>
                            {{ profit.diff }} %
                        </p>
                    </el-tooltip>
                </div>
            </div>
            <div class="big-number">{{ profit.value }}</div>
        </div>
        <div class="revenue-block totals-block">
            <div>
                <div class="title">REVENUE:</div>
                <div class="diff" :class="revenue.sign === 1 ? 'color-green' : revenue.sign === -1 ? 'color-red' : ''">
                    <el-tooltip
                        :content="
                            (revenue.sign === 1 ? '+ ' : revenue.sign === -1 ? '- ' : '') +
                                revenue.diffNum +
                                ' from previous period'
                        "
                        placement="bottom"
                        effect="light"
                    >
                        <p>
                            <i
                                :class="
                                    revenue.sign === 1
                                        ? 'el-icon-caret-top'
                                        : revenue.sign === -1
                                            ? 'el-icon-caret-bottom'
                                            : ''
                                "
                            ></i>
                            {{ revenue.diff }} %
                        </p>
                    </el-tooltip>
                </div>
            </div>
            <div class="big-number">{{ revenue.value }}</div>
        </div>
        <div class="orders-count-block totals-block">
            <div>
                <div class="title">ORDERS:</div>
                <div
                    class="diff"
                    :class="ordersCnt.sign === 1 ? 'color-green' : ordersCnt.sign === -1 ? 'color-red' : ''"
                >
                    <el-tooltip
                        :content="
                            (ordersCnt.sign === 1 ? '+ ' : ordersCnt.sign === -1 ? '- ' : '') +
                                ordersCnt.diffNum +
                                ' from previous period'
                        "
                        placement="bottom"
                        effect="light"
                    >
                        <p>
                            <i
                                :class="
                                    ordersCnt.sign === 1
                                        ? 'el-icon-caret-top'
                                        : ordersCnt.sign === -1
                                            ? 'el-icon-caret-bottom'
                                            : ''
                                "
                            ></i>
                            {{ ordersCnt.diff }} %
                        </p>
                    </el-tooltip>
                </div>
            </div>
            <div class="big-number">{{ ordersCnt.value }}</div>
        </div>
    </div>
</template>

<script>
import {mapGetters} from 'vuex';
import EventBus from '@/socket/eventbus';
import {ORDER_UPDATED_EVENT} from '@/socket/events';
import {Tooltip} from 'element-ui';
import qs from 'qs';

export default {
    name: 'CompanyOverviewTotals',
    components: {
        ElTooltip: Tooltip,
    },

    data() {
        return {
            profit: {
                value: 0,
                diff: 0,
                sign: 0,
                diffNum: 0,
            },

            revenue: {
                value: 0,
                diff: 0,
                sign: 0,
                diffNum: 0,
            },

            ordersCnt: {
                value: 0,
                diff: 0,
                sign: 0,
                diffNum: 0,
            },
        };
    },

    mounted() {
        this.update();

        EventBus.on(
            ORDER_UPDATED_EVENT,
            () => {
                if (this.$route.name === 'company-overview') {
                    this.update();
                }
            },
            this.$options.name
        );
    },

    methods: {
        update() {
            this.axios
                .get('/company-overview/totals', {
                    params: {
                        currency: this.currencyFilter,
                        date: this.dateFilter,
                    },
                    paramsSerializer: (params) => qs.stringify(params),
                })
                .then((response) => {
                    if (
                        !Object.prototype.hasOwnProperty.call(response.data, 'current') ||
                        !Object.prototype.hasOwnProperty.call(response.data, 'previous')
                    ) {
                        return;
                    }

                    let currencyFormat = new Intl.NumberFormat('en', {
                        style: 'currency',
                        currency: this.currencyFilter.code,
                        notation: 'compact',
                    });
                    let numberFormat = new Intl.NumberFormat('en');

                    this.profit.value = currencyFormat.format(response.data.current.profit);
                    this.revenue.value = currencyFormat.format(response.data.current.total);
                    this.ordersCnt.value = numberFormat.format(response.data.current.orders_count);

                    this.profit.sign =
                        response.data.previous.profit > response.data.current.profit
                            ? -1
                            : response.data.previous.profit === response.data.current.profit
                            ? 0
                            : 1;
                    this.revenue.sign =
                        response.data.previous.total > response.data.current.total
                            ? -1
                            : response.data.previous.total === response.data.current.total
                            ? 0
                            : 1;
                    this.ordersCnt.sign =
                        response.data.previous.orders_count > response.data.current.orders_count
                            ? -1
                            : response.data.previous.orders_count === response.data.current.orders_count
                            ? 0
                            : 1;

                    this.profit.diff = (
                        ((this.profit.sign
                            ? response.data.current.profit / response.data.previous.profit
                            : response.data.previous.profit / response.data.current.profit) -
                            1) *
                        100
                    ).toFixed(2);

                    this.revenue.diff = (
                        ((this.revenue.sign
                            ? response.data.current.total / response.data.previous.total
                            : response.data.previous.total / response.data.current.total) -
                            1) *
                        100
                    ).toFixed(2);

                    this.ordersCnt.diff = (
                        ((this.ordersCnt.sign
                            ? response.data.current.orders_count / response.data.previous.orders_count
                            : response.data.previous.orders_count / response.data.current.orders_count) -
                            1) *
                        100
                    ).toFixed(2);

                    this.profit.diffNum = currencyFormat.format(
                        Math.abs(response.data.current.profit - response.data.previous.profit)
                    );
                    this.revenue.diffNum = currencyFormat.format(
                        Math.abs(response.data.current.total - response.data.previous.total)
                    );
                    this.ordersCnt.diffNum = numberFormat.format(
                        Math.abs(response.data.current.orders_count - response.data.previous.orders_count)
                    );

                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },
    },

    computed: {
        ...mapGetters({
            currencyFilter: 'filters/currency',
            dateFilter: 'filters/date',
        }),
    },

    watch: {
        currencyFilter() {
            this.update();
        },

        dateFilter() {
            this.update();
        },
    },
};
</script>

<style lang="scss" scoped>
#totals {
    color: #ffffff;
    display: flex;
    height: 150px;
    margin-top: 5px;
    padding: 20px 0 20px 0;
    margin-left: 5px;

    .totals-block {
        width: 33.3%;
        display: flex;
        flex-direction: row;
        align-items: center;
        justify-content: space-evenly;

        .big-number {
            font-size: 48px;
        }

        .title {
            font-size: 22px;
        }

        .diff {
            font-size: 18px;
            p {
                margin: 0;
            }
        }

        .diff.color-green {
            color: green;
        }

        .diff.color-red {
            color: red;
        }
    }
}
</style>
