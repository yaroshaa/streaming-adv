<template>
    <div>
        <h4>Orders</h4>
        <div
            class="el-height-sm"
            v-if="state.isLoading"
            v-loading="state.isLoading"
        ></div>
        <div
            class="orders-list"
            v-else-if="computedOrders.length"
            v-infinite-scroll="scrollLoad"
            infinite-scroll-disabled="loading"
            infinite-scroll-immediate="false"
            infinite-scroll-delay="500"
        >
            <div v-if="isFiltered" class="reset-feed-filter">
                <i class="el-icon-close"></i>
                <a @click="resetFilters">Reset filters</a>
            </div>
            <transition-group name="list">
                <el-card
                    class="order-card"
                    v-for="order in computedOrders"
                    :key="order.order_id"
                >
                    <div slot="header" class="order-card-header">
                        <span class="">
                            <span>#{{ order.order_id }}</span>
                            <span class="market-logo">
                                <img
                                    :src="order.market_icon_link"
                                    :alt="order.market_name"
                                    rel="icon"
                                />
                            </span>
                            <span v-text="order.customer_name"></span>
                        </span>
                        <span class="el-float-right">
                            <el-tag
                                class="el-tag--round order-status"
                                :type="order.status_color"
                                effect="dark"
                                size="mini"
                                v-text="order.status_name"
                            ></el-tag>
                            <small v-text="order.updated_at_format"></small>
                        </span>
                    </div>

                    <table class="order-product-list">
                        <tbody>
                            <tr
                                v-for="product in order.products"
                                :key="product.product_variant_id"
                            >
                                <td class="el-text-truncate product-name">
                                    <span
                                        v-text="
                                            `${product.product_qty} x ${product.product_name}`
                                        "
                                    ></span>
                                </td>
                                <td>
                                    <span
                                        class="el-text-bold"
                                        v-text="product.product_weight + ' kg'"
                                    ></span>
                                </td>
                                <td>
                                    <span
                                        class="el-text-bold"
                                        v-text="product.product_price_format"
                                    ></span>
                                </td>
                                <td>
                                    <span class="prop-name">Profit:</span>
                                    <span
                                        class="el-text-bold"
                                        v-text="product.product_profit_format"
                                    ></span>
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" style="padding-top: 12px">
                                    <div class="el-grid el-grid-xs">
                                        <div class="el-width-auto el-text-bold">
                                            Total:
                                        </div>
                                        <div class="el-width-auto">
                                            <span class="prop-name">
                                                Weight:
                                            </span>
                                            <span
                                                class="prop-value"
                                                v-text="
                                                    order.product_weight + ' kg'
                                                "
                                            ></span>
                                        </div>
                                        <div class="el-width-auto">
                                            <span class="prop-name">
                                                Revenue:
                                            </span>
                                            <span
                                                class="prop-value"
                                                v-text="
                                                    order.product_price_format
                                                "
                                            ></span>
                                        </div>
                                        <div class="el-width-expand">
                                            <span class="prop-name">
                                                Profit:
                                            </span>
                                            <span
                                                class="prop-value"
                                                v-text="
                                                    order.product_profit_format
                                                "
                                            ></span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                </el-card>
            </transition-group>
        </div>
        <div
            style="height: 150px; text-align: center; line-height: 150px"
            v-else
        >
            Order list is empty
        </div>
    </div>
</template>

<script>
import moment from 'moment';
import {mapGetters, mapActions} from 'vuex';
import EventBus from '@/socket/eventbus';
import {ORDER_UPDATED_EVENT} from '@/socket/events';
import {formatCurrency} from '@/format/format';
import {Tag as ElTag, Card as ElCard} from 'element-ui';
import qs from 'qs';

