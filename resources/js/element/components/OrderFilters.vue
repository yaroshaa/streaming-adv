<!-- eslint-disable -->
<template>
    <div class="filters-block">
        <el-input
            class="filters-block-element"
            ref="input"
            key="key-search"
            v-if="searchFilterEnabled"
            v-model="search"
            style="width: 300px"
            placeholder="Search"
        >
        </el-input>
        <el-date-picker
            v-if="dateFilterEnabled"
            v-model="date"
            type="daterange"
            :picker-options="pickerOptions"
            range-separator="To"
            start-placeholder="Start date"
            end-placeholder="End date"
            class="filters-block-element"
        >
        </el-date-picker>
        <el-date-picker
            v-if="singleDateFilterEnabled"
            v-model="singleDate"
            type="date"
            class="filters-block-element"
        >
        </el-date-picker>
        <el-select
            v-if="marketFilterEnabled"
            class="filters-block-element"
            v-model="market"
            multiple
            placeholder="Market"
            collapse-tags
            value-key="remote_id"
            clearable
            key="key-market"
        >
            <el-option
                v-for="market in markets"
                :label="market.name"
                :value="market"
                :key="market.remote_id"
            >
            </el-option>
        </el-select>
        <el-select
            v-if="sourceFilterEnabled"
            class="filters-block-element"
            v-model="source"
            multiple
            placeholder="Source"
            collapse-tags
            value-key="remote_id"
            clearable
            key="key-source"
        >
            <el-option
                v-for="source in sources"
                :label="source.name"
                :value="source"
                :key="source.remote_id"
            >
            </el-option>
        </el-select>
        <el-select
            v-if="orderStatusFilterEnabled"
            class="filters-block-element"
            v-model="orderStatus"
            multiple
            placeholder="Status"
            collapse-tags
            value-key="id"
            clearable
            key="key-order-status"
        >
            <el-option
                v-for="orderStatus in orderStatuses"
                :key="orderStatus.id"
                :label="orderStatus.name"
                :value="orderStatus"
            >
                <span
                    class="status-dot"
                    :style="
                        'background-color: ' + (orderStatus.color || '#fffff')
                    "
                ></span>
                <span>{{ orderStatus.name }}</span>
            </el-option>
        </el-select>
        <el-select
            v-if="percentileFilterEnabled"
            class="filters-block-element"
            v-model="percentile"
            placeholder="Choose percentile"
            value-key="value"
            clearable
            key="key-percentile"
        >
            <el-option
                v-for="p in topBottomPercentile"
                :key="p.value"
                :label="p.label"
                :value="p.value"
            >
            </el-option>
        </el-select>
        <el-popover
                v-if="weightFilterEnabled"
                class="filters-block-element"
                placement="bottom"
                width="160"
                v-model="weightFilter.visible"
            >
                <div>
                    <el-input
                        key="greater_than"
                        placeholder="0.00"
                        v-model="weightFilter.greater_than"
                        class="weight-value"
                    >
                        <template slot="prepend" class="weight-label"> >=</template>
                    </el-input>
                    <el-input
                        key="lower_than"
                        placeholder="0.00"
                        v-model="weightFilter.lower_than"
                        class="weight-value"
                    >
                        <template slot="prepend" class="weight-label"> <=</template>
                    </el-input>
                    <el-button
                        type="primary"
                        size="mini"
                        @click="weightFilterApply"
                        class="weight-confirm"
                    >Ok</el-button
                    >
                    <el-button
                        type="default"
                        size="mini"
                        @click="resetWeightFilter"
                        class="weight-confirm"
                    >Reset
                    </el-button>
                </div>
                <el-button
                    slot="reference"
                    class="el-button--transparent weight-button"
                ><span>{{ weightLabel }}</span>
                    <i
                        :class="
                        weightFilter.visible
                            ? 'el-icon-arrow-up'
                            : 'el-icon-arrow-down'
                    "
                    ></i>
                </el-button>
            </el-popover>
        <el-select
            v-if="dateGranularityFilterEnabled"
            class="filters-block-element"
            v-model="dateGranularity"
            key="key-date-granularity"
        >
            <el-option
                v-for="dateGranularity in dateGranularityOptions"
                :key="dateGranularity"
                :label="dateGranularity"
                :value="dateGranularity"
            >
            </el-option>
        </el-select>

        <el-select
            v-if="kpiOverviewFilterEnabled"
            v-model="kpiOverviewActiveFields"
            class="filters-block-element"
            placeholder="Select fields"
            key="key-kpi-overview"
        >
            <el-option
                v-for="item in kpiFieldsFilterable"
                :key="item.key"
                :label="item.label"
                :value="item.key"
            >
            </el-option>
        </el-select>

        <el-select
            v-if="currencyFilterEnabled"
            class="filters-block-element"
            v-model="currency"
            placeholder="Currency"
            value-key="id"
            @change="loadCurrencies"
            key="key-currency"
        >
            <el-option
                v-for="currency in currencies"
                :key="currency.id"
                :label="currency.name"
                :value="currency"
            >
            </el-option>
        </el-select>
        <div
            v-if="currencyFilterEnabled"
            class="filters-block-element"
        >
            <span v-for="rate in rates" class="rates">
                {{ currency.code }}/{{ rate.to.code }} = {{ rate.rate }}
            </span>
        </div>
    </div>
