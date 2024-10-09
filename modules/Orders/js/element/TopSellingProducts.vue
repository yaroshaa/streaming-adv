<!-- eslint-disable -->
<template>
    <div class="el-margin-sm-top">
        <div
            class="el-height-sm"
            v-if="state.isLoading"
            v-loading="state.isLoading"
        ></div>
        <div
            v-else-if="productsTop.length"
            class="el-grid el-grid-sm el-child-width-1-5"
        >
            <div
                class="product-item"
                v-for="(product, index) in productsTop"
                :key="product.product_variant_id + index"
            >
                <div class="top-section">
                    <el-tooltip
                        :content="product.market_name"
                        placement="right"
                    >
                        <span class="badge-market">
                            <img
                                class="xxxx-market-icon"
                                :src="product.market_icon_link"
                                :alt="product.market_name"
                                rel="icon"
                            />
                        </span>
                    </el-tooltip>
                    <span
                        class="badge-quantity"
                        v-text="product.product_qty"
                    ></span>
                    <a
                        :href="product.product_link"
                        target="_blank"
                        class="external-link"
                    >
                        <i class="el-icon-top-right"></i>
                    </a>
                    <div class="thumbnail" @click="applyFilter(product)">
                        <div class="centered">
                            <img
                                class=""
                                :src="product.product_image_link"
                                :alt="product.product_name"
                            />
                        </div>
                    </div>
                </div>
                <div class="bottom-section">
                    <div
                        class="name"
                        v-text="product.product_name"
                        :title="product.product_name"
                    ></div>
                    <div class="properties">
                        <dl>
                            <dt class="prop-name">Profit:</dt>
                            <dd
                                class="prop-value"
                                v-text="product.product_profit_format"
                            ></dd>
                            <dt class="prop-name">Weight:</dt>
                            <dd
                                class="prop-value"
                                v-text="product.product_weight"
                            ></dd>
                        </dl>
                    </div>
                </div>
            </div>
        </div>
        <div
            class="el-height-sm"
            style="text-align: center; line-height: 150px"
            v-else
        >
            Product list is empty
        </div>
    </div>
</template>

<!-- eslint-disable -->
<script>
import EventBus from '@/socket/eventbus';
import {mapGetters, mapActions} from 'vuex';
import {ORDER_UPDATED_EVENT} from '@/socket/events';
import {formatCurrency} from '@/format/format';
import qs from "qs";

export default {
    name: 'TopSellingProducts',

    data() {
        return {
            productsTopRaw: [],
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
        ...mapActions({
            filterOrders: 'orders/filter',
            setOrders: 'orders/setOrders',
            updateProduct: 'filters/updateProduct',
            backupFilters: 'filters/backupFilters',
        }),

        applyFilter(product) {
            this.filterOrders((x) => false); // backup current feed
            this.updateProduct({remoteId: product.product_variant_id});
        },

        update() {
            axios
                .get('/top-selling-products', {
                    params: {
                        market: this.marketFilter,
                        currency: this.currencyFilter,
                        percentile: this.percentileFilter,
                        weight: this.weightFilter,
                        orderStatus: this.orderStatusFilter,
                    },
                    paramsSerializer: (params) => qs.stringify(params),
                })
                .then((response) => {
                    this.state.isLoading = false;
                    this.productsTopRaw = Object.values(response.data);
                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },
    },

    computed: {
        ...mapGetters({
            marketFilter: 'filters/markets',
            currencyFilter: 'filters/currency',
            percentileFilter: 'filters/percentile',
            weightFilter: 'filters/weight',
            orderStatusFilter: 'filters/orderStatuses',
        }),

        productsTop() {
            return _.chain(this.productsTopRaw)
                .each((product) => {
                    product.product_profit_format = formatCurrency(
                        product.product_profit,
                        this.currentCurrency
                    );
                })
                .value();
        },
    },

    watch: {
        marketFilter() {
            this.update();
        },

        currencyFilter(value) {
            this.currentCurrency = value.code;
            this.update();
        },

        percentileFilter() {
            this.update();
        },

        weightFilter() {
            this.update();
        },

        orderStatusFilter() {
            this.update();
        },
    },
};
</script>

<style lang="scss">
.product-item {
    .top-section {
        text-align: center;
        color: $--color-white;
        position: relative;
        overflow: hidden;
        border-radius: $--border-radius-base;
        height: 170px;

        .badge-market {
            display: block;
            width: 30px;
            height: 30px;
            position: absolute;
            top: 10px;
            left: 12px;
            border-radius: 50%;
            background-color: $--color-white;
            overflow: hidden;
            z-index: 1;
            img {
                max-width: 100%;
                padding: 5px;
            }
        }
        .badge-quantity {
            font-weight: bold;
            line-height: 28px;
            display: block;
            width: 30px;
            height: 30px;
            position: absolute;
            top: 10px;
            right: 12px;
            background-color: $--color-base-dark;
            border-radius: 50%;
            z-index: 1;
        }
        .external-link {
            font-size: $--font-size-large;
            line-height: 38px;
            color: inherit;
            display: block;
            width: 38px;
            height: 38px;
            position: absolute;
            bottom: 14px;
            right: 12px;
            background-color: $--color-primary;
            border-radius: $--border-radius-base;
            z-index: 1;
            transition: all 0.3s ease-in-out;
            &:hover {
                background-color: $--color-primary-light-3;
            }
        }
        .thumbnail {
            overflow: hidden;
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            opacity: 1;
            transition: opacity 0.1s;
            background-color: $--background-placeholder;

            &::after {
                content: '';
                display: block;
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                overflow: hidden;
            }

            .centered {
                position: absolute;
                top: 0;
                left: 0;
                width: 100%;
                height: 100%;
                transform: translate(50%, 50%);

                img {
                    position: absolute;
                    top: 0;
                    left: 0;
                    // contain by height and width
                    // max-height: 100%;
                    // max-width: none;
                    // contain by width
                    max-width: 100%;
                    transform: translate(-50%, -50%);
                }
            }
        }
        .photo {
            height: inherit;
            background-color: $--background-placeholder;
            img {
                width: 100%;
            }
        }
    }
    .bottom-section {
        padding-top: 12px;
        padding-bottom: 40px;

        .name {
            color: $--color-text-regular;
            line-height: 20px;
            height: 40px;
            overflow: hidden;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        .properties {
            margin-top: 12px;
            dl {
                margin: 0;
            }
            dd + dt {
                margin-left: 12px;
            }
            .prop-name {
                font-weight: normal;
                color: $--color-purple-blue;
                display: inline;
                margin-right: 5px;
            }
            .prop-value {
                font-weight: bold;
                color: $--color-white;
                display: inline;
            }
        }
    }
}
</style>