export default {
    name: 'OrdersFeed',

    components: {
        ElCard,
        ElTag,
    },

    data() {
        return {
            limit: 20,
            state: {
                isLoading: false,
            },
        };
    },

    mounted() {
        this.state.isLoading = true;
        this.currentCurrency = this.currencyFilter.code;

        EventBus.on(
            ORDER_UPDATED_EVENT,
            (event) => {
                if (this.$route.name === 'orders') {
                    this.socketUpdate(event.order);
                }
            },
            this.$options.name
        );

        this.initialLoad();
    },

    methods: {
        ...mapActions({
            addOrders: 'orders/addOrders',
            setOrders: 'orders/setOrders',
            resetOrderFilters: 'orders/resetFilters',
        }),

        resetFilters() {
            this.resetOrderFilters();
        },

        socketUpdate(order) {
            const orders = [order];
            this.addOrders(orders);
            EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
        },

        scrollLoad() {
            if (!this.orders.length) {
                return;
            }

            this.load({
                to: moment(this.orders[this.orders.length - 1].updated_at)
                    .utc()
                    .toISOString(true),
            });
        },

        initialLoad() {
            this.load(
                {
                    from: moment().startOf('day').utc().toISOString(true),
                },
                true
            );
        },

        load(params, rewrite = false) {
            this.state.isLoading = true;
            this.axios
                .get('/orders', {
                    params: {
                        limit: this.limit,
                        market: this.marketFilter,
                        orderStatus: this.orderStatusFilter,
                        currency: this.currencyFilter,
                        percentile: this.percentileFilter,
                        product: this.productFilter,
                        weight: this.weightFilter,
                        ...params,
                    },
                    paramsSerializer: (params) => qs.stringify(params),
                })
                .then((data) => {
                    this.state.isLoading = false;
                    let items = Object.values(data.data);
                    rewrite ? this.setOrders(items) : this.addOrders(items);
                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },
    },

    computed: {
        ...mapGetters({
            marketFilter: 'filters/markets',
            orderStatusFilter: 'filters/orderStatuses',
            currencyFilter: 'filters/currency',
            percentileFilter: 'filters/percentile',
            productFilter: 'filters/product',
            orders: 'orders/orders',
            isFiltered: 'orders/isFiltered',
            weightFilter: 'filters/weight',
        }),

        computedOrders() {
            return _.chain(this.orders) // eslint-disable-line
                .each((order) => {
                    order.updated_at_format = moment(order.updated_at).format(
                        'HH:mm:ss'
                    );
                    order.product_price_format = formatCurrency(
                        order.product_price,
                        this.currentCurrency
                    );
                    order.product_profit_format = formatCurrency(
                        order.product_profit,
                        this.currentCurrency
                    );
                    order.products.forEach((product) => {
                        product.product_price_format = formatCurrency(
                            product.product_price,
                            this.currentCurrency
                        );
                        product.product_profit_format = formatCurrency(
                            product.product_profit,
                            this.currentCurrency
                        );
                    });
                })
                .value();
        },
    },

    watch: {
        marketFilter() {
            this.initialLoad();
        },

        orderStatusFilter() {
            this.initialLoad();
        },

        currencyFilter(value) {
            this.currentCurrency = value.code;
            this.initialLoad();
        },

        percentileFilter() {
            this.initialLoad();
        },

        productFilter() {
            this.initialLoad();
        },

        weightFilter() {
            this.initialLoad();
        },
    },
};
</script>

<style lang="scss">
.orders-list {
    overflow: auto;
    height: 1656px;

    .reset-feed-filter {
        margin-bottom: 10px;
        cursor: pointer;
        color: $--color-white;
    }

    .order-card {
        color: $--color-white;
        margin-bottom: 10px;

        .market-logo {
            width: 20px;
            height: 20px;
            display: inline-block;
            background-color: $--color-white;
            border-radius: 20px;
            line-height: 18px;
            margin-left: 20px;
            margin-right: 7px;

            img {
                max-width: 100%;
                padding: 4px;
            }
        }

        .order-status {
            padding-left: 12px;
            padding-right: 12px;
            vertical-align: 2px;
            margin-right: 12px;
        }
    }

    .list-item {
        display: inline-block;
        margin-right: 10px;
    }
    .list-enter-active,
    .list-leave-active {
        transition: all 1s;
    }
    .list-enter,
    .list-leave-to {
        opacity: 0;
        transform: translateX(-30px);
    }

    &::-webkit-scrollbar {
        width: 0;
        background: transparent; /* make scrollbar transparent */
    }
}

.order-product-list {
    width: 100%;
    color: $--color-white;

    .product-name {
        max-width: 150px;
    }

    tbody {
        td {
            padding-top: 5px;
            padding-bottom: 10px;
        }
        td + td {
            padding-left: 5px;
        }
    }

    tfoot {
        border-top: 1px solid $--color-purple-blue;
    }

    .prop-name {
        color: $--color-purple-blue;
        margin-right: 5px;
    }
    .prop-value {
        font-weight: bold;
    }
}

.el-card__body {
    padding: 10px !important;
}

.el-card__header {
    padding: 10px !important;
    border: none;
}
</style>