</template>

<!-- eslint-disable -->
<script>
import {mapActions, mapGetters} from 'vuex';
import kpiFields from '@/data/kpiFields';

export default {
    name: 'OrderFilters',

    props: {
        weightFilterEnabled: {
            type: Boolean,
            default: false,
        },
        marketFilterEnabled: {
            type: Boolean,
            default: false,
        },
        sourceFilterEnabled: {
            type: Boolean,
            default: false,
        },
        currencyFilterEnabled: {
            type: Boolean,
            default: false,
        },
        orderStatusFilterEnabled: {
            type: Boolean,
            default: false,
        },
        percentileFilterEnabled: {
            type: Boolean,
            default: false,
        },
        dateFilterEnabled: {
            type: Boolean,
            default: false,
        },
        dateDefault: {
            type: Array,
            default: function () {
                return [
                    this.$moment().subtract(1, 'month').startOf('day').toDate(),
                    this.$moment().endOf('day').toDate(),
                ];
            },
        },
        singleDateDefault: {
            type: Date,
            default: function () {
                return new Date();
            },
        },
        dateGranularityFilterEnabled: {
            type: Boolean,
            default: false,
        },
        singleDateFilterEnabled: {
            type: Boolean,
            default: false,
        },
        kpiOverviewFilterEnabled: {
            type: Boolean,
            default: false,
        },
        searchFilterEnabled: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            kpiFields,
            weightFilter: {
                visible: false,
                lower_than: null,
                greater_than: null,
            },
            markets: [],
            sources: [],
            currencies: [],
            orderStatuses: [],
            rates: [],
            weightLabel: 'Weight',
            topBottomPercentile: [
                // {
                //     value: null,
                //     label: 'Choose percentile'
                // },
                {
                    value: 0.95,
                    label: 'Top 5% profit',
                },
                {
                    value: 0.05,
                    label: 'Bottom 5% profit',
                },
            ],
            pickerOptions: {
                shortcuts: [
                    {
                        text: 'Last week',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(
                                start.getTime() - 3600 * 1000 * 24 * 7
                            );
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'Last month',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(
                                start.getTime() - 3600 * 1000 * 24 * 30
                            );
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'This month',
                        onClick(picker) {
                            const end = new Date();
                            const start = Vue.$moment()
                                .startOf('month')
                                .toDate();
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'Last 3 months',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(
                                start.getTime() - 3600 * 1000 * 24 * 90
                            );
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'Year to date',
                        onClick(picker) {
                            const end = new Date();
                            const start = Vue.$moment()
                                .subtract(1, 'year')
                                .toDate();
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'This year',
                        onClick(picker) {
                            const end = new Date();
                            const start = Vue.$moment()
                                .startOf('year')
                                .toDate();
                            picker.$emit('pick', [start, end]);
                        },
                    },
                ],
            },
            dateGranularityOptions: [],
        };
    },

    methods: {
         ...mapActions({
            updateMarkets: 'filters/updateMarkets',
            updateSources: 'filters/updateSources',
            updateCurrency: 'filters/updateCurrency',
            updateStatus: 'filters/updateOrderStatuses',
            resetOrdersFilter: 'orders/resetFilters',
            updatePercentile: 'filters/updatePercentile',
            resetProductsFilter: 'filters/resetProduct',
            updateWeight: 'filters/updateWeight',
            updateDate: 'filters/updateDate',
            updateSingleDate: 'filters/updateSingleDate',
            updateDateGranularity: 'filters/updateDateGranularity',
            updateKpiOverviewActiveFields:
                'filters/updateKpiOverviewActiveFields',
            updateSearch: 'filters/updateSearch',

        }),
        loadCurrencies() {
            axios.get('/exchange-rates/' + this.currency.id).then((data) => {
                this.rates = data.data
                    .filter((x) => x.to.id !== this.currency.id)
                    .map((x) => {
                        x.rate = x.rate.toFixed(2);

                        return x;
                    });
            });
        },
        weightFilterApply() {
            this.weightFilter.visible = false;
            this.resetOrdersFilter();
            this.resetProductsFilter();
            this.updateWeight({
                greater_than: this.weightFilter.greater_than,
                lower_than: this.weightFilter.lower_than,
            });

            this.weightLabel = 'weight';

            if (this.weightFilter.greater_than) {
                this.weightLabel =
                    this.weightFilter.greater_than + ' >= ' + this.weightLabel;
            }

            if (this.weightFilter.lower_than) {
                this.weightLabel =
                    this.weightLabel + ' <= ' + this.weightFilter.lower_than;
            }
        },
        resetWeightFilter() {
            this.weight = {};
            this.weightLabel = 'Weight';

            this.weightFilter = {
                visible: false,
                lower_than: null,
                greater_than: null,
            };

            this.updateWeight({
                greater_than: null,
                lower_than: null,
            });
        },
    },

    created() {
        if (!this.singleDateFilterEnabled && this.dateDefault) {
            this.updateDate(this.dateDefault);
        } else if (this.singleDateFilterEnabled && this.singleDateDefault) {
            this.updateSingleDate(this.singleDateDefault);
        }
    },

    mounted() {
        axios.get('/markets').then((data) => {
            this.markets = data.data;
        });
        axios.get('/sources').then((data) => {
            this.sources = data.data;
        });
        axios.get('/currencies').then((data) => {
            this.currencies = data.data;
        });
        axios.get('/order-statuses').then((data) => {
            this.orderStatuses = data.data;
        });
        axios
            .get('/date-granularity-options')
            .then((data) => (this.dateGranularityOptions = data.data));

        this.loadCurrencies();
    },

    computed: {
        ...mapGetters({
            storageMarkets: 'filters/markets',
            storageSources: 'filters/sources',
            storageStatuses: 'filters/orderStatuses',
            storageCurrency: 'filters/currency',
            storagePercentile: 'filters/percentile',
            storageWeight: 'filters/weight',
            storageDate: 'filters/date',
            storageSingleDate: 'filters/singleDate',
            storageDateGranularity: 'filters/dateGranularity',
            storageKpiOverviewActiveFields: 'filters/kpiOverviewActiveFields',
            storageSearch: 'filters/search',
        }),
        kpiFieldsFilterable: function() {
            return this.kpiFields.filter((kpiField) => { return kpiField.filterable === true });
        },
        market: {
            get() {
                return this.storageMarkets;
            },
            set(value) {
                this.resetOrdersFilter();
                this.resetProductsFilter();
                this.updateMarkets(value);
            },
        },
        source: {
            get() {
                return this.storageSources;
            },
            set(value) {
                this.updateSources(value);
            },
        },
        orderStatus: {
            get() {
                return this.storageStatuses;
            },
            set(value) {
                this.resetOrdersFilter();
                this.resetProductsFilter();
                this.updateStatus(value);
            },
        },
        currency: {
            get() {
                return this.storageCurrency;
            },
            set(value) {
                this.resetOrdersFilter();
                this.updateCurrency(value);
            },
        },
        percentile: {
            get() {
                return this.storagePercentile;
            },
            set(value) {
                this.resetOrdersFilter();
                this.resetProductsFilter();
                this.updatePercentile(value);
            },
        },
        date: {
            get() {
                return this.storageDate;
            },
            set(value) {
                this.updateDate(value);
            },
        },
        singleDate: {
            get() {
                return this.storageSingleDate;
            },
            set(value) {
                this.updateSingleDate(value);
            },
        },
        dateGranularity: {
            get() {
                return this.storageDateGranularity;
            },
            set(value) {
                this.updateDateGranularity(value);
            },
        },
        kpiOverviewActiveFields: {
            get() {
                return this.storageKpiOverviewActiveFields;
            },
            set(value) {
                this.updateKpiOverviewActiveFields(value);
            },
        },
        search: {
            get() {
                return this.storageSearch;
            },
            set(value) {
                this.updateSearch(value);
            },
        }
    },
};
</script>

<style>
.weight-button > span {
    width: 100%;
    display: flex;
    justify-content: space-around;
}
</style>

<style lang="scss" scoped>
.filters-block {
    .filters-block-element {
        .rates {
            &::after {
                content: ' | ';
            }

            &:last-child {
                &::after {
                    content: none;
                }
            }
        }

        .weight-button {
            width: 120px;
            padding-left: 2px;
            padding-right: 2px;
        }
    }
}

.weight-value {
    margin-bottom: 5px;
}

.weight-confirm {
    float: right;
    margin-left: 5px;
}
</style>
