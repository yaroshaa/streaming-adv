<template>
    <div class="filters-block">
        <el-date-picker
            v-model="date"
            type="daterange"
            :picker-options="pickerOptions"
            range-separator="To"
            start-placeholder="Start date"
            end-placeholder="End date"
            class="filters-block-element"
        >
        </el-date-picker>

        <el-select
            class="filters-block-element"
            v-model="currency"
            placeholder="Currency"
            value-key="id"
            @change="loadCurrencies"
            key="key-currency"
        >
            <!--  eslint-disable-next-line  -->
            <el-option v-for="currency in currencies" :key="currency.id" :label="currency.name" :value="currency">
            </el-option>
        </el-select>

        <div class="filters-block-element filters-block-element--info">
            <div class="rates">
                <template v-if="false">
                    <span v-for="rate in rates" :key="rate.to.code" class="separated">
                        {{ currency.code }}/{{ rate.to.code }} = {{ rate.rate }}
                    </span>
                </template>

                <template v-else>
                    <span class="separated" v-for="(item, period) in ov" :key="period">
                        Rev. {{ getLabelForOverPeriod(period) }} = <MarkedValue :value="item.cmam.percentage_change"></MarkedValue>
                        CMAM {{ getLabelForOverPeriod(period) }} = <MarkedValue :value="item.revenue.percentage_change"></MarkedValue>
                    </span>
                </template>
            </div>
            <div class="analytic">
                <span>
                    Break even = <span class="white">{{ formatNumber(storageData.break_even) }}</span>  Streak = <span class="white">{{ streak.value }} {{ streak.type }} 🔥</span>
                </span>
            </div>
        </div>
    </div>
</template>

<script>
import axios from 'axios';
import moment from 'moment';
import {mapActions, mapGetters} from 'vuex';
import {DatePicker, Option, Select} from 'element-ui';
import MarkedValue from '@/element/components/MarketingOverview/components/MarkedValue';
import {formatNumber} from '@/format/format';

export default {
    name: 'MarketingOverviewFilters',

    components: {
        MarkedValue,
        ElDatePicker: DatePicker,
        ElOption: Option,
        ElSelect: Select,
    },

    data() {
        return {
            currencies: [],
            rates: [],
            pickerOptions: {
                shortcuts: [
                    {
                        text: 'Last week',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'Last month',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'This month',
                        onClick(picker) {
                            const end = new Date();
                            const start = moment().startOf('month').toDate();
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'Last 3 months',
                        onClick(picker) {
                            const end = new Date();
                            const start = new Date();
                            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'Year to date',
                        onClick(picker) {
                            const end = new Date();
                            const start = moment().subtract(1, 'year').toDate();
                            picker.$emit('pick', [start, end]);
                        },
                    },
                    {
                        text: 'This year',
                        onClick(picker) {
                            const end = new Date();
                            const start = moment().startOf('year').toDate();
                            picker.$emit('pick', [start, end]);
                        },
                    },
                ],
            },
        };
    },

    mounted() {
        axios.get('/currencies').then((data) => {
            this.currencies = data.data;
        });
        this.loadCurrencies();
    },

    methods: {
        formatNumber: formatNumber,
        ...mapActions({
            updateCurrency: 'filters/updateCurrency',
            resetOrdersFilter: 'orders/resetFilters',
            updateDate: 'filters/updateDate',
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

        getLabelForOverPeriod(period)
        {
            if (period === 'day') {
                return 'DoD';
            }
            if (period === 'week') {
                return 'WoW';
            }

            return 'MoM';
        }
    },

    computed: {
        ...mapGetters({
            storageCurrency: 'filters/currency',
            storageDate: 'filters/date',
            storageData: 'marketingOverview/data',
        }),

        ov() {
            return this.storageData['over_period'] ?? [];
        },

        streak() {
            return this.storageData['streak'] ?? [];
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

        date: {
            get() {
                return this.storageDate;
            },

            set(value) {
                this.updateDate(value);
            },
        },
    },
};
</script>

<style lang="scss" scoped>
.filters-block {
    .filters-block-element--info {
        display: flex;
        flex-wrap: wrap;
        font-size: 10px;
        line-height: 40px;
        color: #8C8C99;
        .rates {
            margin-right: 15px;
            span.separated {
                &::after {
                    content: ' | ';
                }

                &:last-child {
                    &::after {
                        content: none;
                    }
                }
            }
        }
        span {
            &.white {
                //color: #fff;
                color: #000;
            }

            &.red {
                //color: #c10000;
                color: #909399;
            }

            &.green {
                //color: #00ff0b;
                color: #909399;
            }
        }
    }
}
</style>
