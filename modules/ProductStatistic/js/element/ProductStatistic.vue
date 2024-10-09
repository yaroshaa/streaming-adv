<!-- eslint-disable -->
<template>
    <div>
        <el-row>
            <el-col :span="12">
                <products-table
                    :table-columns="table.columns"
                    :loading="tableLoading"
                >
                </products-table>
            </el-col>
            <el-col :span="12">
                <el-row>
                    <el-col :span="24">
                        <price-dynamic-chart
                            :loading="dynamicLoading"
                        />
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="24">
                        <profit-dynamic-chart
                            :loading="dynamicLoading"
                        />
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="24">
                        <quantities-dynamic-chart
                            :loading="dynamicLoading"
                        />
                    </el-col>
                </el-row>
                <el-row>
                    <el-col :span="24">
                        <orders-dynamic-chart
                            :loading="dynamicLoading"
                        />
                    </el-col>
                </el-row>
            </el-col>
        </el-row>
    </div>
</template>

<script>
import EventBus from '@/socket/eventbus';
import {ORDER_UPDATED_EVENT} from '@/socket/events';

import ProductsTable from './ProductsTable';
import OrdersDynamicChart from './OrdersDynamicChart';
import PriceDynamicChart from './PriceDynamicChart';
import ProfitDynamicChart from './ProfitDynamicChart';
import QuantitiesDynamicChart from './QuantitiesDynamicChart';
import productsFields from '@/data/productsFields';
import {getDataDynamic, getProductsList} from '@/service/request/products';
import {mapActions, mapGetters} from 'vuex';
// import qs from 'qs';

export default {
    name: 'ProductStatistic',
    components: {
        ProductsTable ,
        PriceDynamicChart,
        ProfitDynamicChart,
        OrdersDynamicChart,
        QuantitiesDynamicChart,
    },

    data() {
        return {
            tableLoading: false,
            dynamicLoading: false,
            table: {
                columnsMap: {
                    date: 0,
                    name: 1,
                    price: 2,
                    weight: 3
                },

                columns: productsFields,
            },

            productsList: [],
            productsTotal:0
        };
    },

    mounted() {
        EventBus.on(
            ORDER_UPDATED_EVENT,
            () => {
                if (this.$route.name !== 'product-statistic') {
                    return;
                }
                this.getDynamic();
            },
            this.$options.name
        );

        this.getProducts();
    },

    methods: {
        ...mapActions({
            updateDate: 'filters/updateDate',
            setProducts: 'products/setProducts',
            setCurrentProduct: 'products/setCurrentProduct',
            setProductDynamic: 'products/setProductDynamic',
        }),

        getProducts() {
            this.tableLoading = true;
            const params = {
                filter: {
                    currency: this.currencyFilter,
                    search: this.searchFilter,
                    date: this.dateFilter,
                }
            };

            getProductsList(params)
                .then((response) => {
                    if (response.data) {
                        this.productsList = response.data.products
                        this.productsTotal = response.data.products.length
                        this.setProducts(response.data.products);
                    }
                })
                .finally(() => {
                    this.tableLoading = false;
                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },

        getDynamic() {
            this.dynamicLoading = true;
            const params = {
                filter: {
                    remote_id: this.currentProduct.remote_id,
                    currency: this.currencyFilter,
                    date: this.dateFilter,
                    market: this.marketFilter,
                },
            };

            getDataDynamic(params)
                .then((response) => {
                    if (response.data) {
                        this.setProductDynamic(response.data.data);
                    }
                })
                .finally(() => {
                    this.dynamicLoading = false;
                    EventBus.unlock(ORDER_UPDATED_EVENT, this.$options.name);
                });
        },
    },

    computed: {
        ...mapGetters({
            marketFilter: 'filters/markets',
            dateFilter: 'filters/date',
            searchFilter: 'filters/search',
            currencyFilter: 'filters/currency',
            currentProduct: 'products/currentProduct',
            productDynamic: 'products/dynamic',
            products: 'products/products',
        }),
    },

    watch: {
        dateFilter() {
            this.getProducts();
            this.getDynamic();
        },

        marketFilter() {
            this.getDynamic();
        },

        currencyFilter() {
            this.getProducts();
            this.getDynamic();
        },

        searchFilter() {
            this.getProducts();
        },

        currentProduct() {
            this.getDynamic();
        }
    },
}
</script>

<style scoped>
</style>
