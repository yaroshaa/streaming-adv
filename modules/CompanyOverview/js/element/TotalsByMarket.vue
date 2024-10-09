<!-- eslint-disable -->
<template>
    <div id="totals-by-market" v-if="receivedData">
        <div
            v-for="(markerDataItem, index) in marketData"
            :key="index"
            :class="'value-block market-' + markerDataItem.field"
        >
            <div class="title-block">{{ markerDataItem.title }}</div>
            <hr style="width: 100%" />
            <div class="num-and-diff">
                <div class="number-block">
                    {{ format(markerDataItem.format, receivedData[markerDataItem.field].current) }}
                </div>
                <div
                    v-show="receivedData[markerDataItem.field].sign !== 0"
                    :class="[
                        'diff-block',
                        receivedData[markerDataItem.field].sign === 1 ? 'color-green' : '',
                        receivedData[markerDataItem.field].sign === -1 ? 'color-red' : '',
                        markerDataItem.reverseSign ? 'reverse-sign' : '',
                    ]"
                >
                    <el-tooltip
                        :content="
                            (receivedData[markerDataItem.field].sign === 1
                                ? '+ '
                                : receivedData[markerDataItem.field].sign === -1
                                    ? '- '
                                    : '') +
                                // eslint-disable-next-line vue/max-len
                                format(markerDataItem.format, receivedData[markerDataItem.field].diff) +
                                ' from previous period'
                        "
                        placement="bottom"
                        effect="light"
                    >
                        <p>
                            <i
                                :class="
                                    receivedData[markerDataItem.field].sign === 1
                                        ? 'el-icon-caret-top'
                                        : receivedData[markerDataItem.field].sign === -1
                                            ? 'el-icon-caret-bottom'
                                            : ''
                                "
                            ></i>
                            {{ format('percent', receivedData[markerDataItem.field].diff_percentage) }}
                        </p>
                    </el-tooltip>
                </div>
            </div>
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
    name: 'TotalsByMarket',
    components: {
        ElTooltip: Tooltip,
    },

    data: function () {
        return {
            receivedData: null,
            marketData: [
                {field: 'avg_total', title: 'Average order value', format: 'currency'},
                {field: 'avg_profit', title: 'Average profit per order', format: 'currency'},
                {field: 'product_discount', title: 'Total discount', format: 'currency', reverseSign: true},
                {field: 'product_discount_percent', title: 'Discount percentage', format: 'percent', reverseSign: true},
                {field: 'total_packed', title: 'Total packed', format: 'number'},
                {field: 'time_packed', title: 'Average sec to send order', format: 'number'},
                {field: 'total_returned', title: 'Total returned', format: 'number', reverseSign: true},
                {field: 'returned_percent', title: 'Returned percentage', format: 'percent', reverseSign: true},
                {field: 'customers', title: 'Customers', format: 'number'},
                {field: 'new_customers', title: 'New customers', format: 'number'},
                {field: 'new_customers_percent', title: 'New customers percentage', format: 'percent'},
                {field: 'orders', title: 'Orders', format: 'number'},
                {field: 'products_count', title: 'Different items sold', format: 'number'},
            ],

            formatters: {
                currency: () =>
                    new Intl.NumberFormat('en', {
                        style: 'currency',
                        currency: this.currencyFilter.code,
                        notation: 'compact',
                    }),

                number: () => new Intl.NumberFormat('en'),
                percent: () =>
                    new Intl.NumberFormat('en', {
                        style: 'percent',
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2,
                    }),
            },

            formattersInitiated: {},
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
        getFormatter(type) {
            if (!Object.prototype.hasOwnProperty.call(this.formattersInitiated, type)) {
                this.formattersInitiated[type] = this.formatters[type]();
            }

            return this.formattersInitiated[type];
        },

        update() {
            this.axios
                .get('/company-overview/totals-by-market', {
                    params: {
                        currency: this.currencyFilter,
                        date: this.dateFilter,
                    },
                    paramsSerializer: (params) => qs.stringify(params),
                })
                .then((response) => {
                    this.receivedData = response.data;
                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },

        format(type, value) {
            return this.getFormatter(type).format(value);
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
#totals-by-market {
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: flex-start;
    align-items: flex-end;
    margin: 5px;
    padding: 10px;
    //color: #fff;
    color: #000000;

    .market-title {
        font-size: 28px;
        margin-bottom: 7px;
    }

    .value-block {
        display: flex;
        flex-direction: column;
        align-items: baseline;
        margin-bottom: 10px;
        flex: 1 1 auto;
        min-width: 13%;

        .num-and-diff {
            display: flex;
            align-items: baseline;
            flex-direction: column;
            min-height: 64px;
        }

        .title-block {
            font-size: 14px;
            width: 55%;
            //color: $--color-purple-blue;
            color: black;
        }

        .number-block {
            font-size: 30px;
        }

        .diff-block {
            font-size: 10px;
            margin-left: 3px;

            p {
                margin: 0;
            }
        }

        .color-red {
            color: red;
        }
        .color-red.reverse-sign {
            color: green;
        }

        .color-green {
            color: green;
        }
        .color-green.reverse-sign {
            color: red;
        }

        hr {
            margin: 5px 0;
        }
    }

}
</style>
